@extends('layouts.app')

@section('title', 'Categories — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_categories', ['categoriesTabActive' => true])
@endsection
