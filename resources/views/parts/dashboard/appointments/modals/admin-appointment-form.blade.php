@php
    use Theme\Enum\AppointmentPaymentType;use Theme\Enum\AppointmentSource;
    use Theme\Enum\AppointmentType;
@endphp

<div class="divided-row">
    <div :class="visibleEditModal || appointment.type !== 'free' ? 'third' : 'half'" v-show="canChangeEmployeeOnView">
        <div class="field-container">
            <label for="employee">Pracovník</label>
            <select class="custom-select" v-model="chosenEmployeeInForms" id="employee">
                <option v-for="employee in employees" :value="employee.id"
                        v-html="employee.name"></option>
            </select>
        </div>
    </div>
    <div :class="appointment.type === 'free' || !canChangeEmployeeOnView ? 'half' : 'third'" v-if="!visibleEditModal">
        <div class="field-container">
            <label for="type">Typ</label>
            <select class="custom-select" v-model="appointment.type" id="type">
                @foreach(AppointmentType::translatedCases() as $key => $value)
                    <option value="{{$key}}" @selected($loop->index === 0)>{{$value}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div :class="!canChangeEmployeeOnView && visibleEditModal ? 'half' : 'third'"
         v-if="appointment.type === 'reservation'">
        <div class="field-container">
            <label for="source">Objednaný cez</label>
            <select class="custom-select" v-model="appointment.source" id="source">
                @foreach(AppointmentSource::translatedCases() as $key => $value)
                    <option value="{{$key}}" @selected($loop->index === 0)>{{$value}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="full" v-show="appointment.type === 'reservation'">
        <div class="field-container services-field">
            <label for="services">Služby</label>
            <p-multiselect
                    v-model="appointment.services"
                    :options="employeeServices"
                    :filter="true"
                    :option-label="serviceOptionLabel"
                    option-value="id"
                    placeholder="Vyberte služby"
                    display="chip"
                    name="services"
                    :select-all="false"
                    :show-toggle-all="false"
                    filter-placeholder="Vyhľadať službu...">
            </p-multiselect>
        </div>
        <div class="services-total" v-show="appointment.services">
            Celkom: <b>@{{ getTotalDuration(appointment.services, true) }} / @{{ getTotalPrice(appointment.services)
                }}€</b>
        </div>
    </div>
    <div class="half">
        <div class="field-container">
            <label for="start">Začiatok</label>
            <p-datepicker v-model="appointment.datetime.start" show-time hour-format="24"
                          date-format="dd/mm/yy"
                          placeholder="Vyberte začiatok"></p-datepicker>
        </div>
    </div>
    <div class="half">
        <div class="field-container">
            <label for="end">Koniec</label>
            <p-datepicker v-model="appointment.datetime.end" show-time hour-format="24"
                          date-format="dd/mm/yy"
                          placeholder="Vyberte koniec"></p-datepicker>
        </div>
        <div class="end-with-break" v-if="appointment.type === 'reservation' && !!appointmentDatetimeEndWithBreak">
            S prestávkou do <b>@{{appointmentDatetimeEndWithBreak}}</b> (@{{ appointmentBreakInMinutes }} min)
        </div>
    </div>
</div>
<div class="divided-row" v-if="appointment.type === 'reservation'">
    <div class="heading-part">
        <h3>Zákazník</h3>
    </div>
    <div class="full">
        <div class="field-container">
            <label for="name">Vybrať z databázy</label>
            <div class="searchable-select" :class="shownOptions ? 'show' : ''">
                <input type="text" class="searchbar" v-model="customerSearchQuery"
                       @input="debouncedFetchCustomers" @click="shownOptions = true" v-click-outside="hideOptions">
                <div class="options">
                    <div class="option" v-if="Object.keys(customers).length > 0"
                         v-for="customer in customers" @click="chooseCustomer(customer)"
                         v-html="formatCustomerOption(customer)"></div>
                    <div class="nothing" v-if="Object.keys(customers).length === 0"
                         v-html="'Žiadne výsledky.'"></div>
                </div>
            </div>
        </div>
    </div>
    <span style="width: 100%; display: block; font-size: 0.85rem; color: #999">alebo</span>
    <div class="third">
        <div class="field-container">
            <label for="name">Meno a priezvisko</label>
            <input class="custom-text-input" type="text" v-model="appointment.customer.name">
        </div>
    </div>
    <div class="third">
        <div class="field-container">
            <label for="email">Email</label>
            <input class="custom-text-input" type="text" v-model="appointment.customer.email">
        </div>
    </div>
    <div class="third">
        <div class="field-container">
            <label for="phone">Telefón</label>
            <input class="custom-text-input" type="text" v-model="appointment.customer.phone">
        </div>
    </div>
</div>
<div class="divided-row" v-if="appointment.type === 'reservation'">
    <div class="heading-part">
        <h3>Platby</h3>
    </div>
    <div class="row" v-for="(payment, key) in (appointment.payments ?? [])" :key="key">
        <div class="quarter">
            <div class="field-container">
                <label for="amount">Suma (€)</label>
                <p-number v-model="payment.amount" :inputId="`payment-amount-${key}`" mode="currency" currency="EUR" placeholder="Suma (€)"
                          locale="sk-SK"></p-number>
            </div>
        </div>
        <div class="quarter">
            <div class="field-container">
                <label for="amount">Typ</label>
                <select class="custom-select" v-model="payment.type" :id="`payment-type-${key}`">
                    @foreach(AppointmentPaymentType::translatedCases() as $key => $value)
                        <option value="{{$key}}" @selected($loop->index === 0)>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="half payment-note-container">
            <div class="field-container">
                <label for="amount">Poznámka</label>
                <input class="custom-text-input" type="text" v-model="payment.note" placeholder="Poznámka..." :id="`payment-note-${key}`">
            </div>
            <div class="dashicons-before dashicons-trash remove-payment" @click="removePayment(key)"></div>
        </div>
    </div>
    <div class="full">
        <button type="button" class="button button-primary button-small" @click="addPayment" style="margin-top: 0.4rem">Pridať platbu</button>
    </div>
</div>
<div class="divided-row">
    <div class="heading-part">
        <h3>Ostatné</h3>
    </div>
    <div class="full">
        <div class="field-container">
            <label for="note">Poznámka</label>
            <input class="custom-text-input" type="text" v-model="appointment.note">
        </div>
    </div>
</div>