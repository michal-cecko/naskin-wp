import Commons from "../commons.js"

class SingleProductSale extends Commons {
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
                    form_type: "create",
                    resource: {},
                    delete_redirect: {},
                    form: {},
                    reservations: {},
                    createEditLoader: false,
                    deleteLoader: false,
                    products: {},
                    dateFormat: {}
                }
            },
            created() {
                this.dateFormat = _thisClass.getDateFormats();
            },
            mounted() {
                let singleData = document.getElementById('single-data')?.dataset ?? {}
                this.form_type = singleData.form_type
                this.resource = JSON.parse(singleData.resource)
                this.resetForm()
                this.reservations = JSON.parse(singleData.reservations)
                this.products = JSON.parse(singleData.products)
                this.delete_redirect = singleData.delete_redirect
            },
            methods: {
                resetForm() {
                    let isEdit = this.form_type === "edit"
                    this.form = {
                        product_id: isEdit ? this.resource.product_id : null,
                        appointment_id: isEdit ? this.resource.appointment_id : null,
                        price: isEdit ? this.resource.price : null,
                        quantity: isEdit ? this.resource.quantity : null,
                        note: isEdit ? this.resource.note : null,
                        sold_at: isEdit && !!this.resource.sold_at ? moment(this.resource.sold_at, this.dateFormat.payload).format(this.dateFormat.input_no_time) : null,
                    }
                    if (isEdit) {
                        this.form.id = this.resource.id
                    }
                },
                hasErrors() {
                    let err = false;

                    if (!this.form.price) {
                        _thisClass.notify("Zadajte cenu produktu.", "error")
                        err = true;
                    }

                    if (!this.form.sold_at) {
                        _thisClass.notify("Vyberte dátum predania.", "error")
                        err = true;
                    }

                    if (!this.form.quantity) {
                        _thisClass.notify("Zadajte množstvo.", "error")
                        err = true;
                    }

                    if (!this.form.product_id) {
                        _thisClass.notify("Vyberte produkt.", "error")
                        err = true;
                    }

                    return err;
                },
                async saveProductSale() {
                    if (this.hasErrors()) return;

                    this.createEditLoader = true;

                    let url = this.form_type === "edit" ? `/product-sale/edit` : "/product-sale/store";

                    let body = {...this.form}
                    body.sold_at = this.formatSoldAt(body.sold_at)

                    await _thisClass.postFetch(url, body)
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                this.createEditLoader = false;
                                _thisClass.notify(response.data.message, "error")
                                console.error(response);
                                return false;
                            }

                            if (this.form_type === "create") {
                                _thisClass.notify("Predaj produktu bol úspešne pridaný.", "success")

                                setTimeout(() => {
                                    this.createEditLoader = false;

                                    window.location.href = _thisClass.addParamsToUrl({id: response.data.id}, null, true);
                                }, 200);
                            } else {
                                _thisClass.notify("Predaj produktu bol úspešne upravený.", "success")
                                this.createEditLoader = false;
                            }
                        })
                },
                async removeProductSale() {
                    if (this.form_type === "create") return;

                    if (!confirm("Naozaj chcete zmazať tento predaj produktu?")) return;

                    this.deleteLoader = true;

                    await _thisClass.postFetch("/product-sale/delete", {id: this.resource.id})
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                this.deleteLoader = false;
                                _thisClass.notify(response.data.message, "error")
                                return false;
                            }

                            _thisClass.notify("Predaj produktu bol úspešne zmazaný.", "success")

                            setTimeout(() => {
                                this.deleteLoader = false;

                                window.location.href = this.delete_redirect;
                            }, 200);
                        })
                },
                formatSoldAt(dateString) {

                    if(!dateString) return null;
                    // This is the ugliest shit I've ever written, hold pojebane Dates som kokot z nich...

                    if (typeof dateString !== 'string') {
                        return moment(dateString).add(3, "hour").format(this.dateFormat.payload);
                    }

                    // Handle cases for specific formats if necessary
                    if (dateString.includes("T")) {
                        return moment(dateString, this.dateFormat.table).add(3, "hour").format(this.dateFormat.payload);
                    }


                    if (dateString.includes("/")) {
                        return moment(dateString, this.dateFormat.input_no_time).add(3, "hour").format(this.dateFormat.payload);
                    }

                    // If none of the above formats match, return the original date string
                    return dateString;
                },
                setPriceIfEmpty() {
                    if (!this.form.price) {
                        this.form.price = this.products.find(prod => prod.id === this.form.product_id)?.price
                    }
                }
            },
            computed: {
                buttonSaveText() {
                    return this.createEditLoader ? "Ukladám..." : "Uložiť";
                },
                buttonRemoveText() {
                    return this.createEditLoader ? "Odstraňujem..." : "Odstrániť";
                }
            },
        });

        _thisClass.usePrimevue(app);

        app.mount("#singleProductSale");
    }
}

new SingleProductSale()

export {}