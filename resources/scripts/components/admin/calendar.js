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

                    buttonLoader: false,

                    dateRange: { start: null, end: null },
                    customers: {},
                    customerSearchQuery: "",
                    shownOptions: false,
                    debounceTimer: null,

                    notify: false,

                    hasInit: false,

                    dateFormat: {
                        'input': 'DD/MM/YYYY HH:mm',
                        'payload': 'YYYY-MM-DD HH:mm:ss',
                        'table': 'YYYY-MM-DDTHH:mm:ss',
                        'table_select': 'ddd MMM DD YYYY HH:mm:ss [GMT]ZZ (z)',
                    }
                };
            },
            created() {
                console.log(`Calendar Vue component has been created.`)
                this.resetAppointmentVariable()
                this.resetAppointmentToDeleteVariable()
            },
            mounted() {
                let pageData = document.getElementById('page-data')?.dataset ?? {}
                this.loggedInEmployee = document.getElementById('logged-user')?.dataset ?? {}

                let employees = JSON.parse(pageData?.employees)
                this.employees = Object.assign({}, employees);

                let services = JSON.parse(pageData?.services)
                this.services = Object.assign({}, services);

                let loggedInId = parseInt(this.loggedInEmployee.id);
                if (this.loggedInEmployee.role === "administrator" || !this.employees[loggedInId]) {
                    this.chosenEmployeeOnView = -1;
                    const firstKey = Object.keys(this.employees)[0];
                    if (this.employees[firstKey]) {
                        this.chosenEmployeeInForms = this.employees[firstKey].id;
                    }
                } else {
                    this.chosenEmployeeOnView = this.chosenEmployeeInForms = loggedInId;
                }

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

                    let now = _thisClass.getCurrentTimestamp()
                    let appointments = await this.fetchAppointments(now, this.chosenEmployeeOnView, "timeGridWeek");

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
                            };

                            _thisVue.visibleCreateModal = true;
                        },
                        longPressDelay: longPressDelay,
                        editable: false,
                        selectable: true,
                        selectOverlap: true,
                        eventResizableFromStart: false,
                        initialView: 'timeGridWeek',
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

                            let deleteButtonEl = eventEl.querySelector('.delete-button');
                            if (!deleteButtonEl) {
                                eventEl.insertAdjacentHTML('beforeend', html);
                                deleteButtonEl = eventEl.querySelector('.delete-button');
                                deleteButtonEl.addEventListener('click', function () {
                                    _thisVue.resetAppointmentToDeleteVariable()
                                    _thisVue.appointmentToDelete = info.event
                                    _thisVue.visibleDeleteModal = true;
                                });
                            }
                        },

                        eventContent: function (info) {
                            let event = info.event;
                            let view = info.view.type;
                            let props = event.extendedProps;

                            let html = '<div class="event-content-container ' + view + '"><div class="time">' + moment(event.start).format('HH:mm') + ' - ' + moment(event.end).format('HH:mm') + '</div>';
                            html += '<div class="title">' + event.title + '</div>';

                            let empl = _thisVue.employees[props.employeeID]?.name ?? props.employee ?? null
                            if (_thisVue.chosenEmployeeOnView === -1) {
                                if (empl) {
                                    if(props.type === "free") {
                                        html += '<div class="employee">Vybaví: ' + empl + '</div>';
                                    } else {
                                        html += '<div class="employee">' + empl + '</div>';
                                    }
                                }
                            }

                            if (props.type === "reservation") {
                                html += '<div class="service">' + props.services.map((item) =>item?.title).join(", ") + '</div>';
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
                            if (info.jsEvent.target.classList.contains('delete-button')) {
                                return false;
                            }
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

                            let fetchStart = _thisClass.utc(moment(start).add(1, "hour"))
                            console.log("SHOULD REFETCH")
                            //let appointments = await _thisVue.fetchAppointments(fetchStart, _thisVue.chosenEmployeeOnView, view.type)
                            //_thisVue.exchangeAppointmentsOnView(appointments)
                        }
                    })
                    calendar.on('datesSet', async function (info) {
                        if (!_thisVue.hasInit) return;

                        let start = calendar.view.currentStart
                        let end = calendar.view.currentEnd

                        _thisVue.dateRange.start = start
                        _thisVue.dateRange.end = end

                        let fetchStart = _thisClass.utc(moment(start).add(1, "hour"))
                        let appointments = await _thisVue.fetchAppointments(fetchStart, _thisVue.chosenEmployeeOnView, calendar.view.type)
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
                    }

                    if (this.appointment.type !== "free") {
                        body.source = this.appointment.source
                        body.services = this.appointment.services
                        body.customer = this.appointment.customer
                        body.note = this.appointment.note
                    }

                    this.buttonLoader = true;

                    await _thisClass.postFetch("/appointment/store-admin", body)
                        .then(response => response.json())
                        .then(response => {

                            this.buttonLoader = false;

                            if (!response.success) {
                                console.error(response)
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
                                    note: this.appointment.note ?? null,
                                    services: this.appointment.services,
                                    employee: this.employees?.[this.chosenEmployeeInForms]?.name,
                                    employeeID: this.appointment.employeeID,
                                    customer: this.appointment.customer,
                                },
                                color: this.getActiveColor(this.employees?.[this.chosenEmployeeInForms]),
                                textColor: '#ffffff'
                            }

                            this.calendar.addEvent(ev);

                            this.visibleCreateModal = false;
                            this.resetModals()
                        });


                    return true;
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

                    if (this.appointment.type !== "free" && this.appointment.services.length === 0) {
                        _thisClass.notify("Vyberte aspoň jednu službu.", "error")
                        return false;
                    }

                    if (this.appointment.type !== "free" && !this.appointment.customer?.id && !this.appointment.customer?.name) {
                        _thisClass.notify("Vyberte zákazníka alebo zadajte údaje nového.", "error")
                        return false;
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
                    }

                    if (this.appointment.type !== "free") {
                        data.source = this.appointment.source
                        data.services = this.appointment.services
                        data.note = this.appointment.note
                    }

                    this.buttonLoader = true;

                    await _thisClass.postFetch("/appointment/edit-admin", data)
                        .then(response => response.json())
                        .then(response => {

                            if (!response.success) {
                                console.error(response)
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
                                    note: this.appointment.note ?? null,
                                    services: this.appointment.services?.map((appService => this.services[appService])),
                                    employee: this.employees?.[this.chosenEmployeeInForms]?.name,
                                    employeeID: this.appointment.employeeID,
                                    customer: this.appointment.customer,
                                },
                                color: this.getActiveColor(this.employees?.[this.chosenEmployeeInForms]),
                                textColor: '#ffffff'
                            }

                            console.log("edited : ", event, this.appointment)

                            this.calendar.addEvent(event);

                            this.buttonLoader = false;
                            this.visibleEditModal = false;
                            this.resetModals()

                            return true;
                        });
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
                                console.error(response)
                                return
                            }

                            this.appointmentToDelete.remove()
                            this.visibleDeleteModal = false;
                            this.notify = false;
                        });
                },

                fetchAppointments(timestamp, employeeID, dateRange = "timeGridWeek") {
                    let params = {
                        employeeID: employeeID,
                        timestamp: timestamp,
                        dateRange: dateRange,
                    };

                    return fetch(_thisClass.addParamsToUrl(params, `${_thisClass.apiUrl}/appointment/table`))
                        .then(response => response.json())
                        .then(response => {

                            console.log(response)

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
                                        services: appointment.services?.map((appService => this.services[appService.service_id])) ?? {},
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
                        .catch(error => {
                            console.error(error);
                            return null;
                        });
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

                    let now = moment(this.dateRange.start).add(1, "hour").valueOf()
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
                    this.resetAppointmentVariable()
                    let type = appToEdit.extendedProps.type

                    if (type !== "free") {
                        this.appointment.customer = appToEdit.extendedProps.customer
                        this.appointment.services = appToEdit.extendedProps.services.map((service) => service.id)
                    }

                    this.chosenEmployeeInForms = appToEdit.extendedProps.employeeID
                    this.appointment.type = type
                    this.appointment.note = appToEdit.extendedProps.note
                    this.appointment.datetime = {
                        start: moment(appToEdit.start).format(this.dateFormat.input),
                        end: moment(appToEdit.end).format(this.dateFormat.input),
                    }

                    console.log(appToEdit.end, moment(appToEdit.end).format(this.dateFormat.input))
                },

                resetAppointmentVariable() {
                    this.appointment = {
                        customer: {},
                        datetime: {},
                        type: "reservation",
                        source: "phone",
                        services: [],
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
                    }

                    if(appointmentType === "free") {
                        console.log(service, appointmentType, appointmentEmployee)

                        return appointmentEmployee?.vacation_color ?? this.freeAppColor
                    }

                    return this.serviceColors[service?.service_category_id];
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
                                console.log("added", this.services[key])
                            }
                        }
                    }

                    //this.appointment.services = [];
                    this.employeeServices = filteredArr;
                },
            },
            computed: {
                canChangeEmployeeOnView() {
                    return this.chosenEmployeeOnView === -1 || this.loggedInEmployee.role === 'administrator'
                },
                appointmentToDeleteChosenServicesInlineText() {
                    let services = this.appointmentToDelete.extendedProps.services ?? [];
                    let toReturn = "";
                    services.forEach((service) => {
                        if(Number.isInteger(service)) {
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
                }
            },
            watch: {
                chosenEmployeeInForms(newID) {
                    if (!newID) return;

                    this.setServiceList()
                },
                'appointment.services'(newServices) {
                    let start = this.appointment.datetime.start
                    if (start && this.appointment.type === "reservation") {
                        this.appointment.datetime.end = moment(start, this.dateFormat.input).add(this.getTotalDuration(newServices), "minutes").format(this.dateFormat.input)
                    }
                },
                'appointment.datetime.start'(newStart) {
                    let start = newStart
                    let services = this.appointment?.services ?? [];
                    if (services.length && start && this.appointment.type === "reservation") {
                        this.appointment.datetime.end = moment(start, this.dateFormat.input).add(this.getTotalDuration(services), "minutes").format(this.dateFormat.input)
                    }
                },
            }
        });
        app.use(primevue.config.default);
        app.directive("click-outside", clickOutsideDirective);

        app.component('p-datepicker', primevue.calendar);
        app.component('p-multiselect', primevue.multiselect);
        app.component('p-input-text', primevue.inputtext);
        app.component('p-dialog', primevue.dialog);
        app.component('p-confirmdialog', primevue.confirmdialog);

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
