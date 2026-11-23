<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Models\User;
use App\Services\SupabaseAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['services.supabase.url' => 'https://auth.example.test', 'services.supabase.key' => 'test-key']);
    Http::preventStrayRequests();
});

function useSupabaseIdentity(User $user): void
{
    Http::fake([
        '*/auth/v1/user' => Http::response(['id' => $user->supabase_id, 'email' => $user->email, 'email_confirmed_at' => now()->toIso8601String()]),
        '*/auth/v1/token*' => Http::response(['access_token' => 'renewed-access', 'refresh_token' => 'renewed-refresh', 'expires_in' => 3600]),
        '*/auth/v1/logout*' => Http::response(null, 204),
    ]);
    test()->actingAs($user)->withSession(['supabase' => Crypt::encryptString(json_encode(['access_token' => 'access', 'refresh_token' => 'refresh', 'expires_at' => time() + 3600], JSON_THROW_ON_ERROR))]);
}

function accountProfile(string $role = 'member'): array
{
    return ['role' => $role, 'first_name' => 'Mara', 'last_name' => 'Santos', 'phone' => '09171234567', 'address_line' => '12 Sample Street', 'city' => 'Manila', 'region' => 'Metro Manila', 'country_code' => 'PH', 'membership_category_id' => MembershipCategory::first()->id, 'membership_level_id' => MembershipLevel::first()->id];
}

test('signup requests confirmation without creating a local membership or saving passwords', function () {
    Http::fake(['*/signup*' => Http::response(['user' => ['id' => fake()->uuid()]], 200)]);
    $this->post(route('register.store'), ['email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertRedirect(route('verification.notice'));
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('members', 0);
    Http::assertSent(fn ($request): bool => $request['email'] === 'new@example.com' && ! isset($request['role']));
});

test('unverified identities cannot establish an application session', function () {
    Http::fake(['*/token*' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']), '*/user' => Http::response(['id' => fake()->uuid(), 'email' => 'new@example.com', 'email_confirmed_at' => null])]);
    $this->post(route('login.store'), ['email' => 'new@example.com', 'password' => 'password'])->assertSessionHasErrors('auth');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('verified callback reconnects existing users and preserves administrator permissions', function () {
    $user = User::factory()->create(['supabase_id' => null, 'profile_completed_at' => null, 'can_manage_members' => true]);
    $identity = fake()->uuid();
    Http::fake(['*/verify' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']), '*/user' => Http::response(['id' => $identity, 'email' => $user->email, 'email_confirmed_at' => now()->toIso8601String()])]);
    $this->get(route('auth.confirm', ['token_hash' => 'valid-hash', 'type' => 'email']))->assertRedirect(route('account.setup'));
    expect($user->fresh()->supabase_id)->toBe($identity)->and($user->fresh()->can_manage_members)->toBeTrue();
    $this->assertDatabaseCount('users', 1);
    expect(session('supabase'))->not->toContain('access_token');
});

test('member setup is retry safe and cannot later promote the account', function () {
    $user = User::factory()->create(['profile_completed_at' => null]);
    useSupabaseIdentity($user);
    $this->get(route('dashboard'))->assertRedirect(route('account.setup'));
    $this->post(route('account.setup.store'), accountProfile())->assertRedirect(route('dashboard'));
    $this->post(route('account.setup.store'), accountProfile('admin'))->assertRedirect(route('dashboard'));
    expect($user->fresh()->can_manage_members)->toBeFalse();
    $this->assertDatabaseCount('members', 1);
    $this->assertDatabaseCount('membership_transactions', 1);
    expect($user->fresh()->member->transactions()->first()->status)->toBe('Pending');
});

test('open administrator setup creates an administrator without a membership application', function () {
    $user = User::factory()->create(['profile_completed_at' => null]);
    useSupabaseIdentity($user);
    $this->post(route('account.setup.store'), accountProfile('admin'))->assertRedirect(route('dashboard'));
    expect($user->fresh()->can_manage_members)->toBeTrue();
    $this->assertDatabaseCount('members', 0);
    $this->assertDatabaseCount('membership_transactions', 0);
});

test('verified email claims an existing unlinked membership without duplicating history', function () {
    $user = User::factory()->create(['profile_completed_at' => null]);
    $member = Member::factory()->create(['email' => strtoupper($user->email)]);
    useSupabaseIdentity($user);
    $this->post(route('account.setup.store'), accountProfile())->assertRedirect(route('dashboard'));
    expect($member->fresh()->user_id)->toBe($user->id);
    $this->assertDatabaseCount('members', 1);
    $this->assertDatabaseCount('membership_transactions', 0);
});

test('members see only their transactions and receipts and cannot use administrative actions', function () {
    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id]);
    $own = MembershipTransaction::factory()->create(['member_id' => $member->id, 'status' => 'Paid']);
    $payment = Payment::factory()->create(['membership_transaction_id' => $own->id]);
    $other = MembershipTransaction::factory()->create();
    $otherPayment = Payment::factory()->create(['membership_transaction_id' => $other->id]);
    useSupabaseIdentity($user);
    $this->get(route('dashboard'))->assertOk()->assertSee('Your membership')->assertDontSee('Collected payments');
    $this->get(route('transactions.show', $own))->assertOk()->assertDontSee('Approve membership')->assertDontSee('Record payment');
    $this->get(route('payments.show', $payment))->assertOk();
    $this->get(route('transactions.show', $other))->assertForbidden();
    $this->get(route('payments.show', $otherPayment))->assertForbidden();
    $this->get(route('members.index'))->assertForbidden();
    $this->patch(route('transactions.approve', $own))->assertForbidden();
    $this->post(route('payments.store'), [])->assertForbidden();
});

test('renewal requests derive ownership and reject duplicates and deactivated memberships', function () {
    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id, 'membership_starts_at' => now()->subDays(10), 'membership_expires_at' => now()->addDays(20)]);
    $other = Member::factory()->create();
    useSupabaseIdentity($user);
    $this->post(route('account.renew'), ['member_id' => $other->id])->assertRedirect();
    expect($member->transactions()->count())->toBe(1)->and($other->transactions()->count())->toBe(0);
    $this->post(route('account.renew'))->assertSessionHasErrors('transaction');
    $member->forceFill(['deactivated_at' => now()])->save();
    $this->post(route('account.renew'))->assertSessionHasErrors('transaction');
});

test('contact editing cannot change role email or membership level', function () {
    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id]);
    useSupabaseIdentity($user);
    $this->put(route('account.profile.update'), [...accountProfile('admin'), 'email' => 'forged@example.com'])->assertSessionHasNoErrors();
    expect($user->fresh()->can_manage_members)->toBeFalse()->and($user->fresh()->email)->toBe($user->email)->and($member->fresh()->phone)->toBe('09171234567');
});

test('expired access tokens are refreshed before a protected request', function () {
    $user = User::factory()->create();
    useSupabaseIdentity($user);
    $this->withSession(['supabase' => Crypt::encryptString(json_encode(['access_token' => 'old', 'refresh_token' => 'refresh', 'expires_at' => time() - 1]))])->get(route('dashboard'))->assertOk();
    Http::assertSent(fn ($request): bool => str_contains($request->url(), 'grant_type=refresh_token'));
    expect(app(SupabaseAuth::class)->tokens(request())['access_token'])->toBe('renewed-access');
});

test('provider outages deny protected access without logging the user out', function () {
    $user = User::factory()->create();
    useSupabaseIdentity($user);
    Http::fake(['*/auth/v1/user' => Http::response([], 503)]);
    $this->get(route('dashboard'))->assertStatus(503);
    $this->assertAuthenticatedAs($user);
});

test('invalid remote sessions are discarded', function () {
    $user = User::factory()->create();
    useSupabaseIdentity($user);
    Http::fake(['*/auth/v1/user' => Http::response([], 401)]);
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('password recovery is isolated from authenticated membership access', function () {
    Http::fake(['*/verify' => Http::response(['access_token' => 'recovery-access', 'refresh_token' => 'refresh']), '*/user' => Http::response(['id' => fake()->uuid()])]);
    $this->get(route('auth.confirm', ['token_hash' => 'valid-hash', 'type' => 'recovery']))->assertRedirect(route('password.reset'));
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->post(route('password.update'), ['password' => 'new-password', 'password_confirmation' => 'new-password'])->assertRedirect(route('login'));
    $this->assertGuest();
    Http::assertSent(fn ($request): bool => $request->method() === 'PUT' && $request['password'] === 'new-password');
});

test('invalid links and expired password recovery sessions are rejected', function () {
    $this->get(route('password.reset'))->assertForbidden();
    Http::fake(['*/verify' => Http::response([], 400)]);
    $this->get(route('auth.confirm', ['token_hash' => 'expired', 'type' => 'email']))->assertSessionHasErrors('auth');
});
