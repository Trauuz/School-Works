@extends('layouts.app', ['activeNavigation' => 'members'])
@section('title', 'Add member · Student Portal')
@section('mobile-title', 'Add member')
@section('content')
    <header class="page-header"><div><h1>Add member</h1><p>Create a directory record and grant active status.</p></div></header>
    <section class="form-panel">
        <form method="POST" action="{{ route('members.store') }}">
            @csrf
            @include('members._form')
        </form>
    </section>
@endsection
