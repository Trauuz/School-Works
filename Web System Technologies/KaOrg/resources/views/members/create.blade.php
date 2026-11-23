@extends('layouts.app', ['activeNavigation' => 'members'])
@section('title', 'Add member · KaOrg')
@section('mobile-title', 'Add member')
@section('content')
    <header class="page-header"><div><h1>Add member</h1><p>Register a member and automatically record the selected membership fee as paid.</p></div></header>
    <section class="form-panel">
        <form method="POST" action="{{ route('members.store') }}">
            @csrf
            @include('members._form')
        </form>
    </section>
@endsection
