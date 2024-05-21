@if($icsUrl = $appointment->ics_url)

    @include("parts.emails.email-paragraph.email-paragraph", [
        'content' => 'Pridajte si termín do kalendára'
    ])

    @include("parts.emails.email-button", [
        'href' => $icsUrl,
        'text' => 'Pridať do kalendára'
    ])

@endif