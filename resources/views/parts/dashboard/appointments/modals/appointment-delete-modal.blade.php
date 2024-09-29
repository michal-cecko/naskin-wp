<div class="custom-dialog-wrapper" :class="visibleDeleteModal ? 'shown' : ''" id="deleteAppointmentModal">

    <div class="backdrop"></div>

    <div class="custom-dialog">

        <div class="custom-dialog--header ">
            <h3 class="custom-dialog--header--title">Zmazať rezerváciu</h3>
            <button type="button" class="close" @click="visibleDeleteModal = false"></button>
        </div>

        <div class="custom-dialog--body">
            <div class="row">
                <div class="col-12">
                    <p>Naozaj chcete vymazať tento termín?</p>
                    <div class="appointmentToDelete" v-if="appointmentToDelete">
                        <div class="time">@{{ appointmentToDeleteDateFromToFormatted }}</div>
                        <div class="title" v-html="appointmentToDelete.title"></div>
                        <div class="service" v-if="appointmentToDelete?.extendedProps?.type === 'reservation'">
                            @{{ appointmentToDeleteChosenServicesInlineText }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-dialog--footer">
            <div class="notify-checkbox-container">
                <div class="field-container field-row" v-if="appointmentToDelete?.extendedProps?.type === 'reservation' || resource?.type === 'reservation'">
                    <input v-model="notify_customer" type="checkbox" class="form-control" id="notify_customer"
                           name="notify_customer">
                    <label for="notify_customer">Upozorniť zákazníka o zrušení?</label>
                </div>
                <div class="field-container field-row">
                    <input v-model="notify_employee" type="checkbox" class="form-control" id="notify_employee"
                           name="notify_employee">
                    <label for="notify_employee">Upozorniť pracovníka o zrušení?</label>
                </div>
            </div>
            <div class="buttons-container">
                <button type="button" class="button button-secondary button-large" @click="visibleDeleteModal = false">
                    Zrušiť
                </button>
                <button type="button" class="button button-danger button-large"
                        v-html="buttonLoader ? 'Vymazávam...' : 'Vymazať'" @click="removeAppointment()"></button>
            </div>
        </div>
    </div>
</div>