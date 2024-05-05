@if($cancelUrl = $appointment->cancel_url)

    @include("parts.emails.email-paragraph.email-paragraph", [
        'content' => 'Ak chcete zrušiť Vašu rezerváciu, možete tak urobiť pomocou tlačidla nižšie.'
    ])

    @include("parts.emails.email-button", [
        'href' => $cancelUrl,
        'text' => 'Zrušiť rezerváciu'
    ])

@endif