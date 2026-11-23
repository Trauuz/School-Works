<?php

use App\Http\Middleware\EnsureSupabaseSession;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function () {
    $this->withoutMiddleware(EnsureSupabaseSession::class);
});
test('catalog defaults and CRUD are available', function () {
    expect(MembershipCategory::count())->toBe(4)->and(MembershipLevel::orderBy('id')->pluck('fee')->all())->toBe([29900, 59900, 99900, 149900]);
    $this->actingAs(memberManager());
    foreach (['categories', 'levels'] as $kind) {
        $this->get(route($kind.'.index'))->assertOk();
        $this->get(route($kind.'.create'))->assertOk();
    } $this->post(route('categories.store'), ['name' => 'Alumni'])->assertRedirect();
    $category = MembershipCategory::where('name', 'Alumni')->firstOrFail();
    $this->get(route('categories.show', $category))->assertOk();
    $this->put(route('categories.update', $category), ['name' => 'Graduate'])->assertRedirect();
    $this->delete(route('categories.destroy', $category))->assertRedirect();
    $this->post(route('levels.store'), ['name' => 'Annual', 'fee' => '123.45', 'validity_days' => 365, 'benefits' => 'Library access'])->assertRedirect();
    $level = MembershipLevel::where('name', 'Annual')->firstOrFail();
    expect($level->fee)->toBe(12345);
    $this->get(route('levels.edit', $level))->assertOk();
    $this->get(route('levels.show', $level))->assertOk();
    $this->put(route('levels.update', $level), ['name' => 'Annual', 'fee' => '150.00', 'validity_days' => 365])->assertRedirect();
    $this->delete(route('levels.destroy', $level))->assertRedirect();
});
test('referenced catalog entries cannot be deleted and invalid fees are rejected', function () {
    $member = Member::factory()->create();
    $this->actingAs(memberManager())->delete(route('categories.destroy', $member->category))->assertSessionHasErrors('name');
    $this->delete(route('levels.destroy', $member->level))->assertSessionHasErrors('name');
    foreach (['0', '-1', '1.001', '1e3'] as $fee) {
        $this->post(route('levels.store'), ['name' => 'Invalid', 'fee' => $fee, 'validity_days' => 30])->assertSessionHasErrors('fee');
    }
});
