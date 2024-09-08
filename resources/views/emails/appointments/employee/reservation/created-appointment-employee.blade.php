@extends('layouts.email')

@section('content')



    {{-- START TITLE --}}
    @include('parts.emails.email-title', ['title' => "Nová rezervácia zákazníka"])
    {{-- END TITLE --}}



    {{-- START BODY TEXT --}}
    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    @if($isCreatedByEmployee ?? false)
        Pracovník vytvoril zákazníkovi novú nasledovnú rezerváciu:
    @else
        Zákazník  {{ $customer['name'] }} si vytvoril novú rezerváciu:
    @endif
    <br><br>
    @include('parts.emails.appointments.email-appointment-details', ['appointment' => $appointment, 'address' => $address])
    <br>
    @include('parts.emails.appointments.email-customer-details', ['appointment' => $appointment])
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')
    {{-- END BODY TEXT --}}



@endsection