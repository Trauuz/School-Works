<?php

use App\EligibilityStatus;
use App\MemberCategory;
use App\MemberLevel;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function memberManager(): User
{
    return User::factory()->create(['can_manage_members' => true]);
}

test('member levels use membership tier labels', function () {
    expect(MemberLevel::Basic->label())->toBe('Basic')
        ->and(MemberLevel::Silver->label())->toBe('Silver')
        ->and(MemberLevel::Gold->label())->toBe('Gold')
        ->and(MemberLevel::Platinum->label())->toBe('Platinum');
});

test('member management is publicly accessible', function () {
    $member = Member::factory()->create();

    $this->get(route('dashboard'))->assertOk()->assertSee($member->full_name);
    $this->get(route('members.create'))->assertOk();
    $this->get(route('members.show', $member))->assertOk();
    $this->get(route('members.edit', $member))->assertOk();
});

test('guests can use member crud actions', function () {
    $payload = [
        'member_number' => 'M-PUBLIC-1',
        'first_name' => 'Public',
        'last_name' => 'Member',
        'email' => 'public.member@example.com',
        'phone' => null,
        'category' => MemberCategory::Regular->value,
        'level' => MemberLevel::Basic->value,
        'eligibility_status' => EligibilityStatus::Exempt->value,
        'corporate_organization' => null,
        'seat_count' => 1,
    ];

    $this->post(route('members.store'), $payload)->assertRedirect();
    $member = Member::query()->where('member_number', 'M-PUBLIC-1')->firstOrFail();

    $this->put(route('members.update', $member), [...$payload, 'first_name' => 'Updated'])->assertRedirect();
    $this->delete(route('members.destroy', $member))->assertRedirect();
    expect($member->fresh()->deactivated_at)->not->toBeNull();

    $this->patch(route('members.reactivate', $member))->assertRedirect();
    expect($member->fresh()->deactivated_at)->toBeNull();
});

test('authorized users can list members', function () {
    $member = Member::factory()->create(['first_name' => 'Alex', 'last_name' => 'Rivera']);

    $this->actingAs(memberManager())
        ->get(route('members.index'))
        ->assertOk()
        ->assertSee('Alex Rivera');
});

test('member profiles link back to the dashboard', function () {
    $member = Member::factory()->create();

    $this->get(route('members.show', $member))
        ->assertOk()
        ->assertSee('Back to dashboard')
        ->assertSee('href="'.route('dashboard').'"', false);
});

test('member dashboard lists all accounts and exposes crud actions', function () {
    $activeMember = Member::factory()->create(['first_name' => 'Active', 'last_name' => 'Member']);
    $inactiveMember = Member::factory()->deactivated()->create(['first_name' => 'Inactive', 'last_name' => 'Member']);

    $this->actingAs(memberManager())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Member dashboard')
        ->assertSee('Active Member')
        ->assertSee('Inactive Member')
        ->assertSee(route('members.create'), false)
        ->assertSee(route('members.show', $activeMember), false)
        ->assertSee(route('members.edit', $activeMember), false)
        ->assertSee(route('members.destroy', $activeMember), false)
        ->assertSee(route('members.reactivate', $inactiveMember), false);
});

test('staff login opens the member dashboard', function () {
    $manager = memberManager();

    $this->post(route('login.store'), [
        'email' => $manager->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));
});

test('members can be created viewed and updated', function () {
    $manager = memberManager();
    $payload = [
        'member_number' => 'M-10001',
        'first_name' => 'Mara',
        'last_name' => 'Santos',
        'email' => 'mara@example.com',
        'phone' => '09171234567',
        'category' => MemberCategory::Student->value,
        'level' => MemberLevel::Gold->value,
        'eligibility_status' => EligibilityStatus::Verified->value,
        'corporate_organization' => null,
        'seat_count' => 1,
    ];

    $response = $this->actingAs($manager)->post(route('members.store'), $payload);
    $member = Member::query()->firstOrFail();
    $response->assertRedirect(route('members.show', $member));

    $this->actingAs($manager)->get(route('members.show', $member))->assertOk()->assertSee('Mara Santos');

    $this->actingAs($manager)->put(route('members.update', $member), [
        ...$payload,
        'first_name' => 'Maria',
    ])->assertRedirect(route('members.show', $member));

    expect($member->fresh()->first_name)->toBe('Maria');
});

test('required invalid and duplicate member fields are rejected', function () {
    $manager = memberManager();
    $existing = Member::factory()->create();

    $this->actingAs($manager)->post(route('members.store'), [])
        ->assertSessionHasErrors(['member_number', 'first_name', 'last_name', 'email', 'category', 'level']);

    $this->actingAs($manager)->post(route('members.store'), [
        'member_number' => $existing->member_number,
        'first_name' => 'Test',
        'last_name' => 'Member',
        'email' => $existing->email,
        'category' => 'unknown',
        'level' => '9',
    ])->assertSessionHasErrors(['member_number', 'email', 'category', 'level']);
});

test('editing a member keeps its own email and member number unique', function () {
    $member = Member::factory()->create();

    $this->actingAs(memberManager())->put(route('members.update', $member), [
        'member_number' => $member->member_number,
        'first_name' => $member->first_name,
        'last_name' => $member->last_name,
        'email' => $member->email,
        'phone' => $member->phone,
        'category' => $member->category->value,
        'level' => $member->level->value,
        'eligibility_status' => $member->eligibility_status->value,
        'corporate_organization' => $member->corporate_organization,
        'seat_count' => $member->seat_count,
    ])->assertSessionHasNoErrors();
});

test('search and combined filters return the correct members', function () {
    Member::factory()->create([
        'first_name' => 'Alex',
        'last_name' => 'Rivera',
        'category' => MemberCategory::Student,
        'level' => MemberLevel::Gold,
    ]);
    Member::factory()->create([
        'first_name' => 'Alexis',
        'last_name' => 'Cruz',
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Gold,
    ]);

    $this->actingAs(memberManager())
        ->get(route('members.index', ['q' => 'Alex', 'category' => 'student', 'level' => 'gold']))
        ->assertOk()
        ->assertSee('Alex Rivera')
        ->assertDontSee('Alexis Cruz');
});

test('filter parameters survive pagination', function () {
    Member::factory()->count(11)->student()->create();

    $this->actingAs(memberManager())
        ->get(route('members.index', ['category' => 'student']))
        ->assertOk()
        ->assertSee('category=student', false)
        ->assertSee('page=2', false);
});

test('members can be deactivated and reactivated without deletion', function () {
    $manager = memberManager();
    $member = Member::factory()->create(['first_name' => 'Nina', 'last_name' => 'Lopez']);

    $this->actingAs($manager)->delete(route('members.destroy', $member))
        ->assertRedirect(route('members.show', $member));

    expect($member->fresh()->deactivated_at)->not->toBeNull();
    $this->assertDatabaseHas('members', ['id' => $member->id]);

    $this->actingAs($manager)->get(route('members.index'))->assertDontSee('Nina Lopez');
    $this->actingAs($manager)->get(route('members.index', ['status' => 'inactive']))->assertSee('Nina Lopez');

    $this->actingAs($manager)->patch(route('members.reactivate', $member))
        ->assertRedirect(route('members.show', $member));

    expect($member->fresh()->deactivated_at)->toBeNull();
});
