import Commons from './commons.js'
import Cookies from "./cookies.js";

class ReservationForm extends Commons {
    constructor() {
        super();

        this.cookies = new Cookies();

        console.log("Reservation Form JS has been loaded.")

        this._initVueInstance();
    }

    _prepareModalToggling() {
        let _this = this

        this.modal = document.getElementById('reservation');
        if (!this.modal) {
            console.warn("There is no reservation modal on this page.")
            return;
        }

        let buttons = document.querySelectorAll('[data-js-toggle-reservation-modal]');

        if (!buttons.length) {
            console.warn("There are nobuttons that trigger reservation modal on this page.")
            return;
        }

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                _this.toggleModal();
            });
        });
    }

    toggleModal() {
        this.modal.classList.toggle('active');
    }

    _initVueInstance() {
        let _Vue = Vue
        let _thisClass = this

        let reservationNode = document.querySelector("#reservation")
        if (!reservationNode) {
            console.warn("There is no reservation form on this page.")
            return;
        }

        const {createApp} = Vue;
        let app = createApp({
                data() {
                    return {
                        step: 1,

                        chosenCategory: null,
                        chosenServices: {},
                        chosenEmployee: null,
                        chosenTime: {},
                        date: null,

                        customer: {
                            name: "",
                            email: "",
                            phone: "",
                            note: "",
                        },

                        errors: [],

                        saveCustomerToCookies: false,

                        availableDates: {},
                        timeOptions: {},

                        datesLoading: false,
                        sending: false,
                        sent: false,

                        isVisibleOrder: false,

                        container: null,
                        contentContainer: null,
                        cards: {},
                    }
                },
                created() {
                    this.categories = JSON.parse(reservationNode.dataset.categories);
                    console.log(`Reservation Vue component has been created.`)
                    let savedCustomerInfo = _thisClass.cookies.get("saved-info")
                    if (savedCustomerInfo) {
                        savedCustomerInfo = JSON.parse(savedCustomerInfo)
                        this.customer.name = savedCustomerInfo.name
                        this.customer.phone = savedCustomerInfo.phone
                        this.customer.email = savedCustomerInfo.email
                    }
                    this.defaultServices()
                },
                mounted() {
                    _thisClass._prepareModalToggling();
                    if (!this.container) this.container = document.querySelector(".reservation-container")
                    if (!this.contentContainer) this.contentContainer = document.querySelector(".content-container")
                    let cards = this.container?.querySelectorAll(".content") ?? []

                    if (this.container && cards.length) {
                        for (let i = 1; i < cards.length + 1; i++) {
                            this.cards[i] = cards[i - 1];
                        }
                        this.changeHeight(1)
                    }

                    window.resize = function () {
                        this.checkCustomStyle()
                    }
                },
                methods: {
                    changeHeight(step) {
                        this.container.style.height = (this.cards[step].offsetHeight + 160) + "px"
                        console.log(this.cards[step], this.container.style.height)
                    },
                    chooseEmployee(employeeID) {
                        this.isVisibleOrder = true
                        if (employeeID === -1) {
                            this.chosenEmployee = {
                                id: -1,
                            }
                        }
                        this.chosenEmployee = this.availableEmployees.find(employee => employee?.id === employeeID)
                    },
                    toggleService(service) {
                        this.isVisibleOrder = true;
                        if (this.chosenServices[service.id]) {
                            delete this.chosenServices[service.id];
                        } else {
                            this.chosenServices[service.id] = service;
                        }
                    },
                    hasChosenServices() {
                        return Object.keys(this.chosenServices).length > 0;
                    },
                    chooseCategory(category) {
                        this.chosenCategory = category
                    },
                    chooseDate(date) {
                        this.isVisibleOrder = true
                        this.date = date
                        this.timeOptions = this.availableDates[moment(date).format("M")][date]['apps']
                    },
                    chooseTime(time) {
                        this.isVisibleOrder = true
                        this.chosenTime = {...this.timeOptions[time], time: time};
                    },
                    async changeStep(nextStep, returning = false) {
                        if (this.step === 1 && !!this.chosenCategory && returning) {
                            this.defaultServices();
                            return;
                        }

                        if (!this.canContinue(nextStep)) return false;

                        this.step = nextStep
                        if (nextStep !== 5) {
                            this.isVisibleOrder = false
                        }

                        if (nextStep === 2) {
                            let onlyEmployee = this.availableEmployees?.[0] ?? null;
                            if (onlyEmployee && this.availableEmployees.length === 1) {
                                if (!returning) {
                                    this.chooseEmployee(onlyEmployee.id)
                                    await this.changeStep(nextStep + 1)
                                } else {
                                    this.chooseEmployee( -1)
                                    await this.changeStep(nextStep - 1)
                                }

                                return;
                            }
                        }

                        if (nextStep === 3) {
                            if (!returning) {
                                await this.loadDates()
                            } else {
                                this.chosenTime = {}
                            }
                        }

                        this.checkCustomStyle()
                        this.changeHeight(nextStep)
                    },
                    formatSelectedDatetime() {
                        let final = ""
                        final += moment(this.date).format('D. MMMM YYYY')
                        if (!_thisClass.empty(this.chosenTime)) {
                            final += ', ' + moment('2022-03-21T' + this.chosenTime.time + ':00').format('H:mm')
                        }
                        return final
                    },
                    empty(variable) {
                        return _thisClass.empty(variable)
                    },
                    canContinue(nextStep) {
                        //Naspäť možeš vždy
                        if (nextStep < this.step) return true;

                        //Checknuť službu
                        else if (nextStep === 2) {
                            if (!this.hasChosenServices()) return false
                        }

                        //Checknuť employeea
                        if (nextStep === 3) {
                            if (!this.chosenEmployee) return false
                        }

                        //Checknuť dátum
                        else if (nextStep === 4) {
                            if (!this.date) return false
                        }
                        //Checknuť čas
                        else if (nextStep === 5) {
                            if (_thisClass.empty(this.chosenTime)) return false
                        }
                        //Checknuť kontaktné údaje
                        else if (nextStep === 6) {
                            if (!this.customer.name || !this.customer.phone || !this.customer.email) return false
                        }
                        return true
                    },
                    loadDates() {
                        this.availableDates = {}

                        let body = {
                            employee_id: this.chosenEmployee.id,
                            services: Object.keys(this.chosenServices),
                        };

                        this.datesLoading = true;

                        return _thisClass.postFetch("/appointment/available-dates", body)
                            .then(response => response.json())
                            .then(response => {

                                this.datesLoading = false;

                                if (!response.data) {
                                    console.error("Nastala chyba pri získavaní termínov.");
                                    return;
                                }

                                this.availableDates = response.data
                                console.log(this.availableDates)
                            })
                    },

                    sanitizeName() {
                        let removeExisting = this.errors.indexOf("name");
                        if (removeExisting !== -1) {
                            this.errors.splice(removeExisting);
                        }
                        if (!this.customer.name.trim().length) {
                            this.errors.push("name")
                            return false;
                        }
                        return true;
                    },
                    isWeekend(date) {
                        return moment(date).day() === 6 || moment(date).day() === 0
                    },
                    hasChosenCategoryClass() {
                        return !!this.chosenCategory ? 'shown' : ''
                    },
                    activeEmployeeClass(id) {
                        return this.chosenEmployee?.id === id ? 'chosen' : ''
                    },
                    sanitizePhone() {
                        let removeExisting = this.errors.indexOf("phone");
                        if (removeExisting !== -1) {
                            this.errors.splice(removeExisting);
                        }
                        if (this.customer.phone.trim().length < 9) {
                            this.errors.push("phone")
                            return false;
                        }
                        return true;
                    },
                    sanitizeEmail() {
                        let removeExisting = this.errors.indexOf("email");
                        if (removeExisting !== -1) {
                            this.errors.splice(removeExisting);
                        }
                        if (!_thisClass.validateEmail(this.customer.email)) {
                            this.errors.push("email")
                            return false;
                        }
                        return true;
                    },
                    sanitizeInputs() {
                        this.sanitizeName();
                        this.sanitizePhone();
                        this.sanitizeEmail();

                        if (!this.chosenEmployee.id || !this.hasChosenServices() || !this.chosenTime || !this.date) return false;

                        return !this.errors.length;

                    },
                    headerToggler() {
                        if (this.step !== 5) this.isVisibleOrder = !this.isVisibleOrder
                    },
                    getMomentDate(date, format) {
                        return moment(date).format(format)
                    },

                    async makeReservation() {
                        if (!this.sanitizeInputs()) {
                            return false;
                        }

                        let body = {
                            customer: this.customer,
                            employees: this.chosenTime.employees,
                            services: Object.keys(this.chosenServices),
                            date: this.date,
                            time: this.chosenTime.time,
                        }

                        await _thisClass.checkCaptcha().then(function (token) {
                            body.recaptcha = token;
                        }).catch(function (error) {
                            _thisClass.notify("Myslíme si, že ste robot. Obnovte stránku, prosím.", "error")
                            console.error(error)
                            return false;
                        });

                        this.sending = true;

                        let _this = this

                        await _thisClass.postFetch("/appointment/store", body)
                            .then(response => response.json())
                            .then(async (response) => {
                                if (!response.success) {
                                    console.error(response)
                                    return false;
                                }

                                _this.sent = true;

                                let clickIcon = document.querySelector(".check")
                                if (clickIcon) {
                                    clickIcon.click()
                                    console.log("clicked on ", clickIcon)
                                } else {
                                    console.log("click not fo")
                                }

                                await _thisClass.delay(1500);
                                _this.sending = false;

                                _thisClass.toggleModal()
                                await _thisClass.delay(500);
                                if (_this.saveCustomerToCookies) {
                                    _this.saveContactInfoToCookie()
                                }
                                _this.resetReservation()
                            })
                            .catch(error => {
                                _this.sending = false;
                                _thisClass.notify("Nastala chyba pri odosielaní rezervácie. Skúste to prosím znova, alebo nás kontaktujte.")
                                console.error(error)
                            });
                    },
                    saveContactInfoToCookie() {
                        this.customer.note = ""
                        _thisClass.cookies.set("saved-info", JSON.stringify(this.customer), 365)
                    },
                    hasError(name) {
                        return this.step === 5 && this.errors.indexOf(name) !== -1
                    },
                    defaultServices() {
                        this.chosenServices = {}
                        this.chosenCategory = null
                        this.defaultEmployee()
                    },
                    defaultEmployee() {
                        this.chosenEmployee = {
                            id: null,
                            first_name: null,
                        }
                    },
                    defaultCustomer() {
                        this.customer = {
                            name: "",
                            email: "",
                            phone: "",
                            note: "",
                        }
                    },
                    getDayName(num) {
                        return _thisClass.getDayName(num)
                    },
                    getMonthName(num) {
                        return _thisClass.getMonthName(num)
                    },
                    getTimeClass(time) {
                        const now = moment(time, 'HH:mm');
                        const morningEnd = moment('12:00', 'HH:mm');
                        const afternoonEnd = moment('17:00', 'HH:mm');

                        if (now.isBefore(morningEnd)) {
                            return "morning";
                        } else if (now.isBefore(afternoonEnd)) {
                            return "afternoon";
                        } else {
                            return "evening";
                        }
                    },
                    resetReservation() {
                        this.defaultServices()
                        this.defaultCustomer()
                        this.changeStep(1, true)
                        this.isVisibleOrder = false;
                        this.errors = [];
                        this.sent = false;
                        this.sending = false;
                        this.saveCustomerToCookies = false
                        this.availableDates = {}
                        this.chosenTime = {}
                        this.date = null
                        this.timeOptions = {}
                    },
                    checkCustomStyle() {
                        let val = ((this.step - 1) * 20.3);
                        let style = "";
                        if (_thisClass.phoneMQ.matches) {
                            if (!this.container) this.container = document.querySelector(".reservation-container")
                            let width = this.container.offsetWidth
                            val = ((this.step - 1) * (width - 27.2));
                            style = 'translateX(-' + val + 'px)'
                        } else {
                            style = 'translateX(-' + val + 'rem)'
                        }
                        this.contentContainer.style.transform = style;
                        console.log(this.contentContainer.style.transform)
                    },
                    formatDuration(duration) {
                        return _thisClass.humanDurationFromMinutes(duration);
                    }
                },
                computed: {
                    availableServices() {
                        let services = this.chosenCategory?.services?.filter(service => {
                            return service?.employees && service?.employees.length > 0;
                        }) ?? [];
                        console.log("Services: ", services)
                        console.log("Chosen services: ", this.chosenServices)
                        return services;
                    },
                    availableEmployees() {
                        let services = this.chosenServices ?? {};
                        let serviceKeys = Object.keys(services);
                        if (serviceKeys.length === 0) return [];

                        // Check if any service has an empty employee list
                        if (serviceKeys.some(key => (services[key]?.employees ?? []).length === 0)) {
                            return [];
                        }

                        // Initialize the intersection with the first service's employees
                        let intersection = [...(services[serviceKeys[0]]?.employees ?? [])];

                        // Loop through remaining services
                        for (let i = 1; i < serviceKeys.length; i++) {
                            const currentServiceEmployees = services[serviceKeys[i]]?.employees ?? [];
                            intersection = intersection.filter(employee => currentServiceEmployees.some(other => other?.id === employee?.id));
                        }

                        console.log(intersection)

                        return intersection;
                    },
                    totalPrice() {
                        let price = 0;
                        for (let service in this.chosenServices) {
                            price += this.chosenServices[service].price;
                        }
                        return price;
                    },
                    totalDuration() {
                        let duration = 0;
                        for (let service in this.chosenServices) {
                            duration += this.chosenServices[service].duration;
                        }
                        return this.formatDuration(duration);
                    }
                },
                watch: {
                    employee: {
                        handler(newEmployee, oldEmployee) {
                            this.defaultServices();
                        },
                        deep: true
                    },
                    service: {
                        handler(newService, oldService) {
                            this.chosenTime = {}
                            this.date = null
                        },
                        deep: true
                    },
                    'customer.name'(newValue) {
                        this.sanitizeName()
                    },
                    'customer.email'(newValue) {
                        this.sanitizeEmail()
                    },
                    'customer.phone'(newValue) {
                        this.sanitizePhone()
                    }
                },
            }
        );
        app.mount("#reservation");
    }
}

new ReservationForm();

export {}
