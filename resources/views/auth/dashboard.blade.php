@php
    $activeTab        = request('tab', '');
    $empTabActive     = $activeTab === 'emp' || request('search') || request('page');
    $rolesTabActive   = $activeTab === 'roles';
    $expensesTabActive = $activeTab === 'expenses' || request('expense_search') || request('expense_page');
    $categoriesTabActive = $activeTab === 'categories';
    $agencyVendorsTabActive = $activeTab === 'agency_vendors' || request('av_search') || request('av_page');
    $dashTabActive    = !$empTabActive && !$rolesTabActive && !$expensesTabActive && !$categoriesTabActive && !$agencyVendorsTabActive;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
    {{-- Tab Partials --}}
    @include('auth.partials.tab_dashboard')
    @include('auth.partials.tab_employees')
    @include('auth.partials.tab_roles')
    @include('auth.partials.tab_expenses')
    @include('auth.partials.tab_categories')
    @include('auth.partials.tab_agency_vendors')
@endsection
