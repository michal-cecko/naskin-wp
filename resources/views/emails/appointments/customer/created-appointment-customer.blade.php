@extends('layouts.email')

@section('content')



    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Potvrdenie o záväznej rezervácii"])
    {{-- END TITLE --}}



    {{-- START BODY --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    Dobrý deň, ďakujeme za Vašu novú rezerváciu.
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment, 'address' => $address])
    <br>
    @include('parts.emails.appointments.email-customer-details', ['appointment' => $appointment])
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY --}}



    {{-- START CANCEL BTN --}}
    @include('parts.emails.email-horizontal-line')
    @include('parts.emails.appointments.email-cancellation-button', ['appointment' => $appointment])
    {{-- END CANCEL BTN --}}



    {{-- START ICS BTN --}}
    @include('parts.emails.email-horizontal-line')
    @include('parts.emails.appointments.email-ics-button', ['appointment' => $appointment])
    {{-- END ICS BTN --}}


    {{-- START CONTACT US --}}
    @include('parts.emails.email-horizontal-line')
    @include('parts.emails.email-contact-us')
    {{-- END CONTACT US --}}



@endsection