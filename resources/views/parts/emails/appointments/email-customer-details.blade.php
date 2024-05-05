<b>KONTAKTNÉ ÚDAJE</b><br>

<b>Meno a priezvisko</b>: {{ $appointment->customer?->name }}<br>
<b>Telefón</b>: {{ $appointment->customer?->phone }}<br>
<b>Email</b>: {{ $appointment->customer?->email }}<br>

@if(!empty($appointment->note))
    <br>

    <b>Poznámka</b>: {{ $appointment->note }}

    <br>
@endif