@php use Theme\Enum\AppointmentType; @endphp
@extends('layouts.dashboard-layout')

@section('title')
    {{ __(($appointment->type === AppointmentType::RESERVATION ? "Rezervácia" : "Voľno") . " #{$appointment->id}", THEME_DOMAIN) }}
@endsection

@section('content')
    <div id="singleAppointment">

    </div>
@endsection