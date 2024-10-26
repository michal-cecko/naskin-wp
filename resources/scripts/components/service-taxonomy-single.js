import Commons from './commons.js'

class ServiceTaxonomySingle extends Commons {
    constructor() {
        super();

        console.log("Service Detail JS has been loaded.")

        this._prepareSwiper();

        this._prepareReadMore();
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
                delay: 8000,
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

    _prepareReadMore() {
        let readMoreBtns = document.querySelectorAll(".open-read-more");
        readMoreBtns.forEach(btn => {
            btn.addEventListener("click", e => {
                e.preventDefault();
                let readMoreDialog = document.querySelector("#readMoreDialog");
                let readMoreTitle = document.querySelector("#readMoreTitle");
                let readMoreContentEl = document.querySelector("#readMoreContent");

                readMoreTitle.innerHTML = btn.dataset.title;
                readMoreContentEl.innerHTML = btn.dataset.content;

                readMoreDialog.classList.add("shown");
            });
        });
    }
}

new ServiceTaxonomySingle();

export {}
