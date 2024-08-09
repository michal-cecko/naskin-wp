@php
    use Theme\Helpers\ThemeHelper;
@endphp

@extends('layouts.base')

@section("dynamic-notifications")
    <?php
    if (isset($_GET['c'])) {
        if ($_GET['c'] == "1") {
            ThemeHelper::showNotification("Vaša rezervácia bola úspešne zrušená.", "success");
        } else {
            ThemeHelper::showNotification("Nastala chyba pri rušení Vašej rezervácie. Kontaktujte nás.", "error");
        }
    }
    ?>
@endsection

@section('content')

    @include("parts.other.akcia")

    <section id="homepageHero">
        <div class="img-container">
            <img src="{{ main()->assets()->static("images/homepage/hero.jpg") }}" alt="Skincare routine image">
        </div>
        <div class="container">
            <div class="content-card">
                <div class="border-box">
                    <img class="hero-logo" src="{{main()->assets()->static("images/logos/logo-golden.png")}}"
                         alt="NASKINcare">
                    <h4 class="heading">Potešte svoje telo aj&nbsp;dušu</h4>
                    <button type="button" class="btn btn--big btn--brownish_yellow" data-js-toggle-reservation-modal>
                        Objednať sa
                    </button>
                </div>
            </div>
            <div class="socials">
                <a class="facebook" href="{{config("socials.facebook.url")}}" target="_blank">
                    {!! main()->assets()->svg("icons/socials/icon-facebook-solid.svg") !!}
                </a>
                <a class="instagram" href="{{config("socials.instagram.url")}}" target="_blank">
                    {!! main()->assets()->svg("icons/socials/icon-instagram-solid.svg") !!}
                </a>
            </div>
        </div>
    </section>


    <section id="about">
        <div class="anchor" id="o-nas"></div>

        <div class="container">
            <div class="text-part">
                <h2 class="heading">Krása a relax pod&nbsp;jednou strechou</h2>

                <div class="dots-divider">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <p class="body-text">V našom kozmetickom salóne vieme, že každá žena je jedinečná a preto sa u nás
                    nechajte rozmaznávať
                    a doprajte svojej pleti dotyk luxusu s celosvetovo uznávanou nemeckou kozmetikou JANSSEN. Ponúkame
                    množstvo rôznorodých kozmetických ošetrení, ktoré Vám po dôkladnom posúdení vašej pleti vyberieme na
                    mieru.Našim cieľom je aby z nášho salónu odchádzali ženy nie len krásne zvonku, ale aj aby si
                    oddýchli, načerpali novú energiu a zharmonizovali vonkajší vzhľad s vnútorným pocitom.</p>

            </div>
            <div class="image-part">
                <img src="{{main()->assets()->static("images/homepage/about.jpg")}}"
                     alt="Tvár, starostlivosť o pleť na tvári">
            </div>
        </div>
    </section>



    <section id="services" class="bg-light-beige">
        <div class="anchor" id="sluzby"></div>

        <div class="container">

            <h2 class="heading">Naše služby</h2>
            <div class="dots-divider">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="service-cards">
                <div class="service-card">
                    <div class="image-container">
                        <img src="{{main()->assets()->static("images/services/masaze.jpg")}}"
                             alt="Masáže, unavené telo">
                    </div>
                    <div class="text-part">
                        <h4 class="heading">Masáže</h4>
                        <p class="body-text">Zjemnite svoj deň. Uvoľňujúce masáže pre telo a myseľ.</p>
                        <a href="{{site_url()}}/sluzby/masaze" class="btn btn--dirty_beige btn--normal">Zobraziť&nbsp;všetko</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="image-container">
                        <img src="{{main()->assets()->static("images/services/depilacia.jpg")}}"
                             alt="Depilácia, odstraňovanie chĺpkov, bezbolestné">
                    </div>
                    <div class="text-part">

                        <h4 class="heading">Depilácia</h4>
                        <p class="body-text">Hladká a jemná pokožka bez nechcených chĺpkov.</p>
                        <a href="{{site_url()}}/sluzby/depilacia" class="btn btn--dirty_beige btn--normal">Zobraziť&nbsp;všetko</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="image-container">
                        <img src="{{main()->assets()->static("images/services/kozmetika.jpg")}}"
                             alt="Kozmetika, krásna a jemná pokožka a pleť">
                    </div>
                    <div class="text-part">

                        <h4 class="heading">Kozmetika</h4>
                        <p class="body-text">V našom kozmetickom salóne vieme, že každá žena je jedinečná.</p>
                        <a href="{{site_url()}}/sluzby/kozmetika" class="btn btn--dirty_beige btn--normal">Zobraziť&nbsp;všetko</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="image-container">
                        <img src="{{main()->assets()->static("images/services/pedikura.jpg")}}" alt="Pedikúra">
                    </div>
                    <div class="text-part">

                        <h4 class="heading">Pedikúra</h4>
                        <p class="body-text">Kroky k dokonalej starostlivosti: Pedikúra pre zdravé a krásne nohy.</p>
                        <a href="{{site_url()}}/sluzby/pedikura" class="btn btn--dirty_beige btn--normal">Zobraziť&nbsp;všetko</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="image-container">
                        <img src="{{main()->assets()->static("images/services/cukrovy-nastrek.jpg")}}"
                             alt="Cukrový nástrek, sfarbenie do hneda, bezpečné opálenie">
                    </div>
                    <div class="text-part">
                        <h4 class="heading">Cukrový nástrek</h4>
                        <p class="body-text">Potrebujete rýchlo a bezpečne zhnednúť ?</p>
                        <a href="{{site_url()}}/cennik/#cukrovy-nastrek"
                           class="btn btn--dirty_beige btn--normal">Cenník</a>
                    </div>
                </div>
                <div class="service-card">
                    <div class="image-container">
                        <img src="{{main()->assets()->static("images/services/lpg.jpg")}}"
                             alt="Neinvazívne nebolestivé ošetrenie spojivového tkaniva">
                    </div>
                    <div class="text-part">
                        <h4 class="heading">LPG</h4>
                        <p class="body-text">Neinvazívne nebolestivé ošetrenie spojivového tkaniva.</p>
                        <a href="{{site_url()}}/cennik/#lpg"
                           class="btn btn--dirty_beige btn--normal">Cenník</a>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section id="map">
        @php
            $trasaLink = 'https://www.google.com/maps/dir/49.3516707,18.7862853/M+park,+Centrum+8,+017+01+Pova%C5%BEsk%C3%A1+Bystrica/@49.2335643,18.4590289,11z/data=!3m1!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x47148bfc81801d01:0x3d79319a1a010a15!2m2!1d18.4449841!2d49.1155763!3e0?entry=ttu';
        @endphp
        <div class="container">
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2611.476952404687!2d18.442409176125306!3d49.11557627136815!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47148bfc81801d01%3A0x3d79319a1a010a15!2sM%20park!5e0!3m2!1ssk!2ssk!4v1715490583584!5m2!1ssk!2ssk"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="info-container">
                <h2 class="heading">Kde nás nájdete</h2>
                <p class="body-text">
                    {!! get_field("address", "options") !!}
                </p>
                <a href="{{$trasaLink}}" class="btn btn--brownish_yellow btn--normal" target="_blank">Ukázať trasu</a>
            </div>
        </div>
    </section>

@endsection
