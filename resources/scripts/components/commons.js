export default class Commons {
    constructor() {
        this.apiUrl = PHPVars.api_base;
        this.baseUrl = PHPVars.home_url;
        this.templateDirUri = PHPVars.template_directory_uri;
        this.recaptchaKey = PHPVars.recaptcha_key;
        this.phoneMQ = window.matchMedia('(max-width: 768px)');
    }

    getDateFromTimestamp(timestamp, addTime = false) {
        const date = moment(timestamp);
        return date.format('YYYY-MM-DD');
    }

    checkCaptcha() {
        let _this = this

        return new Promise(function (resolve, reject) {
            grecaptcha.ready(function () {
                grecaptcha.execute(_this.recaptchaKey, {action: 'submit'}).then(function (token) {
                    resolve(token);
                }).catch(function (error) {
                    reject(error)
                });
            });
        });
    }

    notify(text, type = "success") {
        let container = document.getElementById("notifications")
        const div = document.createElement('div');
        div.classList.add('notification', type, "shown");
        div.innerHTML = text;
        container.appendChild(div);
        setTimeout(function () {
            div.classList.remove("shown");
            setTimeout(function () {
                div.remove();
            }, 500);
        }, 3000);
    }

    getCurrentTimestamp() {
        return this.utc(moment());
    }

    postFetch(url, body, headers = {}) {
        if (!url.startsWith('/')) url = '/' + url

        return fetch( `${_thisClass.apiUrl}${url}`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                ...headers
            },
            body: JSON.stringify(body)  // Ensure the body is stringified
        })
    }

    humanDurationFromMinutes(minutes) {
        const duration = moment.duration(minutes, 'minutes');
        const hours = duration.hours();
        const mins = duration.minutes();

        let formattedTime = '';
        if (hours > 0) {
            formattedTime += hours + 'h ';
        }
        if (mins > 0 || (hours === 0 && mins === 0)) {
            formattedTime += mins + 'm';
        }

        return formattedTime.trim();
    }


    utc(datetime) {
        const utcOffset = 60; // UTC+1
        let x = moment(datetime).utc().utcOffset(utcOffset);
        return x.valueOf()
    }

    leadZero(number) {
        if (number < 10) return "0" + number;
        return number
    }

    addParamsToUrl(params, baseUrl) {
        const queryString = Object.entries(params)
            .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
            .join('&');

        return `${baseUrl}?${queryString}`;
    }

    validateEmail(email) {
        const re = /\S+@\S+\.\S+/;
        return re.test(email);
    }

    getDayName(num) {
        if (num === "1") return "Pon";
        if (num === "2") return "Uto";
        if (num === "3") return "Str";
        if (num === "4") return "Štv";
        if (num === "5") return "Pia";
        if (num === "6") return "Sob";
        return "Ned";
    }

    getMonthName(num) {
        if (num === "1") return "Január";
        if (num === "2") return "Február";
        if (num === "3") return "Marec";
        if (num === "4") return "Apríl";
        if (num === "5") return "Máj";
        if (num === "6") return "Jún";
        if (num === "7") return "Júl";
        if (num === "8") return "August";
        if (num === "9") return "September";
        if (num === "10") return "Október";
        if (num === "11") return "November";
        return "December";
    }

    empty(variable) {
        return ([undefined, null, false, 0, "[]", "", "null", "0000-00-00"].includes(variable)) ||
            (Array.isArray(variable) && !variable.length) ||
            (!Array.isArray(variable) && typeof variable === "object" && !Object.keys(variable).length)
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    createSwiper(selector, options) {
        let node = document.querySelector(selector);
        if (!node) return;

        const swiper = new Swiper(selector, options);
    }
}