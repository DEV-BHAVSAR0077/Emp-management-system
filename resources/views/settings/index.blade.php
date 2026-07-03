@extends('layouts.app')

@section('title', 'Settings — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_settings', ['settingsTabActive' => true])
@endsection
