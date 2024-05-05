

<!doctype html>
<html {!! language_attributes() !!}>
<head>
    {!! wp_head() !!}

    <meta charset="utf-8">

    <!--  TODO: ADD DESC HERE  -->
    <meta name="description" content="">
    <!--  TODO: ADD KEYWORDS HERE  -->
    <meta name="keywords" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel='shortcut icon' type='image/x-icon' href='{{ main()->assets()->static("favicon/favicon.ico")}}'/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ main()->assets()->static("favicon/apple-touch-icon.png")}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ main()->assets()->static("favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ main()->assets()->static("favicon/favicon-16x16.png")}}">
    <link rel="mask-icon" href="{{ main()->assets()->static("favicon/safari-pinned-tab.svg")}}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    @hasSection('head')
        @yield('head')
    @endif
</head>

<body {!! body_class() !!}>

{!! do_action('wp_body_open') !!}
{!! do_action('get_header') !!}

@section('header')
    @include('parts.header')
@show

<div class="app">

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
