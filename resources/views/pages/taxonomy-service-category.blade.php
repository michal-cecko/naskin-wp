@extends('layouts.base')

@section('content')
    @include("parts.services.services-slider", ['services' => $services, 'term' => $term])
@endsection