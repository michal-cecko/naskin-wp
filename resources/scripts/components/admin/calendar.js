import Commons from "../commons.js"
import {Calendar} from 'https://cdn.skypack.dev/@fullcalendar/core@6.1.4'
import timeGridPlugin from 'https://cdn.skypack.dev/@fullcalendar/timegrid@6.1.4'
import dayGridPlugin from 'https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.4'
import interactionPlugin from 'https://cdn.skypack.dev/@fullcalendar/interaction@6.1.4'
import skLocale from 'https://cdn.skypack.dev/@fullcalendar/core/locales/sk'

class ReservationCalendar extends Commons {

    constructor() {
        super();

        this.activeClass = "active";
        this.activeDate = this.getDateFromTimestamp(this.getCurrentTimestamp())

        this.init();
        this._prepareAutoRefresh()
    }

    init() {
        let _Vue = Vue
        let _thisClass = this

        const clickOutsideDirective = {
            beforeMount(el, binding, vnode) {
                el.clickOutsideEvent = function (event) {
                    if (!(el === event.target || el.contains(event.target))) {
                        binding.value(event);
                    }
                };
                document.body.addEventListener('click', el.clickOutsideEvent);
            },
            unmounted(el) {
                document.body.removeEventListener('click', el.clickOutsideEvent);
            }
        };

        const {createApp} = Vue;
        const app = createApp({
            data() {
                return {
                    calendar: null,

                    // Data needed to create appointment
                    appointment: {},
                    freeAppColor: "#0b99a6",
                    serviceColors: {},
                    serviceDurations: {},
                    services: {},

                    visibleCreateModal: false,
                    visibleEditModal: false,
                    visibleDeleteModal: false,

                    editingAppointment: null,

                    appointmentToDelete: null,

                    loggedInEmployee: {},
                    chosenEmployeeOnView: null,
                    chosenEmployeeInForms: null,
                    employees: null,
                    employeeServices: [],
                    urlEmployeeKey: "employee",
                    urlDateKey: "date",

                    buttonLoader: false,

                    dateRange: {start: null, end: null},
                    customers: {},
                    customerSearchQuery: "",
                    shownOptions: false,
                    debounceTimer: null,
                    ignoreInputWatchers: false,

                    products: null,

                    notify: false,

                    hasInit: false,

                    dateFormat: {}
                };
            },
            created() {
                this.dateFormat = _thisClass.getDateFormats();
                console.log(`Calendar Vue component has been created.`)
                this.resetAppointmentVariable()
                this.resetAppointmentToDeleteVariable()
            },
            mounted() {
                let pageData = document.getElementById('page-data')?.dataset ?? {}
                this.loggedInEmployee = document.getElementById('logged-user')?.dataset ?? {}

                let employees = JSON.parse(pageData?.employees)
                this.employees = Object.assign({}, employees)

                let products = JSON.parse(pageData?.products)
                this.products = Object.assign({}, products)

                let services = JSON.parse(pageData?.services)
                this.services = Object.assign({}, services)

                this.breaks = JSON.parse(pageData?.breaks)

                let urlParams = _thisClass.getUrlParams()
                let urlEmployee = urlParams[this.urlEmployeeKey] ? this.employees[urlParams.employee] : null
                let urlDate = urlParams[this.urlDateKey] ?? null

                let loggedInId = parseInt(this.loggedInEmployee.id);
                if (this.loggedInEmployee.role === "administrator" || !this.employees[loggedInId]) {
                    this.chosenEmployeeOnView = urlEmployee ? urlEmployee.id : -1;
                    if (this.chosenEmployeeOnView === -1) {
                        const firstKey = Object.keys(this.employees)[0];
                        this.chosenEmployeeInForms = this.employees[firstKey].id;
                    } else {
                        this.chosenEmployeeInForms = this.chosenEmployeeOnView;
                    }
                } else {
                    this.chosenEmployeeOnView = this.chosenEmployeeInForms = loggedInId;
                }

                let params = {}

                if (!urlEmployee) {
                    params[this.urlEmployeeKey] = this.chosenEmployeeOnView
                }

                if (!urlDate) {
                    params[this.urlDateKey] = moment().format(this.dateFormat.url_date)
                }

                _thisClass.addParamsToUrl(params, null, true)

                this.serviceColors = JSON.parse(pageData?.colors)
                this.serviceDurations = JSON.parse(pageData?.durations)


                this.initCalendar();
                this.setServiceList();
            },
            methods: {
                hideOptions() {
                    this.shownOptions = false
                },

                getResources() {
                    let toReturn = [];
                    this.employees.forEach(employee => {
                        toReturn.push({id: employee.id, title: employee.name})
                    })
                    return toReturn
                },

                async initCalendar() {
                    let _thisVue = this

                    let urlParams = _thisClass.getUrlParams()

                    let initialDate = urlParams[_thisVue.urlDateKey] ?? moment().format(_thisVue.dateFormat.url_date);
                    let appointments = await this.fetchAppointments(initialDate, this.chosenEmployeeOnView, "timeGridWeek");

                    const calendarEl = document.getElementById('calendar')

                    const isTouchable = (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0));

                    const longPressDelay = isTouchable ? 1000 : 100;

                    let calendar = new Calendar(calendarEl, {
                        plugins: [timeGridPlugin, dayGridPlugin, interactionPlugin],
                        locale: skLocale,
                        nowIndicator: true,
                        select: function (info) {
                            _thisVue.resetAppointmentVariable();

                            _thisVue.appointment.datetime = {
                                start: moment(info.start, _thisVue.dateFormat.table_select).format(_thisVue.dateFormat.input),
                                end: moment(info.end, _thisVue.dateFormat.table_select).format(_thisVue.dateFormat.input),
                                end_with_break: null,
                            };

                            _thisVue.visibleCreateModal = true;
                        },
                        longPressDelay: longPressDelay,
                        editable: false,
                        selectable: true,
                        selectOverlap: true,
                        eventResizableFromStart: false,
                        initialView: 'timeGridWeek',
                        initialDate: initialDate,
                        nextDayThreshold: "00:00:00",
                        rerenderDelay: 500,
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay'
                        },

                        views: {
                            timeGrid: {
                                dayHeaderFormat: {
                                    weekday: 'long',
                                    month: 'numeric',
                                    day: 'numeric',
                                    omitCommas: true
                                },
                                slotDuration: '00:05:00',
                                slotMinTime: '03:00:00',
                                slotMaxTime: '23:00:00',
                            },
                        },

                        slotLabelFormat: {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        },

                        dayHeaderContent: function (arg) {
                            const date = new Date(arg.date);
                            const day = date.toLocaleString('sk-SK', {weekday: 'long'});
                            const month = date.toLocaleString('sk-SK', {month: 'numeric'});
                            const dayNum = date.toLocaleString('sk-SK', {day: 'numeric'});
                            return `${day}, ${dayNum}${month}`;
                        },

                        eventDidMount: function (info) {
                            let html = '<span class="delete-button">&times;</span>';
                            let eventEl = info.el;
                            let props = info.event.extendedProps;

                            let deleteButtonEl = eventEl.querySelector('.delete-button');
                            if (!deleteButtonEl && _thisVue.onlyLoggedInEmployeeCondition(info.event)) {
                                eventEl.insertAdjacentHTML('beforeend', html);
                                deleteButtonEl = eventEl.querySelector('.delete-button');
                                deleteButtonEl.addEventListener('click', function () {
                                    _thisVue.resetAppointmentToDeleteVariable()
                                    _thisVue.appointmentToDelete = info.event
                                    _thisVue.visibleDeleteModal = true;
                                });
                            }

                            if (props.break && _thisVue.onlyLoggedInEmployeeCondition(info.event)) {
                                html = '<div class="break" style="height: ' + (19.5 * (props.break / 5)) + 'px">' + props.break + 'min</div>';
                                let breakEl = eventEl.querySelector('.breakEl');
                                if (!breakEl) {
                                    eventEl.insertAdjacentHTML('beforeend', html);
                                }
                            }

                            if (!_thisVue.onlyLoggedInEmployeeCondition(info.event)) {
                                html = '<div class="block">' + _thisVue.employees[`${props.employeeID}`]?.name + ' má službu</div>';
                                let blockingEl = eventEl.querySelector('.breakEl');
                                if (!blockingEl) {
                                    eventEl.insertAdjacentHTML('beforeend', html);
                                }
                            }

                        },

                        eventContent: function (info) {
                            let event = info.event;
                            let view = info.view.type;
                            let props = event.extendedProps;

                            let html = '<div class="event-content-container ' + view + '">' +
                                '<div class="time">' +
                                _thisVue.formatEventTime(event) + '</div>';
                            html += '<div class="title">' + event.title + '</div>';

                            let empl = _thisVue.employees[props.employeeID]?.name ?? props.employee ?? null
                            if (_thisVue.chosenEmployeeOnView === -1 || props.employeeID !== _thisVue.chosenEmployeeOnView) {
                                if (empl) {
                                    if (props.type !== "free") {
                                        html += '<div class="employee">Vybaví: ' + empl + '</div>';
                                    } else {
                                        html += '<div class="employee">' + empl + '</div>';
                                    }
                                }
                            }

                            if (props.type === "reservation") {
                                let isObject = props.services[0]?.id ?? false
                                html += '<div class="service">'
                                if (isObject) {
                                    html += props.services.map((item) => item?.title).join(", ")
                                } else {
                                    html += props.services.map((serviceID) => _thisVue.services[serviceID]?.title).join(", ")
                                }
                                html += '</div>';
                                if (view === "timeGridWeek") {
                                }
                                //day
                                else {
                                    html += '<h3 class="heading">Kontaktné údaje</h3>'
                                    html += '<div class="customer">';
                                    html += '<div class="email">' + props.customer.email + '</div>'
                                    html += '<div class="phone">' + props.customer.phone + '</div>'
                                    html += '</div>';
                                }
                            }

                            if (props.note) {
                                let note = props.note
                                if (note.length > 30 && view === "timeGridWeek") {
                                    note = note.substring(0, 30) + "...";
                                }
                                html += '<div class="note">' + note + '</div>';
                            }
                            html += '</div>';

                            return {
                                html: html,
                            };
                        },
                        eventClick: function (info) {
                            if (info.jsEvent.target.classList.contains('delete-button')) return false;

                            if (!_thisVue.onlyLoggedInEmployeeCondition(info.event)) return false;

                            _thisVue.editingAppointment = info.event;
                            _thisVue.loadEditModal(info.event);
                            _thisVue.visibleEditModal = true;
                        },
                        eventDrop: false,
                        eventResize: false,
                        events: appointments,

                        viewDidMount: async function (view) {
                            let start = calendar.view.currentStart
                            let end = calendar.view.currentEnd

                            _thisVue.dateRange.start = start
                            _thisVue.dateRange.end = end
                        }
                    })
                    calendar.on('datesSet', async function (info) {
                        if (!_thisVue.hasInit) return;

                        let start = calendar.view.currentStart
                        let end = calendar.view.currentEnd

                        _thisVue.dateRange.start = start
                        _thisVue.dateRange.end = end

                        _thisClass.addParamsToUrl({[_thisVue.urlDateKey]: moment(start, _thisVue.dateFormat.table_select).format(_thisVue.dateFormat.url_date)}, null, true)

                        let appointments = await _thisVue.fetchAppointments(moment(start).add(1, "hour").format(_thisVue.dateFormat.url_date), _thisVue.chosenEmployeeOnView, calendar.view.type)
                        _thisVue.exchangeAppointmentsOnView(appointments)
                    });
                    this.calendar = calendar;
                    calendar.render()

                    _thisVue.dateRange.start = calendar.view.currentStart
                    _thisVue.dateRange.end = calendar.view.currentEnd

                    this.hasInit = true;
                },

                async createAppointment() {

                    if (!this.checkErrors()) return;

                    let body = {
                        notify: !!this.notify,
                        employeeID: parseInt(this.chosenEmployeeInForms),
                        date: {
                            start: moment(this.appointment.datetime.start, this.dateFormat.input).format(this.dateFormat.payload),
                            end: moment(this.appointment.datetime.end, this.dateFormat.input).format(this.dateFormat.payload),
                        },
                        type: this.appointment.type === "free" ? "free" : "reservation",
                        note: this.appointment.note
                    }

                    if (this.appointment.type !== "free") {
                        body.source = this.appointment.source
                        body.services = this.appointment.services
                        body.payments = this.appointment.payments
                        body.productSales = this.appointment.productSales
                        body.customer = this.appointment.customer
                    }

                    this.buttonLoader = true;

                    await _thisClass.postFetch("/appointment/store-admin", body)
                        .then(response => response.json())
                        .then(response => {

                            this.buttonLoader = false;

                            if (!response.success) {
                                _thisClass.notify(response.data.message, "error")
                                console.error(response);
                                return false;
                            }

                            let ev = {
                                title: this.appointment.type === "free" ? "Voľno" : this.appointment.customer.name,
                                start: moment(this.appointment.datetime.start, this.dateFormat.input).format(this.dateFormat.table),
                                end: moment(this.appointment.datetime.end, this.dateFormat.input).format(this.dateFormat.table),
                                extendedProps: {
                                    id: response.data.id,
                                    source: this.appointment.source,
                                    type: this.appointment.type,
                                    break: this.appointmentBreakInMinutes,
                                    end_with_break: this.getEndWithBreak(moment(this.appointment.datetime.end, this.dateFormat.input), this.appointmentBreakInMinutes),
                                    note: this.appointment.note ?? null,
                                    services: this.appointment.services.map(id => this.services[id]),
                                    payments: this.appointment.payments,
                                    productSales: this.appointment.productSales,
                                    employee: this.employees?.[this.chosenEmployeeInForms]?.name,
                                    employeeID: this.chosenEmployeeInForms,
                                    customer: this.appointment.customer,
                                },
                                color: this.getActiveColor(this.employees?.[this.chosenEmployeeInForms], this.appointment.type, Object.values(this.appointment.services ?? {})[0]),
                                textColor: '#ffffff'
                            }

                            this.calendar.addEvent(ev);

                            this.visibleCreateModal = false;
                            this.resetModals()
                        })


                    return true;
                },

                onlyLoggedInEmployeeCondition(event) {
                    return +event.extendedProps.employeeID === +this.loggedInEmployee.id || ['administrator', 'together_employee', 'manager'].includes(this.loggedInEmployee.role);
                },

                checkErrors() {
                    if (!this.appointment.datetime.end) {
                        _thisClass.notify("Zadajte koniec služby.", "error")
                        return false;
                    }

                    const startTime = moment(this.appointment.datetime.start, this.dateFormat.input);
                    const endTime = moment(this.appointment.datetime.end, this.dateFormat.input);

                    console.log("comparing", startTime, endTime)
                    if (!endTime.isAfter(startTime)) {
                        _thisClass.notify("Koniec služby musí byť neskôr ako začiatok!", "error")
                        return false;
                    }

                    if (!this.appointment.datetime.start) {
                        _thisClass.notify("Zadajte začiatok služby.", "error")
                        return false;
                    }

                    if (this.appointment.type !== "free") {
                        if (this.appointment.services.length === 0) {
                            _thisClass.notify("Vyberte aspoň jednu službu.", "error")
                            return false;
                        }

                        if (this.appointment.payments.length !== 0) {
                            let err = false;

                            this.appointment.payments.forEach(payment => {
                                if (!payment.amount) {
                                    _thisClass.notify("Nezadali ste sumu platby.", "error")
                                    err = true;
                                }
                                if (!payment.type) {
                                    _thisClass.notify("Nezadali ste typ platby.", "error")
                                    err = true;
                                }
                            })

                            if (err) return false;
                        }

                        if (this.appointment.productSales.length !== 0) {
                            let err = false;

                            this.appointment.productSales.forEach(sale => {
                                if (!sale.price?.toString()?.length) {
                                    _thisClass.notify("Nezadali ste sumu produktu.", "error")
                                    err = true;
                                }
                                if (!sale.product_id) {
                                    _thisClass.notify("Nevybrali ste produkt.", "error")
                                    err = true;
                                }
                                if (!sale.quantity) {
                                    _thisClass.notify("Zadajte množstvo produktu viac ako 0.", "error")
                                    err = true;
                                }
                            })

                            if (err) return false;
                        }

                        if (!this.appointment.customer?.id && !this.appointment.customer?.name) {
                            _thisClass.notify("Vyberte zákazníka alebo zadajte údaje nového.", "error")
                            return false;
                        }
                    }

                    return true;
                },

                async editAppointment() {
                    if (!this.editingAppointment) return;

                    if (!this.checkErrors()) return;

                    let data = {
                        employeeID: parseInt(this.chosenEmployeeInForms),
                        id: this.editingAppointment.extendedProps.id,
                        notify: !!this.notify,
                        type: this.appointment.type,
                        date: {
                            start: moment(this.appointment.datetime.start, this.dateFormat.input).format(this.dateFormat.payload),
                            end: moment(this.appointment.datetime.end, this.dateFormat.input).format(this.dateFormat.payload),
                        },
                        note: this.appointment.note
                    }

                    if (this.appointment.type !== "free") {
                        data.source = this.appointment.source
                        data.services = this.appointment.services
                        data.payments = this.appointment.payments
                        data.productSales = this.appointment.productSales
                    }

                    this.buttonLoader = true;

                    await _thisClass.postFetch("/appointment/edit-admin", data)
                        .then(response => response.json())
                        .then(response => {

                            this.buttonLoader = false;

                            if (!response.success) {
                                _thisClass.notify(response.data.message, "error")
                                console.error(response);
                                return false;
                            }

                            this.editingAppointment.remove();
                            this.editingAppointment = null;

                            let event = {
                                title: this.appointment.type === "free" ? "Voľno" : this.appointment.customer.name,
                                start: moment(this.appointment.datetime.start, this.dateFormat.input).format(this.dateFormat.table),
                                end: moment(this.appointment.datetime.end, this.dateFormat.input).format(this.dateFormat.table),
                                extendedProps: {
                                    id: response.data.id,
                                    source: this.appointment.source,
                                    type: this.appointment.type,
                                    break: this.appointmentBreakInMinutes,
                                    end_with_break: this.getEndWithBreak(moment(this.appointment.datetime.end, this.dateFormat.input), this.appointmentBreakInMinutes),
                                    note: this.appointment.note ?? null,
                                    services: this.appointment.services.map(id => this.services[id]),
                                    payments: this.appointment.payments,
                                    productSales: this.appointment.productSales,
                                    employee: this.employees?.[this.chosenEmployeeInForms]?.name,
                                    employeeID: this.chosenEmployeeInForms,
                                    customer: this.appointment.customer,
                                },
                                color: this.getActiveColor(this.employees?.[this.chosenEmployeeInForms], this.appointment.type, Object.values(this.appointment.services ?? {})[0]),
                                textColor: '#ffffff'
                            }

                            console.log("edited : ", event, this.appointment)

                            this.calendar.addEvent(event);

                            this.visibleEditModal = false;
                            this.resetModals()

                            return true;
                        })
                },

                async removeAppointment() {
                    let data = {
                        id: this.appointmentToDelete.extendedProps.id,
                        notify: !!this.notify,
                    }

                    this.buttonLoader = true;

                    await _thisClass.postFetch("/appointment/cancel-admin", data)
                        .then(response => response.json())
                        .then(response => {
                            this.buttonLoader = false;

                            if (!response.success) {
                                _thisClass.notify(response.data.message, "error")
                                console.error(response);
                                return false;
                            }

                            this.appointmentToDelete.remove()
                            this.visibleDeleteModal = false;
                            this.notify = false;
                        })
                },

                fetchAppointments(date, employeeID, dateRange = "timeGridWeek") {
                    let params = {
                        employeeID: employeeID,
                        date: date,
                        dateRange: dateRange,
                    };

                    return fetch(_thisClass.addParamsToUrl(params, `${_thisClass.apiUrl}/appointment/table`))
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                _thisClass.notify(response.data.message, "error")
                                console.error(response);
                                return false;
                            }

                            let appointments = [];
                            for (const [ID, appointment] of Object.entries(response.data.appointments)) {
                                let title = "Voľno"
                                if (appointment.type !== "free") {
                                    title = appointment.customer.name
                                }

                                console.log(this.employees?.[appointment.employeeID], appointment.employeeID, appointment)

                                appointments.push({
                                    title: title,
                                    start: moment(appointment.datetime.from, this.dateFormat.payload).format(this.dateFormat.table),
                                    end: moment(appointment.datetime.to, this.dateFormat.payload).format(this.dateFormat.table),
                                    extendedProps: {
                                        id: ID,
                                        source: appointment.source,
                                        type: appointment.type,
                                        note: appointment.note,
                                        break: appointment.break,
                                        end_with_break: appointment.break ? moment(appointment.datetime.to_with_break, this.dateFormat.payload) : null,
                                        services: appointment.services?.map((appService => this.services[appService.service_id])) ?? {},
                                        payments: appointment.payments ?? [],
                                        productSales: appointment.productSales ?? [],
                                        employee: appointment.employee,
                                        employeeID: appointment.employeeID,
                                        customer: appointment.customer ?? null,
                                    },
                                    color: this.getActiveColor(this.employees?.[appointment.employeeID], appointment.type, Object.values(appointment.services ?? {})[0]),
                                    textColor: '#ffffff'
                                })
                            }

                            console.log(appointments)

                            return appointments;
                        })
                },

                debouncedFetchCustomers() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        this.fetchCustomers()
                    }, 500);
                },

                fetchCustomers() {
                    const searchTerm = this.customerSearchQuery;
                    if (!searchTerm) {
                        this.customers = {};
                        return;
                    }
                    let params = {
                        search: searchTerm,
                    };
                    return fetch(_thisClass.addParamsToUrl(params, `${_thisClass.apiUrl}/customers/search`))
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                _thisClass.notify(response.data.message, "error")
                                console.error(response);
                                return false;
                            }

                            this.customers = response.data;
                            this.shownOptions = true
                        })
                },

                chooseCustomer(customer) {
                    this.shownOptions = false
                    this.customerSearchQuery = customer.name
                    this.appointment.customer.id = customer.id
                    this.appointment.customer.name = customer.name
                    this.appointment.customer.email = customer.email
                    this.appointment.customer.phone = customer.phone
                },

                formatCustomerOption(customer) {
                    let toReturn = "<b>" + customer.name + "</b>";
                    if (customer.email) {
                        toReturn += " | <span>" + customer.email + "</span>";
                    }
                    if (customer.phone) {
                        toReturn += " | <span>" + customer.phone + "</span>";
                    }
                    return toReturn
                },

                async changeCurrentEmployeeView(id) {
                    this.chosenEmployeeOnView = this.chosenEmployeeInForms = id

                    let now = moment(this.dateRange.start).add(1, "hour").format(this.dateFormat.url_date)
                    let params = {[this.urlEmployeeKey]: this.chosenEmployeeOnView}
                    _thisClass.addParamsToUrl(params, null, true)

                    let appointments = await this.fetchAppointments(now, id, this.calendar.view.type);
                    this.exchangeAppointmentsOnView(appointments)
                },

                exchangeAppointmentsOnView(appointments) {
                    let _thisVue = this
                    this.removeAppsFromViewOnly()
                    console.log("new apps: ", appointments)
                    appointments.forEach(function (event) {
                        console.log(event)
                        _thisVue.calendar.addEvent(event);
                    });
                },

                removeAppsFromViewOnly() {
                    let oldAppointments = this.calendar.getEvents();
                    oldAppointments.forEach(function (event) {
                        event.remove();
                    });
                },

                loadEditModal(appToEdit) {
                    console.log("loading ed modal", appToEdit, this.employeeServices);
                    this.resetAppointmentVariable()
                    let type = appToEdit.extendedProps.type

                    this.ignoreInputWatchers = true;
                    setTimeout(() => {
                        this.ignoreInputWatchers = false;
                    }, 500);

                    if (type !== "free") {
                        this.appointment.customer = appToEdit.extendedProps.customer
                        this.appointment.services = appToEdit.extendedProps.services.map((service) => service.id)
                        this.appointment.payments = appToEdit.extendedProps.payments
                        this.appointment.productSales = appToEdit.extendedProps.productSales
                        this.appointment.source = appToEdit.extendedProps.source
                    }

                    this.chosenEmployeeInForms = appToEdit.extendedProps.employeeID
                    this.appointment.type = type
                    this.appointment.note = appToEdit.extendedProps.note
                    this.appointment.datetime = {
                        start: moment(appToEdit.start).format(this.dateFormat.input),
                        end: moment(appToEdit.end).format(this.dateFormat.input),
                        end_with_break: appToEdit.extendedProps.end_with_break ?? null,
                    }
                },

                resetAppointmentVariable() {
                    this.appointment = {
                        customer: {},
                        datetime: {},
                        type: "reservation",
                        source: "phone",
                        break: null,
                        services: [],
                        payments: [],
                        productSales: [],
                    }
                },

                resetModals() {
                    this.resetAppointmentVariable()
                    this.resetAppointmentToDeleteVariable()
                    this.customerSearchQuery = ""
                    this.chosenEmployeeInForms = null
                    this.customers = {}
                    this.notify = false;
                },

                resetAppointmentToDeleteVariable() {
                    this.appointmentToDelete = null
                },

                getActiveColor(appointmentEmployee = null, appointmentType = null, service = null) {
                    if (!appointmentEmployee) {
                        appointmentEmployee = this.appointment.employee
                    }

                    if (!appointmentType) {
                        appointmentType = this.appointment.type
                    }

                    if (!service) {
                        service = Object.values(this.appointment.services)[0]
                    } else if (Number.isInteger(service)) {
                        service = this.services[service] ?? null
                    }

                    if (appointmentType === "free") {
                        return appointmentEmployee?.vacation_color ?? this.freeAppColor
                    }

                    let catID = service?.service_category_id ?? service?.category_id ?? null

                    return this.serviceColors?.[catID];
                },

                getTotalDuration(serviceIDs, humanTime = false) {
                    let duration = 0;
                    serviceIDs.forEach(id => {
                        duration += this.serviceDurations[id]
                    });
                    if (humanTime) {
                        duration = _thisClass.humanDurationFromMinutes(duration)
                    }
                    return duration
                },

                getTotalPrice(serviceIDs) {
                    let price = 0;
                    serviceIDs.forEach(id => {
                        price += this.services[id]?.price
                    });
                    return price
                },

                serviceOptionLabel(service) {
                    return `${service.title} / ${service.duration}min / ${service.price}€`;
                },

                setServiceList() {
                    const filteredArr = [];

                    let allowedServices = this.employees?.[this.chosenEmployeeInForms]?.allowed_services ?? null;

                    if (allowedServices) {
                        for (const key in this.services) {
                            if ((allowedServices).includes(parseInt(key))) {
                                filteredArr.push(this.services[key]);
                            }
                        }
                    }

                    this.employeeServices = filteredArr;

                    if (this.appointment.services.length) {
                        this.appointment.services = this.appointment.services.filter((service) => this.employeeServices.map((service) => service.id).includes(service));
                    }
                },
                formatEventTime(event) {
                    const start = moment(event.start);
                    let end = moment(event.end);
                    const endWithBreak = event.extendedProps.end_with_break;
                    if (!!endWithBreak) {
                        end = endWithBreak;
                    }

                    console.log("start", start, "end", end, "endWithBreak", endWithBreak)

                    if (start.isSame(end, 'day')) {
                        // Event is on the same day
                        return start.format('HH:mm') + ' - ' + end.format('HH:mm');
                    } else {
                        // Event spans more than one day
                        return start.format('DD.MM HH:mm') + ' - ' + end.format('DD.MM HH:mm');
                    }
                },
                getEndWithBreak(momentEnd, minutes) {
                    return momentEnd.add(minutes, 'minutes');
                },
                addPayment() {
                    if (!this.appointment.payments) this.appointment.payments = []

                    this.appointment.payments.push({
                        amount: null,
                        type: "c",
                        note: null,
                    })
                },
                removePayment(index) {
                    this.appointment.payments.splice(index, 1)
                },
                addSale() {
                    if (!this.appointment.productSales) this.appointment.productSales = []

                    this.appointment.productSales.push({
                        product_id: null,
                        price: null,
                        quantity: 1,
                        note: null,
                    })
                },
                removeSale(index) {
                    this.appointment.productSales.splice(index, 1)
                },
                setPriceIfEmpty(sale) {
                    if (!sale.price) {
                        sale.price = this.products[sale.product_id].price
                    }
                }
            },
            computed: {
                productOptions() {
                    let opts = Object.keys(this.products).map(id => {
                        return {
                            value: parseInt(id),
                            label: this.products[id].title + " (" + this.products[id].price + " €)"
                        }
                    })

                    return opts
                },
                canChangeEmployeeOnView() {
                    return this.chosenEmployeeOnView === -1 || this.loggedInEmployee.role === 'administrator'
                },
                appointmentToDeleteChosenServicesInlineText() {
                    let services = this.appointmentToDelete.extendedProps.services ?? [];
                    let toReturn = "";
                    services.forEach((service) => {
                        if (Number.isInteger(service)) {
                            toReturn += this.services[service].title + ", ";
                        } else {
                            toReturn += service.title + ", ";
                        }
                    });
                    return toReturn.slice(0, -2);
                },
                appointmentToDeleteDateFromToFormatted() {
                    if (!this.appointmentToDelete) return "Niečo sa pokazilo.";

                    const start = moment(this.appointmentToDelete.start, this.dateFormat.table);
                    const end = moment(this.appointmentToDelete.end, this.dateFormat.table);

                    const startDate = start.format(this.dateFormat.input);
                    const endDate = end.format(this.dateFormat.input);

                    if (start.isSame(end, 'day')) {
                        // If the start and end dates are the same, return the start date and end time only
                        return startDate + " - " + end.format('HH:mm');
                    } else {
                        // If the start and end dates are different, return both dates and times
                        return startDate + " - " + endDate;
                    }
                },
                appointmentBreakInMinutes() {
                    if (this.appointment.type === "free") return null;

                    let breakVal = null;

                    if (this.appointment.services.length) {
                        this.appointment.services.forEach(serviceID => {
                            console.log(this.services[serviceID])
                            let currentBreakVal = +(this.services[serviceID]?.static_break ?? -1);
                            if (currentBreakVal > -1) {
                                console.log("currentBreakVal", currentBreakVal)
                                breakVal = currentBreakVal;
                                return;
                            }
                        });
                    }

                    if (breakVal !== null) return breakVal;

                    const start = moment(this.appointment.datetime.start, this.dateFormat.input);
                    const end = moment(this.appointment.datetime.end, this.dateFormat.input);
                    const duration = end.diff(start, 'minutes');

                    this.breaks.some((durationBreakRecord) => {
                        if (duration <= durationBreakRecord.duration) {
                            breakVal = durationBreakRecord.break;
                            return true; // This will break out of the some loop
                        }
                        return false;
                    });

                    return breakVal;
                },
                appointmentDatetimeEndWithBreak() {
                    if (!this.appointment.datetime.end) return null;
                    if (!this.appointmentBreakInMinutes) return null;

                    return moment(this.appointment.datetime.end, this.dateFormat.input).add(this.appointmentBreakInMinutes, 'minutes').format("HH:mm");
                },
            },
            watch: {
                chosenEmployeeInForms(newID) {
                    if (!newID) return;

                    this.setServiceList()
                },
                'appointment.services'(newServices) {
                    if (this.ignoreInputWatchers) return;

                    let start = this.appointment.datetime.start
                    if (start && this.appointment.type === "reservation") {
                        this.appointment.datetime.end = moment(start, this.dateFormat.input).add(this.getTotalDuration(newServices), "minutes").format(this.dateFormat.input)
                    }
                },
                'appointment.datetime.start'(newStart) {
                    if (this.ignoreInputWatchers) return;

                    let start = newStart
                    let services = this.appointment?.services ?? [];
                    if (services.length && start && this.appointment.type === "reservation") {
                        this.appointment.datetime.end = moment(start, this.dateFormat.input).add(this.getTotalDuration(services), "minutes").format(this.dateFormat.input)
                    }
                },
            }
        });

        app.directive("click-outside", clickOutsideDirective);

        _thisClass.usePrimevue(app);

        app.mount("#calendarContainer");
    }

    _prepareAutoRefresh() {
        function autoRefresh() {
            let timeoutID = setTimeout(() => {
                location.reload();
            }, 600000); //10 mins

            // Reset the timer if the user interacts with the page
            document.addEventListener("click", resetTimer);
            document.addEventListener("mousemove", resetTimer);
            document.addEventListener("touchstart", resetTimer);

            function resetTimer() {
                clearTimeout(timeoutID);
                timeoutID = setTimeout(() => {
                    location.reload();
                }, 600000); //10 mins
            }
        }

        autoRefresh();
    }
}

new ReservationCalendar()

export {}
