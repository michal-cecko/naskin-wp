@extends('layouts.dashboard-layout')

@section('title')
    Zoznam všetkých termínov
@endsection

@section('content')
    @include('parts.dashboard.appointments.tables.dashboard-appointments-list-table')
@endsection