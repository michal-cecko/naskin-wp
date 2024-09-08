<div class="custom-dialog-wrapper" :class="visibleCreateModal ? 'shown' : ''" id="createAppointmentModal">

    <div class="backdrop"></div>

    <div class="custom-dialog">

        <div class="custom-dialog--header ">
            <h3 class="custom-dialog--header--title">Nová rezervácia</h3>
            <button type="button" class="close" @click="visibleCreateModal = false"></button>
        </div>

        <div class="custom-dialog--body">
            @include('parts.dashboard.appointments.modals.admin-appointment-form')
        </div>

        <div class="custom-dialog--footer">
            <div class="notify-checkbox-container">
                <div class="field-container field-row" v-if="appointment.type === 'reservation'">
                    <input v-model="notify_customer" type="checkbox" class="form-control" id="notify_customer" name="notify_customer">
                    <label for="notify_customer">Upozorniť zákazníka o vytvorení?</label>
                </div>
                <div class="field-container field-row">
                    <input v-model="notify_employee" type="checkbox" class="form-control" id="notify_employee" name="notify_employee">
                    <label for="notify_employee">Upozorniť pracovníka o vytvorení?</label>
                </div>
            </div>
            <div class="buttons-container">
                <button type="button" class="button button-secondary button-large" @click="visibleCreateModal = false">Zrušiť</button>
                <button type="button" class="button button-primary button-large" v-html="buttonLoader ? 'Pridávam...' : 'Pridať'" @click="createAppointment()"></button>
            </div>
        </div>
    </div>

</div>