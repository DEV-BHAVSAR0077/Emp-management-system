@extends('layouts.app')

@section('title', 'Agency & Vendors — ' . config('app.name'))

@section('content')
    @include('auth.partials.tab_agency_vendors', ['agencyVendorsTabActive' => true])
@endsection
