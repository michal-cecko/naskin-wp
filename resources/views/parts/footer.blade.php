<footer>
    <div class="anchor" id="kontakt"></div>
    <div class="img-container">
        <img src="{{main()->assets()->static("images/footer/phone-instagram-mockup.png")}}" alt="" class="phone">
        {!! main()->assets()->svg("icons/other/arrows/icon-painted-arrow.svg") !!}
    </div>
    <div class="contact-part">
        <div class="wrapperis">
            <a href="mailto:{{config("contact.email.value")}}" class="contact-item">
                {!! main()->assets()->svg("icons/contact/icon-mail-outlined.svg") !!}
                <span>{{config("contact.email.text")}}</span>
            </a>
            <a href="tel:{{config("contact.phone.value")}}" class="contact-item">
                {!! main()->assets()->svg("icons/contact/icon-phone-outlined.svg") !!}
                <span>{{config("contact.phone.text")}}</span>
            </a>
            <a href="{{config("socials.facebook.url")}}" class="contact-item" target="_blank">
                {!! main()->assets()->svg("icons/socials/icon-facebook-outlined.svg") !!}
                <span>{{config("socials.facebook.text")}}</span>
            </a>
            <a href="{{config("socials.instagram.url")}}" class="contact-item" target="_blank">
                {!! main()->assets()->svg("icons/socials/icon-instagram-outlined.svg") !!}
                <span>{{config("socials.instagram.text")}}</span>
            </a>
        </div>
    </div>
    <div class="white-part">
        <div class="container">
            <h2 class="heading text-left">Kontaktujte nás</h2>
        </div>
    </div>
    <div class="beige-part bg-light-beige">
        <div class="container">
            <button class="btn btn--brownish_yellow btn--normal" data-js-toggle-reservation-modal>Chcem sa objednať</button>
            <a href="{{site_url()}}/cennik" class="btn btn--brownish_yellow btn--normal">Cenník služieb</a>
            <img src="{{main()->assets()->static("images/logos/logo-golden.png")}}" alt="NASKINcare" class="logo">
        </div>
    </div>

    <div class="bg-light-beige copyright">
        <div class="container">
            <p>2024 &copy; NASKINcare. Všetky práva vyhradené. | Made by <a href="https://synapps.sk">Synapps</a></p>
        </div>
    </div>
</footer>