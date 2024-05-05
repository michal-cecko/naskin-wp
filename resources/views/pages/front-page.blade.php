@extends('layouts.base')

@section('content')
    <div class="notification-container">
        <?php if (isset($_GET['c']) && $_GET['c'] == "1") showNotification("Vaša rezervácia bola úspešne zrušená.", "success") ?>
    </div>
@endsection
