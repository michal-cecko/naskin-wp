@extends('layouts.base')

@section("dynamic-notifications")
    {{ $view->showDynamicNotifications() }}
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
                @foreach($serviceCategories as $category)
                    <div class="service-card">
                        <div class="image-container">
                            <img src="{{$category['image']}}" alt="{{$category['name']}}">
                        </div>
                        <div class="text-part">
                            <h4 class="heading">{{$category['name']}}</h4>
                            <p class="body-text">{{$category['shortDesc']}}</p>
                            <a href="{{site_url()}}/sluzby/{{$category['slug']}}" class="btn btn--dirty_beige btn--normal">Zobraziť&nbsp;všetko</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



    <section id="map">
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
