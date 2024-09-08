@extends('layouts.email')

@section('content')



    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Zmena v rezervácii zákazníka"])
    {{-- END TITLE --}}



    {{-- START BODY TEXT --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    Zákazník  {{ $customer['name'] }} si vytvoril novú rezerváciu:
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment, 'address' => $address])
    <br>
    @include('parts.emails.appointments.email-customer-details', ['appointment' => $appointment])
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY TEXT --}}



@endsection