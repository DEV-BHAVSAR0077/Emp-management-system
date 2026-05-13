@php
    $activeTab        = request('tab', '');
    $empTabActive     = $activeTab === 'emp' || request('search') || request('page');
    $rolesTabActive   = $activeTab === 'roles';
    $expensesTabActive = $activeTab === 'expenses' || request('expense_search') || request('expense_page');
    $dashTabActive    = !$empTabActive && !$rolesTabActive && !$expensesTabActive;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
    {{-- Tab Partials --}}
    @include('auth.partials.tab_dashboard')
    @include('auth.partials.tab_employees')
    @include('auth.partials.tab_roles')
    @include('auth.partials.tab_expenses')
@endsection
