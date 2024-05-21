@extends('layouts.email')

@section('content')


    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Zrušenie rezervácie"])
    {{-- END TITLE --}}



    {{-- START BODY --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    @if($isCancelledByEmployee ?? false)
        Dobrý deň, toto je potvrdenie zrušeného termínu rezerevácie pracovníkom:
    @else
        Dobrý deň. Upozorňujeme, že zákazník zrušil rezerváciu nasledovného termínu
    @endif
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment, 'address' => $address])
    <br>
    @include('parts.emails.appointments.email-customer-details', ['appointment' => $appointment])
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY --}}



@endsection