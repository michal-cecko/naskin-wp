<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">Pridať termín</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row divided-row">
                    <div class="heading-part col-12">
                        <h3>Rezervácia</h3>
                    </div>
                    <div class="part col-12"
                         v-show="chosenEmployeeOnView === -1 || loggedInEmployee.role === 'administrator'"
                         :class="appointment.type === 'appointment' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="type">Pracovník</label>
                            <select v-model="chosenEmployeeInForms" class="form-control" id="type">
                                <option v-for="employee in employees" :value="employee.id"
                                        v-html="employee.name"></option>
                            </select>
                        </div>
                    </div>
                    <div class="part col-12" :class="appointment.type === 'appointment' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="type">Typ</label>
                            <select v-model="appointment.type" class="form-control" id="type">
                                <option value="free">Voľno</option>
                                <option value="appointment" selected>Termín</option>
                            </select>
                        </div>
                    </div>
                    <div class="part col-12" :class="appointment.type === 'appointment' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="start" class="form-label">Začiatok</label>
                            <input v-model="appointment.datetime.start" onfocus="this.showPicker()" step="1800"
                                   type="datetime-local" class="form-control" id="start" name="start">
                        </div>
                    </div>
                    <div class="part col-12" v-show="appointment.type === 'appointment'"
                         :class="appointment.type === 'appointment' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="service">Služba</label>
                            <select v-model="appointment.serviceID" class="form-control" id="service">
                                @if(!empty($services))
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" @selected($loop->index === 0)>
                                            {{$service->title}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="part col-12" :class="appointment.type === 'appointment' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="end" class="form-label">Koniec</label>
                            <input v-model="appointment.datetime.end" onfocus="this.showPicker()" step="1800"
                                   type="datetime-local" class="form-control" id="end" name="end">
                        </div>
                    </div>
                </div>
                <div class="row divided-row" v-if="appointment.type === 'appointment'">
                    <div class="heading-part col-12">
                        <h3>Zákazník</h3>
                    </div>
                    <div class="part col-12">
                        <div class="field-container mb-3">
                            <label for="name" class="form-label">Vybrať z databázy</label>
                            <div class="searchable-select" :class="shownOptions ? 'show' : ''"
                                 v-click-outside="hideOptions">
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
                    <div class="part col-md-4 col-12">
                        <div class="field-container mb-3">
                            <label for="name" class="form-label">Meno a priezvisko</label>
                            <input v-model="appointment.customer.name" type="text" class="form-control" id="name"
                                   name="name">
                        </div>
                    </div>
                    <div class="part col-md-4 col-12">
                        <div class="field-container mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input v-model="appointment.customer.email" type="email" class="form-control" id="email"
                                   name="email">
                        </div>
                    </div>
                    <div class="part col-md-4 col-12">
                        <div class="field-container mb-3">
                            <label for="phone" class="form-label">Telefón</label>
                            <input v-model="appointment.customer.phone" type="tel" class="form-control" id="phone"
                                   name="phone">
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
                            <input v-model="appointment.note" type="tel" class="form-control" id="note" name="note">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="field-container me-auto" v-if="appointment.type === 'appointment'">
                    <input v-model="notify" type="checkbox" class="form-control" id="notify" name="notify">
                    <label for="notify">Odoslať notifikáciu?</label>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušiť</button>
                <button type="button" class="btn btn-primary" v-html="buttonLoader ? 'Pridávam...' : 'Pridať'"
                        @click="createAppointment()"></button>
            </div>
        </div>
    </div>
</div>