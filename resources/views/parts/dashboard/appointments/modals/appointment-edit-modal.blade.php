@php use Theme\Enum\AppointmentType; @endphp

<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAppointmentModalLabel">Upraviť termín</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row divided-row">
                    <div class="heading-part col-12">
                        <h3>Rezervácia</h3>
                    </div>
                    <div class="part col-12"
                         v-show="chosenEmployeeOnView === -1 || loggedInEmployee.role === 'administrator'"
                         :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="type">Pracovník</label>
                            <select v-model="chosenEmployeeInForms" class="form-control" id="type">
                                <option v-for="employee in employees" :value="employee.id"
                                        v-html="employee.name"></option>
                            </select>
                        </div>
                    </div>
                    <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="type">Typ</label>
                            <select v-model="appointment.type" class="form-control" id="type" disabled>
                                <option value="{{AppointmentType::VACATION->value}}">Voľno</option>
                                <option value="{{AppointmentType::RESERVATION->value}}" selected>Termín</option>
                            </select>
                        </div>
                    </div>
                    <div class="part col-12" v-show="appointment.type === 'reservation'">
                        <div class="field-container services-field mb-3">
                            <label for="service">Služby</label>
                            <select v-model="appointment.services" class="form-control" id="service" multiple>
                                <option :value="service.id" v-for="service in employeeServices">
                                    @{{service.title}} / @{{service.duration}}min / @{{service.price}}€
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="start" class="form-label">Začiatok</label>
                            <input v-model="appointment.datetime.start" onfocus="this.showPicker()" step="1800"
                                   type="datetime-local" class="form-control" id="start" name="start">
                        </div>
                    </div>
                    <div class="part col-12" :class="appointment.type === 'reservation' ? 'col-md-6' : ''">
                        <div class="field-container mb-3">
                            <label for="end" class="form-label">Koniec</label>
                            <input v-model="appointment.datetime.end" onfocus="this.showPicker()" step="1800"
                                   type="datetime-local" class="form-control" id="end" name="end">
                        </div>
                    </div>
                </div>
                <div class="row divided-row" v-if="appointment.type === 'reservation'">
                    <div class="heading-part col-12">
                        <h3>Zákazník</h3>
                    </div>
                    <div class="part col-md-4 col-12">
                        <div class="field-container mb-3">
                            <label for="name" class="form-label">Meno a priezvisko</label>
                            <input v-model="appointment.customer.name" type="text" class="form-control" id="name" name="name" disabled>
                        </div>
                    </div>
                    <div class="part col-md-4 col-12">
                        <div class="field-container mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input v-model="appointment.customer.email" type="email" class="form-control" id="email" name="email" disabled>
                        </div>
                    </div>
                    <div class="part col-md-4 col-12">
                        <div class="field-container mb-3">
                            <label for="phone" class="form-label">Telefón</label>
                            <input v-model="appointment.customer.phone" type="tel" class="form-control" id="phone" name="phone" disabled>
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
                <div class="field-container me-auto" v-if="appointment.type === 'reservation'">
                    <input v-model="notify" type="checkbox" class="form-control" id="notify" name="notify">
                    <label for="notify">Odoslať notifikáciu o úprave?</label>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušiť</button>
                <button type="button" class="btn btn-primary btn-loader"
                        v-html="buttonLoader ? 'Upravujem...' : 'Upraviť'"
                        @click="editAppointment()">
                </button>
            </div>
        </div>
    </div>
</div>