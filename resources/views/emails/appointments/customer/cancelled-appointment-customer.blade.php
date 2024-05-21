@extends('layouts.email')

@section('content')



    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Zrušenie Vašej rezervácie"])
    {{-- END TITLE --}}



    {{-- START BODY --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    @if($isCancelledByEmployee ?? false)
        Dobrý deň, je nám to ľúto, ale nemôžeme vybaviť Vašu rezerváciu. Vaša rezervácia bola zrušená.
    @else
        Dobrý deň, na základe vášho podnetu bola Vaša registrácia zrušená.
    @endif
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment, 'address' => $address])
    <br>
    @include('parts.emails.appointments.email-customer-details', ['appointment' => $appointment])
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY --}}



    {{-- START CONTACT US --}}
    @include('parts.emails.email-horizontal-line')
    @include('parts.emails.email-contact-us')
    {{-- END CONTACT US --}}



@endsection