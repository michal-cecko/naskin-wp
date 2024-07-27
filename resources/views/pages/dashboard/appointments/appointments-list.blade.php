@extends('layouts.dashboard-layout')

@section('title')
    Zoznam všetkých termínov
@endsection

@section('content')
    {!! $filter?->generate() !!}
    @include('parts.dashboard.appointments.tables.dashboard-appointments-list-table')
@endsection