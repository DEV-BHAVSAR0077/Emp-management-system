@extends('layouts.app')

@section('title', 'Payments — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_payments', ['paymentsTabActive' => true])
@endsection
