@extends('layouts.email')

@section('content')


    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Zrušené voľno"])
    {{-- END TITLE --}}



    {{-- START BODY --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    Dobrý deň, Vaše nahlásené voľno bolo zrušené.
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment])
    <br>
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY --}}



@endsection