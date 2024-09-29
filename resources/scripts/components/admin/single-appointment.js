import Commons from "../commons.js"

class SingleAppointment extends Commons {
    constructor() {
        super();

        this.init();
    }

    init() {
        let _thisClass = this

        const {createApp} = Vue;
        const app = createApp({
            data() {
                return {
                    resource: {},
                    delete_redirect: null,
                    buttonLoader: false,
                    notify_customer: false,
                    notify_employee: false,
                    visibleDeleteModal: false,
                }
            },
            mounted() {
                let singleData = document.getElementById('single-data')?.dataset ?? {}
                this.resource = JSON.parse(singleData.resource)
                this.delete_redirect = singleData.delete_redirect
            },
            methods: {
                async removeAppointment() {
                    let data = {
                        id: this.resource.id,
                        notify_customer: !!this.notify_customer,
                        notify_employee: !!this.notify_employee,
                    }

                    this.buttonLoader = true;

                    await _thisClass.postFetch("/appointment/cancel-admin", data)
                        .then(response => response.json())
                        .then(response => {
                            this.buttonLoader = false;

                            if (!response.success) {
                                _thisClass.notifyResponseErrors(response)
                                console.error(response);
                                return false;
                            }

                            setTimeout(() => {
                                this.deleteLoader = false;

                                window.location.href = this.delete_redirect;
                            }, 200);
                        })
                },
            },
        });

        _thisClass.usePrimevue(app);

        app.mount("#singleAppointment");
    }
}

new SingleAppointment()

export {}