@extends('layouts.app', ['activeNavigation' => 'members'])
@inject('membershipPricing', 'App\Services\MembershipPricing')

@section('title', 'Member dashboard · Student Portal')
@section('mobile-title', 'Member dashboard')
@section('mobile-action')<a href="{{ route('members.create') }}">Add</a>@endsection

@section('content')
    <header class="page-header">
        <div>
            <h1>Member dashboard</h1>
            <p>Search, update, and manage every portal member from one place.</p>
        </div>
        <a class="primary-action" href="{{ route('members.create') }}">Add member</a>
    </header>

    <form class="filter-panel" method="GET" action="{{ route('dashboard') }}">
        <div class="filter-search">
            <label for="q">Search members</label>
            <input id="q" name="q" value="{{ $search }}" placeholder="Name, email, or member number">
        </div>
        <div class="filter-control">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                @foreach (App\MemberCategory::cases() as $option)
                    <option value="{{ $option->value }}" @selected($category === $option)>{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-control">
            <label for="level">Level</label>
            <select id="level" name="level">
                <option value="">All levels</option>
                @foreach (App\MemberLevel::cases() as $option)
                    <option value="{{ $option->value }}" @selected($level === $option)>{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-control">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                <option value="all" @selected($status === 'all')>All</option>
            </select>
        </div>
        <button class="filter-submit" type="submit">Apply</button>
        <a class="filter-clear" href="{{ route('dashboard') }}">Clear filters</a>
    </form>

    <section class="member-panel" aria-labelledby="member-list-title">
        <div class="member-panel-heading">
            <h2 id="member-list-title">Member directory</h2>
            <span>Showing {{ $members->firstItem() ?? 0 }}–{{ $members->lastItem() ?? 0 }} of {{ $members->total() }}</span>
        </div>
        @if ($members->isEmpty())
            <div class="empty-state">
                <h3>{{ request()->hasAny(['q', 'category', 'level']) || $status !== 'active' ? 'No members match these filters.' : 'No members yet.' }}</h3>
                <p>{{ request()->hasAny(['q', 'category', 'level']) || $status !== 'active' ? 'Try clearing or changing the current filters.' : 'Add the first person to start the directory.' }}</p>
            </div>
        @else
            <div class="member-table-wrap">
                <table class="member-table">
                    <thead><tr><th>Name</th><th>Membership</th><th>Monthly fee</th><th>Contact</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach ($members as $member)
                        <tr>
                            <td data-label="Name"><a class="member-name" href="{{ route('members.show', $member) }}">{{ $member->full_name }}</a><span>{{ $member->member_number }}</span></td>
                            <td data-label="Membership">{{ $member->category->label() }}<span>{{ $member->level->label() }}</span></td>
                            <td data-label="Monthly fee">{{ $membershipPricing->formatted($membershipPricing->totalPrice($member->category, $member->level, $member->seat_count)) }}</td>
                            <td data-label="Contact"><a href="mailto:{{ $member->email }}">{{ $member->email }}</a><span>{{ $member->phone ?: 'No phone' }}</span></td>
                            <td data-label="Status"><span class="status-pill {{ $member->deactivated_at ? 'is-inactive' : '' }}">{{ $member->deactivated_at ? 'Inactive' : 'Active' }}</span></td>
                            <td class="row-actions">
                                <a class="row-action" href="{{ route('members.show', $member) }}">View</a>
                                <a class="row-action" href="{{ route('members.edit', $member) }}">Edit</a>
                                @if ($member->deactivated_at)
                                    <form method="POST" action="{{ route('members.reactivate', $member) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="row-action" type="submit">Reactivate</button>
                                    </form>
                                @else
                                    <button class="row-action row-action-danger" type="button" data-deactivate-member data-member-name="{{ $member->full_name }}" data-member-action="{{ route('members.destroy', $member) }}">Deactivate</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $members->links() }}</div>
        @endif
    </section>

    <dialog class="confirmation-dialog" id="dashboard-deactivate-member">
        <form method="dialog"><button class="dialog-close" aria-label="Close">×</button></form>
        <h2>Deactivate <span data-dialog-member-name></span>?</h2>
        <p>They will leave the active directory. Their account stays in this dashboard and can be reactivated later.</p>
        <div class="dialog-actions">
            <button class="secondary-action" type="button" data-dialog-close>Cancel</button>
            <form method="POST" data-deactivate-form>
                @csrf
                @method('DELETE')
                <button class="danger-action" type="submit">Deactivate</button>
            </form>
        </div>
    </dialog>
@endsection
