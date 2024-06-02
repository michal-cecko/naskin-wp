

<div class="custom-dialog-wrapper" :class="visibleEditModal ? 'shown' : ''" id="editAppointmentModal">

    <div class="backdrop"></div>

    <div class="custom-dialog">

        <div class="custom-dialog--header ">
            <h3 class="custom-dialog--header--title">Úprava rezervácie</h3>
            <button type="button" class="close" @click="visibleEditModal = false"></button>
        </div>

        <div class="custom-dialog--body">
            @include('parts.dashboard.appointments.modals.admin-appointment-form')
        </div>

        <div class="custom-dialog--footer">
            <div class="field-container field-row" v-if="appointment.type === 'reservation'">
                <input v-model="notify" type="checkbox" class="form-control" id="notify" name="notify">
                <label for="notify">Odoslať notifikáciu o úprave?</label>
            </div>
            <button type="button" class="button button-secondary button-large" @click="visibleEditModal = false">Zrušiť</button>
            <button type="button" class="button button-primary button-large" v-html="buttonLoader ? 'Upravujem...' : 'Upraviť'" @click="editAppointment()"></button>
        </div>
    </div>
</div>