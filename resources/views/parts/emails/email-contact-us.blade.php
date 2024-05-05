@php
    /* TODO: Add websettings email */
    $email = $email ?? get_field("email", "options");
    $color = $color ?? "#127DB3";
@endphp

@if( !empty($email) )

    @include('parts.emails.email-paragraph.email-paragraph-opening-tag')
    Máte nejaké otázky? <a href="mailto:{{ $email }}" target="_blank" style="color: {{$color}};">{{ $email }}</a>
    @include('parts.emails.email-paragraph.email-paragraph-closing-tag')

@endif