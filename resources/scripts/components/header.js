import Commons from './commons.js'

class Header extends Commons {
    constructor() {
        super();

        let _this = this;

        console.log("Header JS has been loaded.")

        document.addEventListener("DOMContentLoaded", function () {
            _this._prepare();
        })
    }

    _prepare() {
        const toggler = document.querySelector('.toggler');
        const header = document.querySelector('header');
        const links = header.querySelectorAll('li');

        toggler.addEventListener('click', function () {
            header.classList.toggle('open');
        })

        links.forEach(link => {
            link.addEventListener('click', function () {
                header.classList.remove('open');
            })
        })
    }
}

new Header();

export {}
