@extends('layouts.email')

@section('content')


    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Vytvorené voľno"])
    {{-- END TITLE --}}



    {{-- START BODY --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    Dobrý deň, bolo nahlásené nové voľno pracovníkovi.
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment])
    <br>
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY --}}



@endsection