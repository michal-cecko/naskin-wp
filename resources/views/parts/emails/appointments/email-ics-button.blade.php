@if($icsUrl = $appointment->ics_url)

    @include("parts.emails.email-paragraph.email-paragraph", [
        'content' => 'Pridajte si termín do kalendára'
    ])

    @include("parts.emails.appointments.email-ics-button", [
        'href' => $icsUrl,
        'text' => 'Pridať do kalendára'
    ])

@endif