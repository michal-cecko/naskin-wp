@extends('layouts.dashboard-layout')

@section('title')
    Zoznam všetkých predajov
@endsection

@section('content')

    {!! $filter->generate() !!}

    @include("modules.card.metric-card-wrapper", ['metrics' => $metrics])

    {!! $table->generate() !!}

@endsection