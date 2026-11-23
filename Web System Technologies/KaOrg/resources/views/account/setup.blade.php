@extends('layouts.app')
@section('title', 'Complete your profile · KaOrg')
@section('content')
<header class="page-header"><div><h1>Complete your profile</h1><p>Your email is verified. Choose how you’ll use the portal.</p></div></header>
<form class="form-panel account-form" method="POST" action="{{ route('account.setup.store') }}">
    @csrf
    @if(session('registration_role') === 'admin')
        <input type="hidden" name="role" value="admin" data-account-role>
    @else
    <div class="field"><label for="role">Account type</label><select id="role" name="role" data-account-role @disabled($user->can_manage_members)>
        <option value="member" @selected(old('role', $user->can_manage_members ? 'admin' : session('registration_role', 'member')) === 'member')>Member</option>
        <option value="admin" @selected(old('role', $user->can_manage_members ? 'admin' : session('registration_role', 'member')) === 'admin')>Administrator</option>
    </select>@if($user->can_manage_members)<input type="hidden" name="role" value="admin">@endif
    <p class="field-help">Administrators can manage all member and payment records. Members can access their own records and request renewals.</p></div>
    @endif
    <div class="form-grid">
        <div class="field"><label for="first_name">First name</label><input id="first_name" name="first_name" autocomplete="given-name" value="{{ old('first_name', $user->member?->first_name) }}" maxlength="100" required></div>
        <div class="field"><label for="last_name">Last name</label><input id="last_name" name="last_name" autocomplete="family-name" value="{{ old('last_name', $user->member?->last_name) }}" maxlength="100" required></div>
        <div class="field"><label for="email">Verified email</label><input id="email" type="email" value="{{ $user->email }}" readonly></div>
        <div class="field"><label for="phone">Phone <span>Optional</span></label><input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $user->phone ?? $user->member?->phone) }}" maxlength="30"></div>
    </div>
    <fieldset data-member-fields @if(session('registration_role') === 'admin') hidden disabled @endif><legend>Membership</legend>
        <p class="field-help">If this email already has a membership record, its category, level, and history are preserved.</p>
        <div class="form-grid">
            <div class="field"><label for="membership_category_id">Category</label><select id="membership_category_id" name="membership_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('membership_category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="field"><label for="membership_level_id">Level</label><select id="membership_level_id" name="membership_level_id">@foreach($levels as $level)<option value="{{ $level->id }}" @selected(old('membership_level_id') == $level->id)>{{ $level->name }} · PHP {{ number_format($level->fee / 100, 2) }} / {{ $level->validity_days }} days</option>@endforeach</select></div>
        </div>
    </fieldset>
    @include('account._address')
    <div class="form-actions"><button class="primary-action">Complete profile</button></div>
</form>
@endsection
