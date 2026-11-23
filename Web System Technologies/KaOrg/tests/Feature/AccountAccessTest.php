<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Models\User;
use App\Services\SupabaseAuth;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['services.supabase.url' => 'https://auth.example.test', 'services.supabase.key' => 'test-key']);
    Http::preventStrayRequests();
});

function useSupabaseIdentity(User $user, int $identityStatus = 200): void
{
    Http::fake([
        '*/auth/v1/user' => Http::response(['id' => $user->supabase_id, 'email' => $user->email, 'email_confirmed_at' => now()->toIso8601String()], $identityStatus),
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
    $this->post(route('register.store'), ['username' => 'campus_admin', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertRedirect(route('verification.notice'));
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('members', 0);
    Http::assertSent(fn ($request): bool => $request['email'] === 'new@example.com' && ! isset($request['role']));
});

test('unverified identities cannot establish an application session', function () {
    Http::fake(['*/token*' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']), '*/user' => Http::response(['id' => fake()->uuid(), 'email' => 'new@example.com', 'email_confirmed_at' => null])]);
    User::factory()->create(['email' => 'new@example.com', 'username' => 'new_user']);
    $this->post(route('login.store'), ['username' => 'new_user', 'password' => 'password'])->assertSessionHasErrors('auth');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 1);
});

test('verified callback reconnects existing users and preserves administrator permissions', function () {
    $user = User::factory()->create(['supabase_id' => null, 'profile_completed_at' => null, 'can_manage_members' => true]);
    $identity = fake()->uuid();
    Http::fake(['*/verify' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']), '*/user' => Http::response(['id' => $identity, 'email' => $user->email, 'email_confirmed_at' => now()->toIso8601String()])]);
    $this->get(route('auth.confirm', ['token_hash' => 'valid-hash', 'type' => 'email']))->assertRedirect(route('auth.confirm'));
    $this->post(route('auth.confirm'), ['confirm_link' => '1'])->assertRedirect(route('account.setup'));
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
    useSupabaseIdentity($user, 503);
    $this->get(route('dashboard'))->assertStatus(503);
    $this->assertAuthenticatedAs($user);
});

test('invalid remote sessions are discarded', function () {
    $user = User::factory()->create();
    useSupabaseIdentity($user, 401);
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
    $this->get(route('password.reset'))->assertOk()->assertSee('data-auth-callback', false);
    Http::fake(['*/verify' => Http::response([], 400)]);
    $this->get(route('auth.confirm', ['token_hash' => 'expired', 'type' => 'email']))->assertRedirect(route('auth.confirm'));
    $this->post(route('auth.confirm'), ['confirm_link' => '1'])->assertOk()->assertSee('Request a password reset');
});

test('registration defaults to administrator before email verification', function () {
    Http::fake(['*/signup*' => Http::response(['user' => ['id' => fake()->uuid()]], 200)]);
    $this->get(route('register'))->assertOk()->assertDontSee('Account type')->assertDontSee('name="role"', false);
    $this->post(route('register.store'), ['username' => 'campus_admin', 'email' => 'admin@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertRedirect(route('verification.notice'));
    Http::assertSent(fn ($request): bool => $request['data']['registration_role'] === 'admin' && $request['data']['username'] === 'campus_admin');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('registration ignores client supplied account types', function () {
    Http::fake(['*/signup*' => Http::response(['user' => ['id' => fake()->uuid()]], 200)]);
    $this->post(route('register.store'), ['username' => 'campus_admin', 'email' => 'admin@example.com', 'role' => 'member', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertRedirect(route('verification.notice'))->assertSessionHas('pending_signup.role', 'admin');
    Http::assertSent(fn ($request): bool => $request['data']['registration_role'] === 'admin');
});

test('verified administrator registration carries the account type through profile completion', function () {
    $identity = fake()->uuid();
    Http::fake([
        '*/verify' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']),
        '*/user' => Http::response(['id' => $identity, 'email' => 'admin@example.com', 'email_confirmed_at' => now()->toIso8601String(), 'user_metadata' => ['registration_role' => 'admin', 'username' => 'campus_admin']]),
    ]);
    $this->get(route('auth.confirm', ['token_hash' => 'valid-hash', 'type' => 'email']))->assertRedirect(route('auth.confirm'));
    $this->post(route('auth.confirm'), ['confirm_link' => '1'])->assertRedirect(route('account.setup'))->assertSessionHas('registration_role', 'admin');
    expect(User::where('supabase_id', $identity)->firstOrFail()->can_manage_members)->toBeFalse();
    $this->get(route('account.setup'))->assertOk()->assertDontSee('Account type')->assertSee('name="role" value="admin"', false);
    $profile = accountProfile('member');
    unset($profile['membership_category_id'], $profile['membership_level_id']);
    $this->post(route('account.setup.store'), $profile)->assertRedirect(route('dashboard'));
    expect(User::where('supabase_id', $identity)->firstOrFail()->can_manage_members)->toBeTrue();
    $this->assertDatabaseCount('members', 0);
    $admin = User::where('supabase_id', $identity)->firstOrFail();
    expect($admin->username)->toBe('campus_admin')->and($admin->name)->toBe('Mara Santos');
    $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('Hello, campus_admin!');
    $this->actingAs(User::where('supabase_id', $identity)->firstOrFail())->get(route('members.index'))->assertOk();
});

test('signup rejects missing or invalid usernames without contacting the auth provider', function ($username) {
    $this->post(route('register.store'), ['username' => $username, 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertSessionHasErrors('username');
    Http::assertNothingSent();
})->with([null, '', 'ab', 'name with spaces', '<script>alert(1)</script>', str_repeat('a', 31)]);

test('existing accounts retain their name as the greeting when no username is set', function () {
    $user = User::factory()->create(['name' => 'Mara Santos']);
    useSupabaseIdentity($user);
    $this->get(route('dashboard'))->assertOk()->assertSee('Hello, Mara Santos!');
});

test('username login authenticates through the linked Supabase email', function () {
    $user = User::factory()->create(['username' => 'campus_admin']);
    Http::fake([
        '*/token*' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']),
        '*/user' => Http::response(['id' => $user->supabase_id, 'email' => $user->email, 'email_confirmed_at' => now()->toIso8601String()]),
    ]);
    $this->get(route('login'))->assertOk()->assertSee('name="username"', false);
    $this->post(route('login.store'), ['username' => ' CAMPUS_ADMIN ', 'password' => 'password'])->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
    Http::assertSent(fn ($request): bool => str_contains($request->url(), 'token') && $request['email'] === $user->email && ! isset($request['username']));
});

test('unknown usernames cannot sign in', function () {
    $this->post(route('login.store'), ['username' => 'unknown_user', 'password' => 'password'])->assertSessionHasErrors('username');
    $this->assertGuest();
    Http::assertNothingSent();
});

test('signup rejects usernames already used regardless of letter case', function () {
    User::factory()->create(['username' => 'Campus_Admin']);
    $this->post(route('register.store'), ['username' => 'CAMPUS_ADMIN', 'email' => 'other@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertSessionHasErrors('username');
    Http::assertNothingSent();
});

test('signup reserves a username before email confirmation', function () {
    Http::fake(['*/signup*' => Http::response(['user' => ['id' => fake()->uuid()]], 200)]);
    $data = ['username' => 'CAMPUS_ADMIN', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'];
    $this->post(route('register.store'), $data)->assertRedirect(route('verification.notice'));
    $this->assertDatabaseHas('username_reservations', ['username' => 'campus_admin', 'email' => 'new@example.com']);
    $this->post(route('register.store'), [...$data, 'email' => 'other@example.com'])->assertSessionHasErrors('username');
    Http::assertSentCount(1);
});

test('failed remote signup releases the username reservation', function () {
    Http::fake(['*/signup*' => Http::response([], 500)]);
    $this->post(route('register.store'), ['username' => 'campus_admin', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertSessionHasErrors('auth');
    $this->assertDatabaseCount('username_reservations', 0);
});

test('incorrect passwords cannot authenticate a username', function () {
    User::factory()->create(['username' => 'campus_admin']);
    Http::fake(['*/token*' => Http::response([], 400)]);
    $this->post(route('login.store'), ['username' => 'campus_admin', 'password' => 'wrong-password'])->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('the database rejects duplicate usernames regardless of case', function () {
    User::factory()->create(['username' => 'campus_admin']);
    expect(fn () => User::factory()->create(['username' => 'CAMPUS_ADMIN']))->toThrow(UniqueConstraintViolationException::class);
});

test('signup explains confirmation email delivery failures without exposing provider details', function () {
    Http::fake(['*/signup*' => Http::response(['error_code' => 'unexpected_failure', 'msg' => 'Error sending confirmation email'], 500)]);
    $this->from(route('register'))->post(route('register.store'), ['username' => 'campus_admin', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])
        ->assertRedirect(route('register'))->assertSessionHasErrors(['auth' => 'The confirmation email could not be sent. The administrator needs to check Supabase email delivery settings before you try again.']);
    $this->assertDatabaseCount('username_reservations', 0);
    expect(session()->getOldInput())->not->toHaveKeys(['password', 'password_confirmation']);
});

test('signup explains certificate connection failures and releases the username', function () {
    Http::fake(fn () => throw new ConnectionException('cURL error 60: certificate verification failed'));
    $this->from(route('register'))->post(route('register.store'), ['username' => 'campus_admin', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])
        ->assertRedirect(route('register'))->assertSessionHasErrors(['auth' => 'The server could not verify the authentication certificate. Restart the PHP server after configuring its trusted certificates.']);
    $this->assertDatabaseCount('username_reservations', 0);
});

test('email callbacks without server side tokens render the browser callback instead of validation errors', function () {
    $this->get(route('auth.confirm'))->assertOk()->assertSee('data-auth-callback', false)->assertDontSee('The token hash field is required.')->assertHeader('Referrer-Policy', 'no-referrer');
    Http::assertNothingSent();
});

test('browser signup callbacks validate the identity and display a confirmation page', function () {
    $identity = fake()->uuid();
    Http::fake(['*/user' => Http::response(['id' => $identity, 'email' => 'confirmed@example.com', 'email_confirmed_at' => now()->toIso8601String(), 'user_metadata' => ['username' => 'confirmed_user']])]);
    $this->post(route('auth.confirm'), ['access_token' => 'valid-access', 'refresh_token' => 'valid-refresh', 'type' => 'signup'])->assertRedirect(route('account.setup'));
    $this->get(route('verification.confirmed'))->assertRedirect(route('account.setup'));
    $this->get(route('account.setup'))->assertOk()->assertSee('Complete your profile');
    $this->get(route('dashboard'))->assertRedirect(route('account.setup'));
    $this->assertAuthenticated();
});

test('browser recovery callbacks open the new password form without authenticating the user', function () {
    Http::fake(['*/user' => Http::response(['id' => fake()->uuid(), 'email_confirmed_at' => now()->toIso8601String()])]);
    $this->post(route('auth.confirm'), ['access_token' => 'valid-recovery', 'refresh_token' => 'refresh', 'type' => 'recovery'])->assertRedirect(route('password.reset'));
    $this->assertGuest();
    $this->get(route('password.reset'))->assertOk()->assertSee('Choose a new password')->assertSee('name="password_confirmation"', false);
});

test('untrusted callback tokens cannot create a recovery session or leak into old input', function () {
    Http::fake(['*/user' => Http::response([], 401)]);
    $this->post(route('auth.confirm'), ['access_token' => 'forged-access', 'refresh_token' => 'forged-refresh', 'type' => 'recovery'])->assertOk()->assertSee('Request a password reset');
    $this->assertGuest();
    expect(session('password_recovery'))->toBeNull();
    expect(session()->getOldInput())->not->toHaveKeys(['access_token', 'refresh_token']);
});

test('partial callback links have a helpful recovery page', function () {
    $this->get(route('auth.confirm', ['token_hash' => 'missing-type']))->assertOk()->assertSee('Resend confirmation email')->assertDontSee('The type field is required.');
    Http::assertNothingSent();
});

test('password reset hash links are forwarded to recovery verification', function () {
    $this->get(route('password.reset', ['token_hash' => 'recovery-hash']))->assertRedirect(route('auth.confirm', ['token_hash' => 'recovery-hash', 'type' => 'recovery']));
});

test('confirmation success cannot be viewed without a verified account', function () {
    $this->get(route('verification.confirmed'))->assertRedirect(route('login'));
});

test('verification notice preserves the pending email across refreshes and starts a cooldown', function () {
    Http::fake(['*/signup*' => Http::response(['user' => ['id' => fake()->uuid()]], 200)]);
    $this->post(route('register.store'), ['username' => 'new_member', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertRedirect(route('verification.notice'));
    $this->get(route('verification.notice'))->assertOk()->assertSee('new@example.com')->assertDontSee('Current term')->assertHeader('Cache-Control', 'no-store, private');
    $this->get(route('verification.notice'))->assertOk()->assertSee('new@example.com');
    $this->post(route('verification.resend'))->assertSessionHasErrors('auth');
    Http::assertSentCount(1);
});

test('new confirmation links are not consumed on GET', function () {
    $this->get(route('auth.confirm', ['token_hash' => 'scanner-token', 'type' => 'email']))->assertRedirect(route('auth.confirm'));
    $this->get(route('auth.confirm'))->assertOk()->assertSee('Confirm your email');
    Http::assertNothingSent();
});

test('email correction keeps the username and rejects old links', function () {
    Http::fake(['*/signup*' => Http::response(['user' => ['id' => fake()->uuid()]], 200)]);
    $this->post(route('register.store'), ['username' => 'new_member', 'email' => 'typo@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password']);
    $this->travel(61)->seconds();
    $this->post(route('verification.correct'), ['email' => 'correct@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertRedirect(route('verification.notice'));
    $this->assertDatabaseHas('username_reservations', ['username' => 'new_member', 'email' => 'correct@example.com']);
    expect(session('pending_signup.email'))->toBe('correct@example.com');
    Http::fake(['*/user' => Http::response(['id' => fake()->uuid(), 'email' => 'typo@example.com', 'email_confirmed_at' => now()->toIso8601String(), 'user_metadata' => ['username' => 'new_member']])]);
    $this->post(route('auth.confirm'), ['access_token' => 'old-access', 'refresh_token' => 'refresh', 'type' => 'signup'])->assertOk()->assertSee('We');
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});

test('correction cannot reassign a reservation without its ownership token', function () {
    DB::table('username_reservations')->insert(['username' => 'new_member', 'email' => 'old@example.com', 'owner_token_hash' => hash('sha256', 'secret'), 'pending_expires_at' => now()->addHour()]);
    $this->withSession(['pending_signup' => ['username' => 'new_member', 'email' => 'old@example.com', 'role' => 'member', 'expires_at' => time() + 600, 'owner_token' => 'wrong']])->post(route('verification.correct'), ['email' => 'other@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertSessionHasErrors('email');
    Http::assertNothingSent();
});

test('verification status never authenticates a pending browser', function () {
    $user = User::factory()->create(['username' => 'new_member']);
    $this->withSession(['pending_signup' => ['username' => 'new_member', 'email' => $user->email, 'role' => 'member', 'expires_at' => time() + 600]])->get(route('verification.status'))->assertJson(['verified' => true, 'continue_url' => route('login')]);
    $this->assertGuest();
});

test('failed email correction keeps the original reservation and pending context', function () {
    Http::fake(['*/signup*' => Http::sequence()->push(['user' => ['id' => fake()->uuid()]], 200)->push([], 500)]);
    $this->post(route('register.store'), ['username' => 'new_member', 'email' => 'typo@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password']);
    $this->post(route('verification.correct'), ['email' => 'correct@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertSessionHasErrors('email');
    $this->assertDatabaseHas('username_reservations', ['username' => 'new_member', 'email' => 'typo@example.com']);
    expect(session('pending_signup.email'))->toBe('typo@example.com');
});

test('expired pending signup cannot correct its email', function () {
    $this->withSession(['pending_signup' => ['username' => 'new_member', 'email' => 'typo@example.com', 'expires_at' => time() - 1]])->post(route('verification.correct'), ['email' => 'correct@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])->assertForbidden();
    Http::assertNothingSent();
});

test('resend honors provider retry after across browser sessions', function () {
    Http::fake(['*/resend*' => Http::response([], 429, ['Retry-After' => '180'])]);
    $this->from(route('verification.notice'))->post(route('verification.resend'), ['email' => 'retry@example.com'])->assertSessionHasErrors('auth');
    expect(RateLimiter::availableIn('verification:'.hash('sha256', 'retry@example.com')))->toBeGreaterThan(60);
});

test('first login requires profile setup and completed profiles retain dashboard access', function () {
    $user = User::factory()->create(['username' => 'new_admin', 'profile_completed_at' => null]);
    Http::fake([
        '*/token*' => Http::response(['access_token' => 'access', 'refresh_token' => 'refresh']),
        '*/user' => Http::response(['id' => $user->supabase_id, 'email' => $user->email, 'email_confirmed_at' => now()->toIso8601String(), 'user_metadata' => ['username' => 'new_admin', 'registration_role' => 'admin']]),
    ]);
    $this->post(route('login.store'), ['username' => 'new_admin', 'password' => 'password'])->assertRedirect(route('account.setup'));
    $this->get(route('dashboard'))->assertRedirect(route('account.setup'));
    $this->post(route('account.setup.store'), accountProfile('admin'))->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
    $this->actingAs($user->fresh());
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('verification.confirmed'))->assertOk()->assertSee('Open dashboard');
});

test('signup rejects spaces anywhere in a username with a clear message', function (string $username) {
    $this->post(route('register.store'), ['username' => $username, 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'long-password'])
        ->assertSessionHasErrors(['username' => 'The username must contain only letters, numbers, and underscores. Spaces are not allowed.']);
    Http::assertNothingSent();
})->with(['first last', ' username', 'username ', "user\tname", "user\nname", "user\u{00A0}name"]);
