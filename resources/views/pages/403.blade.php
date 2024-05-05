@extends('layouts.base')

@section('content')
    <div class="container" id="403" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 70vh; width: 100vw; padding: 10vh 20vw; text-align: center">
        <h1 style="margin-bottom: 1.5rem">K tomuto nemáte prístup</h1>
        <p style="margin-bottom: 1.8rem">Kliknutím na tlačidlo nižšie sa vrátite na domovskú stránku.</p>
        <a href="<?php echo get_option( "siteurl" ); ?>" class="btn btn-normal btn-primary">Späť na domov</a>
    </div>
@endsection