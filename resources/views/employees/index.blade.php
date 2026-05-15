@extends('layouts.app')

@section('title', 'Users — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_employees', ['empTabActive' => true])
@endsection
