@php
    $activeTab      = request('tab', '');
    $empTabActive   = $activeTab === 'emp' || request('search') || request('page');
    $rolesTabActive = $activeTab === 'roles';
    $dashTabActive  = !$empTabActive && !$rolesTabActive;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
    {{-- Tab Partials --}}
    @include('auth.partials.tab_dashboard')
    @include('auth.partials.tab_employees')
    @include('auth.partials.tab_roles')
@endsection
