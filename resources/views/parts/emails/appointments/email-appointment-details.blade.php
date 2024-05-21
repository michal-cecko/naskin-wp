<b>DETAILY REZERVÁCIE</b><br>

<b>Dátum a čas</b>: {{ $appointment->start_at->format("j.n.Y - H:i") }} - {{ $appointment->end_at->format("H:i") }}<br>
<b>Pobočka</b>: {{ $address }}<br>
<b>Služby</b>:<br>

@include('parts.emails.appointments.email-services-list', ['services' => $appointment->services])

<b>Pracovník</b>: {{ $appointment->employee?->display_name }}<br>