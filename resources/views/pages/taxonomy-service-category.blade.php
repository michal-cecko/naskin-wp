@extends('layouts.base')

@section('content')
    @include("parts.services.services-slider", ['services' => $services, 'term' => $term])

    <div class="custom-dialog-wrapper" id="readMoreDialog">
        <div class="backdrop"></div>
        <div class="custom-dialog">
            <div class="custom-dialog--header">
                <h5 class="custom-dialog--header--title" id="readMoreTitle"></h5>
                <button type="button" class="close"></button>
            </div>
            <div class="custom-dialog--body">
                <p class="body-text" id="readMoreContent"></p>
            </div>
        </div>
    </div>
@endsection