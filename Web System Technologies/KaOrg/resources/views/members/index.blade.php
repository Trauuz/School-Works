@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>Members</h1>
<a class="primary-action" href="{{ route('members.create') }}">Register member</a>
</header>
<form class="filter-panel" method="GET">
<div class="field">
<label for="q">Search members</label>
<input id="q" name="q" value="{{ request('q') }}" placeholder="Name, email, membership number">
</div>
@foreach (['category' => $categories, 'level' => $levels] as $key => $options)<div class="field">
<label for="{{ $key }}">{{ ucfirst($key) }}</label>
<select id="{{ $key }}" name="{{ $key }}">
<option value="">All</option>@foreach ($options as $option)<option value="{{ $option->id }}" @selected(request($key) == $option->id)>{{ $option->name }}</option>@endforeach</select>
</div>@endforeach
<div class="field">
<label for="status">Status</label>
<select id="status" name="status">
<option value="">All</option>
<option value="active" @selected(request('status')==='active')>Active membership</option>
<option value="inactive" @selected(request('status')==='inactive')>Deactivated</option>
</select>
</div>
<button class="filter-submit">Apply</button>
<a class="filter-clear" href="{{ route('members.index') }}">Clear</a>
</form>
<section class="member-panel">
<div class="member-table-wrap">
<table class="member-table">
<thead>
<tr>
<th>Member</th>
<th>Category</th>
<th>Level</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>@forelse($members as $member)<tr>
<td>
<a href="{{ route('members.show',$member) }}">{{ $member->full_name }}</a>
<span>{{ $member->member_number }}</span>
</td>
<td>{{ $member->category->name }}</td>
<td>{{ $member->level->name }}</td>
<td>{{ $member->deactivated_at ? 'Deactivated' : ($member->is_active ? 'Active' : 'No current membership') }}</td>
<td>
<a href="{{ route('members.edit',$member) }}">Edit</a>
</td>
</tr>@empty<tr>
<td colspan="5">No members found. Register a member or change your filters.</td>
</tr>@endforelse</tbody>
</table>
</div>
<div class="pagination">{{ $members->links() }}</div>
</section>
@endsection
