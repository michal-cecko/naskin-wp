<section id="servicesSlider">
    <div class="container">

        <p class="subheading">ZOZNAM SLUŽIEB KATEGÓRIE</p>
        <h2 class="heading service-category-name">{{$term->name}}</h2>
        @if(!empty($term->description))
            <p class="body-text description">{{$term->description}}</p>
        @endif
        <a href="{{ site_url() }}/cennik/#{{$term->slug}}" class="btn btn--normal btn--brownish_yellow cennik-btn">Cenník služieb</a>

        @if(!empty($services))
            <div class="swiper-services-container">
                <div class="swiper swiper-services">
                    <div class="swiper-wrapper">
                        @foreach($services as $service)
                            <div class="swiper-slide">
                                <a href="{{ site_url() }}/cennik/#{{$term->slug}}" class="service-card">
                                    <div class="image-container">
                                        <img src="{{get_field("service_image", $service->id)}}" alt="{{$service->name}}">
                                    </div>
                                </a>
                                <span>{{$service->title}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="swiper-arr swiper-arr--left">
                    {!! main()->assets()->svg("icons/other/arrows/icon-arrow-circle-outlined.svg") !!}
                </div>
                <div class="swiper-arr swiper-arr--right">
                    {!! main()->assets()->svg("icons/other/arrows/icon-arrow-circle-outlined.svg") !!}
                </div>
            </div>
        @endif
    </div>
</section>