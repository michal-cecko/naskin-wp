import Commons from "../commons.js"

class SingleExpense extends Commons {
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
                    categories: {},
                    createEditLoader: false,
                    deleteLoader: false,
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
                this.categories = JSON.parse(singleData.categories)
                this.products = JSON.parse(singleData.products)
                this.delete_redirect = singleData.delete_redirect
            },
            methods: {
                resetForm() {
                    let isEdit = this.form_type === "edit"
                    this.form = {
                        category_id: isEdit ? this.resource.category_id : null,
                        product_id: isEdit ? this.resource.product_id : null,
                        description: isEdit ? this.resource.description : null,
                        price: isEdit ? this.resource.price : null,
                        supplier: isEdit ? this.resource.supplier : null,
                        note: isEdit ? this.resource.note : null,
                        bought_at: isEdit && !!this.resource.bought_at ? moment(this.resource.bought_at, this.dateFormat.payload).format(this.dateFormat.input_no_time) : null,
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

                    if (!this.form.description?.length) {
                        _thisClass.notify("Zadajte popis výdavku.", "error")
                        err = true;
                    }

                    if (!this.form.category_id) {
                        _thisClass.notify("Vyberte kategóriu.", "error")
                        err = true;
                    }

                    return err;
                },
                async saveExpense() {
                    if (this.hasErrors()) return;

                    this.createEditLoader = true;

                    let url = this.form_type === "edit" ? `/expense/edit` : "/expense/store";

                    let body = {...this.form}
                    body.bought_at = this.formatBoughtAt(body.bought_at)

                    console.log(body.bought_at)

                    await _thisClass.postFetch(url, body)
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                this.createEditLoader = false;
                                _thisClass.notifyResponseErrors(response)
                                console.error(response);
                                return false;
                            }

                            if (this.form_type === "create") {
                                _thisClass.notify("Výdavok bol úspešne pridaný.", "success")

                                setTimeout(() => {
                                    this.createEditLoader = false;

                                    window.location.href = _thisClass.addParamsToUrl({id: response.data.id}, null, true);
                                }, 200);
                            } else {
                                _thisClass.notify("Výdavok bol úspešne upravený.", "success")
                                this.createEditLoader = false;
                            }
                        })
                },
                async removeExpense() {
                    if (this.form_type === "create") return;

                    if (!confirm("Naozaj chcete zmazať tento výdavok?")) return;

                    this.deleteLoader = true;

                    await _thisClass.postFetch("/expense/delete", {id: this.resource.id})
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                this.deleteLoader = false;
                                _thisClass.notifyResponseErrors(response)
                                return false;
                            }

                            _thisClass.notify("Výdavok bol úspešne zmazaný.", "success")

                            setTimeout(() => {
                                this.deleteLoader = false;

                                window.location.href = this.delete_redirect;
                            }, 200);
                        })
                },
                formatBoughtAt(dateString) {

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
                }
            },
            computed: {
                buttonSaveText() {
                    return this.createEditLoader ? "Ukladám..." : "Uložiť";
                },
                buttonRemoveText() {
                    return this.deleteLoader ? "Odstraňujem..." : "Odstrániť";
                }
            },
        });

        _thisClass.usePrimevue(app);

        app.mount("#singleExpense");
    }
}

new SingleExpense()

export {}