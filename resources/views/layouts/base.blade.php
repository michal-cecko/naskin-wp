<!doctype html>
<html {!! language_attributes() !!}>
<head>
    {!! wp_head() !!}

    <meta charset="utf-8">

    <meta name="description"
          content="Salón Naskin je moderný salón krásy v srdci Považskej Bystrice. Ponúkame kozmetiku, masáže, pedikúru a ďalšie služby s profesionálnym prístupom a kvalitnými produktami.">
    <meta name="keywords"
          content="cukrový nástrek PB, cukrový nástrek Považská, cukrový nástrek Považská Bystrica, kozmetika PB, kozmetika Považská, kozmetika Považská Bystrica, laminácia obočia PB, laminácia obočia Považská, laminácia obočia Považská Bystrica, depilácia PB, depilácia Považská, depilácia Považská Bystrica, pedikúra PB, pedikúra Považská, pedikúra Považská Bystrica, gél lak PB, gél lak Považská, gél lak Považská Bystrica, masáže PB, masáže Považská, masáže Považská Bystrica, masáž PB lávové kamene, masáž Považská lávové kamene, masáž Považská Bystrica lávové kamene, tejpovanie PB, tejpovanie Považská, tejpovanie Považská Bystrica, skincare PB, skincare Považská, skincare Považská Bystrica">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel='shortcut icon' type='image/x-icon' href='{{ main()->assets()->static("favicon/favicon.ico")}}'/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ main()->assets()->static("favicon/apple-touch-icon.png")}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ main()->assets()->static("favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ main()->assets()->static("favicon/favicon-16x16.png")}}">
    <link rel="manifest" href="{{ main()->assets()->static("favicon/site.webmanifest")}}">
    <link rel="mask-icon" href="{{ main()->assets()->static("favicon/safari-pinned-tab.svg")}}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">


    @hasSection('head')
        @yield('head')
    @endif


    {{-- Global site tag (gtag.js) - Google Analytics --}}
    @include("parts.scripts.gtag")


    <title>NASKIN | Salón krásy</title>
</head>

<body {!! body_class() !!}>

{!! do_action('wp_body_open') !!}
{!! do_action('get_header') !!}

@section('header')
    @include('parts.header')
@show

<div id="app">

    <div id="customNotifications" class="custom-notifications">
        @section('dynamic-notifications')
            @yield('dynamic-notifications')
        @show
    </div>

    @if(!get_field("hide_reservations", "option"))
        @include("parts.appointments.appointment-form")
    @endif

    @section('content')
        @yield('content')
    @show

</div>

@section('footer')
    @include('parts.footer')
@show

{!! do_action('get_footer') !!}

{!! wp_footer() !!}

@hasSection('scripts')
    @yield('scripts')
@endif
</body>
</html>
