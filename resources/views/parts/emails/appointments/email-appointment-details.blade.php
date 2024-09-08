@php use Theme\Enum\AppointmentType; @endphp

@if($appointment->type === AppointmentType::RESERVATION)

    <b>DETAILY REZERVÁCIE</b><br>

    <b>Dátum a čas</b>: {{ $appointment->start_at->format("j.n.Y - H:i") }} - {{ $appointment->end_at->format("H:i") }}<br>
    <b>Pobočka</b>: {{ $address }}<br>
    <b>Služby</b>:<br>

    @include('parts.emails.appointments.email-services-list', ['services' => $appointment->services])

    <b>Pracovník</b>: {{ $appointment->employee?->display_name }}<br>

@else
    <b>DETAILY VOĽNA</b><br>

    <b>V dátume</b>: {{ $appointment->start_at->format("j.n.Y - H:i") }} až {{ $appointment->end_at->format("j.n.Y - H:i") }}<br>
    <b>Pracovník</b>: {{ $appointment->employee?->display_name }}<br>
    <b>Poznámka</b>: {{ $appointment->note }}
@endif