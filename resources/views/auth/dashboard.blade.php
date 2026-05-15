@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_dashboard', ['dashTabActive' => true])
@endsection
