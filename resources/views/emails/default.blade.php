@extends('layouts.email')

@section('content')
    {{-- START BODY --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')

    {!! $content !!}

    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY --}}

    {{-- START CONTACT US --}}
    @include('parts.emails.email-horizontal-line')
    @include('parts.emails.email-contact-us')
    {{-- END CONTACT US --}}
@endsection