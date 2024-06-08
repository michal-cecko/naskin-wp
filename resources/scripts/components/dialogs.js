import Commons from './commons.js'

class Dialogs extends Commons {
    constructor() {
        super();

        let _this = this;

        console.log("Dialog JS has been loaded.")

        document.addEventListener("DOMContentLoaded", function () {
            _this._prepareClosing();
        })
    }

    _prepareClosing() {
        let dialogs = document.querySelectorAll(".custom-dialog-wrapper")
        dialogs.forEach(dialog => {
            dialog.style.display = "block"
            let dialogClose = dialog.querySelector(".close")
            dialogClose.addEventListener("click", () => {
                dialog.classList.remove("shown")
            })
        })
    }
}

new Dialogs();

export {}
