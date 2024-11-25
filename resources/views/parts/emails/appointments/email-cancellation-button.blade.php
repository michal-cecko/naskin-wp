@if(($cancelUrl = $appointment->cancel_url) && get_field("can_customer_cancel_appointment", "options") ?? true)
    @include("parts.emails.email-paragraph.email-paragraph", [
        'content' => 'Ak chcete zrušiť Vašu rezerváciu, možete tak urobiť pomocou tlačidla nižšie.'
    ])

    @include("parts.emails.email-button", [
        'href' => $cancelUrl,
        'text' => 'Zrušiť rezerváciu'
    ])
@endif