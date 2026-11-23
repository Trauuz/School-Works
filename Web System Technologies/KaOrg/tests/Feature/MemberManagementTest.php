<?php

use App\Http\Middleware\EnsureSupabaseSession;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Services\MembershipManager;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);
beforeEach(function () {
    $this->withoutMiddleware(EnsureSupabaseSession::class);
});
function memberManager(): User
{
    return User::factory()->create(['can_manage_members' => true]);
}
function memberPayload(): array
{
    return ['first_name' => 'Mara', 'last_name' => 'Santos', 'email' => 'mara@example.com', 'phone' => '09171234567', 'membership_category_id' => MembershipCategory::first()->id, 'membership_level_id' => MembershipLevel::first()->id];
}
test('membership routes require an administrator', function () {
    $member = Member::factory()->create();
    foreach (['/members', '/members/create', "/members/$member->id", '/categories', '/levels', '/transactions'] as $url) {
        $this->get($url)->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
        $this->app['auth']->forgetGuards();
    }
});
test('registration generates immutable numbers and exactly one paid application', function () {
    $this->actingAs(memberManager())->post(route('members.store'), memberPayload())->assertSessionHasNoErrors()->assertRedirect();
    $member = Member::firstOrFail();
    expect($member->member_number)->toStartWith('MEM-')->and($member->is_active)->toBeFalse()->and($member->transactions()->count())->toBe(1)->and($member->transactions()->first()->status)->toBe('Paid');
    $this->put(route('members.update', $member), [...memberPayload(), 'first_name' => 'Maria'])->assertSessionHasNoErrors();
    expect($member->fresh()->first_name)->toBe('Maria')->and($member->transactions()->count())->toBe(1);
    $this->put(route('members.update', $member), [...memberPayload(), 'member_number' => 'M-override'])->assertSessionHasErrors('member_number');
});
test('member validation rejects missing invalid and duplicate inputs', function () {
    $this->actingAs(memberManager())->post(route('members.store'), [])->assertSessionHasErrors(['first_name', 'last_name', 'email', 'membership_category_id', 'membership_level_id']);
    $member = Member::factory()->create();
    $this->post(route('members.store'), [...memberPayload(), 'email' => 'invalid', 'membership_level_id' => 999])->assertSessionHasErrors(['email', 'membership_level_id']);
    $this->post(route('members.store'), [...memberPayload(), 'email' => $member->email])->assertSessionHasErrors('email');
});
test('member directory filters and history pages work', function () {
    $member = Member::factory()->create(['first_name' => 'Alex', 'last_name' => 'Rivera']);
    $other = Member::factory()->create(['first_name' => 'Other', 'last_name' => 'Person']);
    $this->actingAs(memberManager())->get(route('members.index', ['q' => 'Alex', 'category' => $member->membership_category_id, 'level' => $member->membership_level_id]))->assertOk()->assertSee('Alex Rivera')->assertDontSee('Other Person');
    foreach (['members.show', 'members.edit'] as $route) {
        $this->get(route($route, $member))->assertOk();
    } $this->get(route('members.create'))->assertOk();
    $this->patch(route('members.deactivate', $member))->assertRedirect();
    expect($member->fresh()->deactivated_at)->not->toBeNull();
    $this->assertDatabaseHas('members', ['id' => $member->id]);
});
test('administrator login and logout use existing authentication', function () {
    $admin = memberManager();
    config(['services.supabase.url' => 'https://auth.example.test', 'services.supabase.key' => 'test-key']);
    Http::fake([
        '*/token*' => Http::response(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_in' => 3600]),
        '*/user' => Http::response(['id' => $admin->supabase_id, 'email' => $admin->email, 'email_confirmed_at' => now()->toIso8601String()]),
        '*/logout*' => Http::response(null, 204),
    ]);
    $admin->forceFill(['username' => 'test_admin'])->save();
    $this->post(route('login.store'), ['username' => 'test_admin', 'password' => 'password'])->assertRedirect(route('dashboard'));
    $this->post(route('logout'))->assertRedirect(route('login'));
});

test('generated member numbers are unique and database rejects duplicates', function () {
    $members = Member::factory()->count(2)->create();
    expect($members[0]->member_number)->not->toBe($members[1]->member_number);
    expect(fn () => Member::factory()->create(['member_number' => $members[0]->member_number]))
        ->toThrow(QueryException::class);
});

test('registration records the full selected fee once and updates dashboard collections', function () {
    $this->actingAs(memberManager())->post(route('members.store'), memberPayload())->assertSessionHasNoErrors();
    $member = Member::firstOrFail();
    $transaction = $member->transactions()->firstOrFail();
    expect($transaction->payment->amount)->toBe($member->level->fee)->and($transaction->status)->toBe('Paid');
    $this->assertDatabaseCount('payments', 1);
    $this->get(route('dashboard'))->assertViewHas('collected', $transaction->fee);
    $this->post(route('members.store'), memberPayload())->assertSessionHasErrors('email');
    $this->assertDatabaseCount('payments', 1);
});

test('deleting a paid member removes directory access and keeps financial history and collections', function () {
    $this->actingAs(memberManager())->post(route('members.store'), memberPayload())->assertSessionHasNoErrors();
    $member = Member::firstOrFail();
    $transaction = $member->transactions()->firstOrFail();
    $payment = $transaction->payment;
    $this->delete(route('members.destroy', $member))->assertRedirect(route('members.index'));
    $this->assertSoftDeleted('members', ['id' => $member->id]);
    $this->assertDatabaseHas('membership_transactions', ['id' => $transaction->id, 'status' => 'Paid']);
    $this->assertDatabaseHas('payments', ['id' => $payment->id, 'amount' => $transaction->fee]);
    $this->get(route('members.index'))->assertDontSee($member->member_number);
    $this->get(route('members.show', $member))->assertNotFound();
    $this->delete(route('members.destroy', $member))->assertNotFound();
    $this->get(route('transactions.show', $transaction))->assertOk()->assertSee('(deleted)')->assertDontSee('Approve membership');
    $this->get(route('payments.show', $payment))->assertOk()->assertSee($member->member_number);
    $this->get(route('dashboard'))->assertViewHas('total', 0)->assertViewHas('collected', $transaction->fee);
});

test('deleting a member cancels outstanding unpaid applications', function () {
    $member = Member::factory()->create();
    $transaction = app(MembershipManager::class)->create($member, 'application');
    $this->actingAs(memberManager())->delete(route('members.destroy', $member))->assertRedirect(route('members.index'));
    expect($transaction->fresh()->status)->toBe('Cancelled');
    $this->get(route('dashboard'))->assertViewHas('pending', 0);
});

test('only administrators can delete or deactivate members', function () {
    $member = Member::factory()->create();
    $this->delete(route('members.destroy', $member))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())->delete(route('members.destroy', $member))->assertForbidden();
    $this->patch(route('members.deactivate', $member))->assertForbidden();
    $this->assertNotSoftDeleted('members', ['id' => $member->id]);
});
