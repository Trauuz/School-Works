@php
    $editing = isset($member);
@endphp

<div class="form-grid">
    <div class="field">
        <label for="member_number">Member number</label>
        <input id="member_number" name="member_number" value="{{ old('member_number', $member->member_number ?? '') }}" required>
        @error('member_number')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $member->email ?? '') }}" required>
        @error('email')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="first_name">First name</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $member->first_name ?? '') }}" required>
        @error('first_name')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="last_name">Last name</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $member->last_name ?? '') }}" required>
        @error('last_name')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="phone">Phone <span>Optional</span></label>
        <input id="phone" name="phone" value="{{ old('phone', $member->phone ?? '') }}">
        @error('phone')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="category">Category</label>
        <select id="category" name="category" required>
            @foreach (App\MemberCategory::cases() as $category)
                <option value="{{ $category->value }}" @selected(old('category', isset($member) ? $member->category->value : App\MemberCategory::Regular->value) === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
        @error('category')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="level">Level</label>
        <select id="level" name="level" required>
            @foreach (App\MemberLevel::cases() as $level)
                <option value="{{ $level->value }}" @selected(old('level', isset($member) ? $member->level->value : App\MemberLevel::Basic->value) === $level->value)>{{ $level->label() }} · {{ app(App\Services\MembershipPricing::class)->formatted(app(App\Services\MembershipPricing::class)->unitPrice(App\MemberCategory::Regular, $level)) }} regular</option>
            @endforeach
        </select>
        @error('level')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="eligibility_status">Eligibility status</label>
        <select id="eligibility_status" name="eligibility_status" required>
            @foreach (App\EligibilityStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('eligibility_status', isset($member) ? $member->eligibility_status->value : App\EligibilityStatus::Exempt->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <p class="field-help">Student, Senior Citizen, and Corporate discounts activate only after verification.</p>
        @error('eligibility_status')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="corporate_organization">Corporate organization <span>Corporate only</span></label>
        <input id="corporate_organization" name="corporate_organization" value="{{ old('corporate_organization', $member->corporate_organization ?? '') }}">
        @error('corporate_organization')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="seat_count">Seats</label>
        <input type="number" min="1" id="seat_count" name="seat_count" value="{{ old('seat_count', $member->seat_count ?? 1) }}" required>
        <p class="field-help">Corporate memberships require at least 10 active seats.</p>
        @error('seat_count')<p class="field-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="form-actions">
    <a class="secondary-action" href="{{ $editing ? route('members.show', $member) : route('members.index') }}">Cancel</a>
    <button class="primary-action" type="submit">{{ $editing ? 'Save changes' : 'Add member' }}</button>
</div>
