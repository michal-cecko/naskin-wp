<div class="modal fade" id="deleteAppointmentModal" tabindex="-1" aria-labelledby="deleteAppointmentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAppointmentModalLabel">Zmazať termín</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <p>Naozaj chcete vymazať tento termín?</p>
                        <div class="appointmentToDelete" v-if="appointmentToDelete">
                            <div class="time"
                                 v-html="moment(appointmentToDelete.start).format('HH:mm') + ' - ' + moment(appointmentToDelete.end).format('HH:mm')"></div>
                            <div class="title" v-html="appointmentToDelete.title"></div>
                            <div class="service" v-if="appointmentToDelete.extendedProps.service"
                                 v-html="appointmentToDelete.extendedProps.service"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" v-if="appointmentToDelete">
                <div class="field-container me-auto"
                     v-if="appointmentToDelete.extendedProps.type === 'reservation'">
                    <input v-model="notify" type="checkbox" class="form-control" id="notify" name="notify">
                    <label for="notify">Odoslať notifikáciu?</label>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušiť</button>
                <button type="button" class="btn btn-danger" v-html="buttonLoader ? 'Vymazávam...' : 'Vymazať'"
                        @click="removeAppointment()">
                </button>
            </div>
        </div>
    </div>
</div>