import Commons from './components/commons.js'

class General extends Commons {
    constructor() {
        super();

        console.log("General JS has been loaded.")

        this._prepareParametersRemoval()
        this._prepareNotifications()
        this._prepareSmoothScrolling()
        this._prepareAkciaDialog()
    }


    _prepareParametersRemoval() {
        const hasAppointmentActionParam = /c=[0123456789]/.test(window.location.search);
        if (!hasAppointmentActionParam) return;

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        let value = params.get("c");
        params.delete("c");
        url.search = params.toString();

        history.replaceState(null, '', `${url.pathname}?${params.toString()}`);

        if (value === "0") {
            this.notify("Nebolo možné nájsť rezerváciu na zrušenie.", "error", 5000)
        }

        if (value === "1") {
            this.notify("Vaša rezervácia bola zrušená.", "success", 5000)
        }

        if (value === "2") {
            this.notify("Táto rezervácia už bola v minulosti zrušená.", "warning", 5000)
        }

        if (value === "3") {
            this.notify("Rezerváciu už nieje možné zrušiť.", "warning", 5000)
        }

        if (value === "4") {
            this.notify("Rezervácie nieje možné zrušiť.", "warning", 5000)
        }
    }


    _prepareNotifications() {
        let notifications = document.querySelectorAll(".notification")
        if (notifications.length) {
            let x = 2200;
            let i = 0;
            notifications.forEach(notification => {
                setTimeout(function () {
                    notification.remove()
                }, x + (300 * i))
                i++;
            })
        }
    }

    _prepareSmoothScrolling() {
        let anchorlinks = document.querySelectorAll('a[href^="#"]')

        for (let item of anchorlinks) {
            item.addEventListener('click', (e) => {
                let hashval = item.getAttribute('href')
                let target = document.querySelector(hashval)
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                })
                history.pushState(null, null, hashval)
                e.preventDefault()
            })
        }
    }

    _prepareAkciaDialog() {
        let akciaDialog = document.querySelector("#akciaDialog")
        if (!akciaDialog) return;

        setTimeout(() => {
            akciaDialog.classList.add("shown")
        }, 3000)
    }
}

new

General();

export {}
