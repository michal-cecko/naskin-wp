import Commons from './commons.js'

class ServiceTaxonomySingle extends Commons {
    constructor() {
        super();

        console.log("Service Detail JS has been loaded.")

        this._prepareSwiper();
    }

    _prepareSwiper() {
        this.createSwiper(".swiper-services", {
            slidesPerView: 1,
            spaceBetween: 16,
            navigation: {
                prevEl: '.swiper-services-container .swiper-arr--left',
                nextEl: '.swiper-services-container .swiper-arr--right',
            },
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                420: {
                    slidesPerView: 2,
                },

                768: {
                    slidesPerView: 3,
                },

                1024: {
                    slidesPerView: 4,
                },
            }
        })
    }
}

new ServiceTaxonomySingle();

export {}
