@php
    use Theme\Enum\AppointmentSource;
    use Theme\Enum\AppointmentType;
@endphp

<p-dialog v-model:visible="visibleCreateModal" modal header="Pridať termín">
    <div class="row divided-row">
        <div class="heading-part col-12">
            <h3>Rezervácia</h3>
        </div>
        <div class="part col-12"
             v-show="chosenEmployeeOnView === -1 || loggedInEmployee.role === 'administrator'"
             :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
            <div class="field-container mb-3">
                <label for="employee">Pracovník</label>
                <select v-model="chosenEmployeeInForms" class="form-control" id="employee">
                    <option v-for="employee in employees" :value="employee.id"
                            v-html="employee.name"></option>
                </select>
            </div>
        </div>
        <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
            <div class="field-container mb-3">
                <label for="type">Typ</label>
                <select v-model="appointment.type" class="form-control" id="type">
                    @foreach(AppointmentType::translatedCases() as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
            <div class="field-container mb-3">
                <label for="type">Objednaný cez</label>
                <select v-model="appointment.type" class="form-control" id="type">
                    @foreach(AppointmentSource::translatedCases() as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="part col-12" v-show="appointment.type === 'reservation'">
            <div class="field-container services-field mb-3">
                <label for="service">Služby</label>
                <p-multiselect
                        v-model="appointment.services"
                        :options="employeeServices"
                        :filter="true"
                        :option-label="serviceOptionLabel"
                        option-value="id"
                        placeholder="Vyberte služby"
                        display="chip"
                        :select-all="false"
                        :show-toggle-all="false"
                        filter-placeholder="Vyhľadať službu..."></p-multiselect>
            </div>
        </div>
        <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
            <div class="field-container mb-3">
                <label for="start" class="form-label">Začiatok</label>
                <p-datepicker v-model="appointment.datetime.start" show-time hour-format="24" date-format="dd/mm/yy"
                              placeholder="Vyberte začiatok"></p-datepicker>
            </div>
        </div>
        <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
            <div class="field-container mb-3">
                <label for="end" class="form-label">Koniec</label>
                <p-datepicker v-model="appointment.datetime.end" show-time hour-format="24" date-format="dd/mm/yy"
                              placeholder="Vyberte koniec"></p-datepicker>
            </div>
        </div>
    </div>
    <div class="row divided-row" v-if="appointment.type === 'reservation'">
        <div class="heading-part col-12">
            <h3>Zákazník</h3>
        </div>
        <div class="part col-12">
            <div class="field-container mb-3">
                <label for="name" class="form-label">Vybrať z databázy</label>
                <div class="searchable-select" :class="shownOptions ? 'show' : ''" v-click-outside="hideOptions">
                    <input type="text" class="searchbar" v-model="customerSearchQuery"
                           @input="debouncedFetchCustomers" @click="shownOptions = true">
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
        <span style="font-size: 0.85rem; color: #999; margin-bottom: 0.6rem">alebo</span>
        <div class="part col-md-4 col-12">
            <div class="field-container mb-3">
                <label for="name" class="form-label">Meno a priezvisko</label>
                <p-input-text type="text" v-model="appointment.customer.name"></p-input-text>
            </div>
        </div>
        <div class="part col-md-4 col-12">
            <div class="field-container mb-3">
                <label for="email" class="form-label">Email</label>
                <p-input-text type="text" v-model="appointment.customer.email"></p-input-text>
            </div>
        </div>
        <div class="part col-md-4 col-12">
            <div class="field-container mb-3">
                <label for="phone" class="form-label">Telefón</label>
                <p-input-text type="text" v-model="appointment.customer.phone"></p-input-text>
            </div>
        </div>
    </div>
    <div class="row divided-row">
        <div class="heading-part col-12">
            <h3>Ostatné</h3>
        </div>
        <div class="part col-12">
            <div class="field-container mb-3">
                <label for="note" class="form-label">Poznámka</label>
                <p-input-text type="text" v-model="appointment.note"></p-input-text>
            </div>
        </div>
    </div>
    <div class="dialog-footer">
        <div class="field-container me-auto" v-if="appointment.type === 'reservation'">
            <input v-model="notify" type="checkbox" class="form-control" id="notify" name="notify">
            <label for="notify">Odoslať notifikáciu?</label>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušiť</button>
        <button type="button" class="btn btn-primary" v-html="buttonLoader ? 'Pridávam...' : 'Pridať'"
                @click="createAppointment()"></button>
    </div>
</p-dialog>