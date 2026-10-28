@extends('layouts.app', ['activeNavigation' => 'members'])
@section('title', 'Edit '.$member->full_name.' · Student Portal')
@section('mobile-title', 'Edit member')
@section('content')
    <header class="page-header"><div><h1>Edit member</h1><p>Update {{ $member->full_name }}’s directory information.</p></div></header>
    <section class="form-panel">
        <form method="POST" action="{{ route('members.update', $member) }}">
            @csrf
            @method('PUT')
            @include('members._form')
        </form>
    </section>
@endsection
