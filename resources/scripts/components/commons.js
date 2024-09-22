export default class Commons {
    constructor() {
        this.apiUrl = PHPVars.api_base;
        this.baseUrl = PHPVars.home_url;
        this.templateDirUri = PHPVars.template_directory_uri;
        this.recaptchaKey = PHPVars.recaptcha_key;
        this.phoneMQ = window.matchMedia('(max-width: 576px)');
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

    notify(text, type = "success", timeout = 3000) {
        type = "n-" + type
        let container = document.getElementById("customNotifications")
        const div = document.createElement('div');
        div.classList.add('custom-notification', type, "shown");
        div.innerHTML = text;
        container.appendChild(div);
        setTimeout(function () {
            div.classList.remove("shown");
            console.log("removing notification")
            setTimeout(function () {
                div.remove();
            }, 500);
        }, timeout);
    }

    getResponseError(response) {
        let message = response.data?.message ?? null;

        console.log(message, response.data?.errors)

        if(!message && response.data?.errors) {
            message = Object.values(response.data.errors).map(error => error.map(message => message).join("<br>")).join("<br>");
        } else {
            message = message ?? response.data ?? null;
        }

        return message;
    }

    notifyResponseErrors(response, timeout = 4000) {
        let message = this.getResponseError(response);
        if(message) {
            this.notify(message, "error", timeout)
        }
    }

    getCurrentTimestamp() {
        return this.utc(moment());
    }

    async postFetch(url, body, headers = {}) {
        if (!url.startsWith('/')) url = '/' + url

        return fetch( `${this.apiUrl}${url}`, {
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
            formattedTime += hours + 'h';
        }
        if (mins > 0 || (hours === 0 && mins === 0)) {
            formattedTime += (formattedTime.length ? " " : "") + mins + 'min';
        }

        return formattedTime.trim();
    }


    utc(datetime, format = null) {
        const utcOffset = 60; // UTC+1
        let x = format ? moment(datetime, format) : moment(datetime)
        x = x.utc().utcOffset(utcOffset);
        console.log("new UTC from datetime: " + datetime + " = " + x.format("YYYY-MM-DD"))
        return x.valueOf()
    }

    leadZero(number) {
        if (number < 10) return "0" + number;
        return number
    }

    addParamsToUrl(params, baseUrl = null, addToCurrentUrl = false) {
        let urlObj;

        if (baseUrl === null) {
            // Use the current URL if baseUrl is null
            urlObj = new URL(window.location.href);
        } else {
            // Create a new URL object from the base URL
            urlObj = new URL(baseUrl);
        }

        // Get the existing search parameters
        const existingParams = new URLSearchParams(urlObj.search);

        // Add new parameters or update existing ones
        Object.entries(params).forEach(([key, value]) => {
            existingParams.set(key, value);
        });

        // Update the URL's search string
        urlObj.search = existingParams.toString();

        // Get the updated URL as a string
        const updatedUrl = urlObj.toString();

        // If addToCurrentUrl is true, update the browser's address bar without refreshing the page
        if (addToCurrentUrl) {
            window.history.pushState({}, '', updatedUrl);
        }

        // Return the updated URL
        return updatedUrl;
    }

    getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        const paramObj = {};

        params.forEach((value, key) => {
            paramObj[key] = value;
        });

        return paramObj;
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

    getDateFormats() {
        return {
            'input': 'DD/MM/YYYY HH:mm',
            'input_no_time': 'DD/MM/YYYY',
            'payload': 'YYYY-MM-DD HH:mm:ss',
            'payload_no_time': 'YYYY-MM-DD',
            'table': 'YYYY-MM-DDTHH:mm:ss',
            'table_select': 'ddd MMM DD YYYY HH:mm:ss [GMT]ZZ (z)',
            'url_date': 'YYYY-MM-DD',
        }
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    createSwiper(selector, options) {
        let node = document.querySelector(selector);
        if (!node) return;

        const swiper = new Swiper(selector, options);
    }

    usePrimevue(vueInstance) {
        vueInstance.use(PrimeVue.Config,
            {
                zIndex: {
                    overlay: 1000000,
                },
                theme: {
                    preset: PrimeVue.Themes.Aura,
                    options: {
                        darkModeSelector: '.my-app-dark',
                    }
                },
                locale: {
                    dayNames: [
                        "Nedeľa",
                        "Pondelok",
                        "Utorok",
                        "Streda",
                        "Štvrtok",
                        "Piatok",
                        "Sobota",
                    ],
                    dayNamesShort: ["Ne", "Po", "Ut", "St", "Št", "Pi", "So"],
                    dayNamesMin: ["Ne", "Po", "Ut", "St", "Št", "Pi", "So"],
                    firstDayOfWeek: 1,
                    monthNames: [
                        "Január",
                        "Február",
                        "Marec",
                        "Apríl",
                        "Máj",
                        "Jún",
                        "Júl",
                        "August",
                        "September",
                        "Október",
                        "November",
                        "December",
                    ],
                    monthNamesShort: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "Máj",
                        "Jún",
                        "Júl",
                        "Aug",
                        "Sep",
                        "Okt",
                        "Nov",
                        "Dec",
                    ],
                }
            });

        vueInstance.component('p-datepicker', PrimeVue.DatePicker);
        vueInstance.component('p-select', PrimeVue.Select);
        vueInstance.component('p-multiselect', PrimeVue.MultiSelect);
        vueInstance.component('p-input-text', PrimeVue.InputText);
        vueInstance.component('p-dialog', PrimeVue.Dialog);
        vueInstance.component('p-confirmdialog', PrimeVue.ConfirmDialog);
        vueInstance.component('p-number', PrimeVue.InputNumber);
    }
}