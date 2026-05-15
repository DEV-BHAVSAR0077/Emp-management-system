@extends('layouts.app')

@section('title', 'Roles — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_roles', ['rolesTabActive' => true])
@endsection
