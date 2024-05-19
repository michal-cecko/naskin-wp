<header class="bg-light-beige">
    <div class="container">
        <a href="{{home_url()}}" class="logo">
            <img src="{{main()->assets()->static("images/logos/logo-golden.png")}}" alt="NASKINcare">
        </a>
        <div class="toggler-container">
            <div class="toggler">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
        <div class="menu">
            <ul>
                <li><a href="{{site_url()}}/#sluzby">Služby</a></li>
                <li><a href="{{site_url()}}/cennik">Cenník</a></li>
                <li><a href="{{site_url()}}/#kontakt">Kontakt</a></li>
                <li><button type="button" class="btn btn--normal btn--brownish_yellow" data-js-toggle-reservation-modal>Objednať sa</button></li>
            </ul>
        </div>
    </div>
</header>
