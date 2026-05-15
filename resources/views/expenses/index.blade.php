@extends('layouts.app')

@section('title', 'Expenses — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_expenses', ['expensesTabActive' => true])
@endsection
