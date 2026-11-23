<div class="form-grid">
<div class="field">
<label for="first_name">First name</label>
<input id="first_name" name="first_name" type="text" value="{{ old('first_name', $member->first_name ?? '') }}" required>
</div>
<div class="field">
<label for="last_name">Last name</label>
<input id="last_name" name="last_name" type="text" value="{{ old('last_name', $member->last_name ?? '') }}" required>
</div>
<div class="field">
<label for="email">Email</label>
<input id="email" name="email" type="email" value="{{ old('email', $member->email ?? '') }}" required>
</div>
<div class="field">
<label for="phone">Phone (optional)</label>
<input id="phone" name="phone" type="text" value="{{ old('phone', $member->phone ?? '') }}" >
</div>
<div class="field">
<label for="membership_category_id">Category</label>
<select id="membership_category_id" name="membership_category_id" required>@foreach ($categories as $option)<option value="{{ $option->id }}" @selected(old('membership_category_id', $member->membership_category_id ?? '') == $option->id)>{{ $option->name }}</option>@endforeach</select>
</div>
<div class="field">
<label for="membership_level_id">Level</label>
<select id="membership_level_id" name="membership_level_id" required>@foreach ($levels as $option)<option value="{{ $option->id }}" @selected(old('membership_level_id', $member->membership_level_id ?? '') == $option->id)>{{ $option->name }}</option>@endforeach</select>
</div>
</div>
<div class="form-actions">
<a class="secondary-action" href="{{ route('members.index') }}">Cancel</a>
<button class="primary-action">Save member</button>
</div>
