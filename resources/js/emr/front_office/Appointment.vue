<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="appointmentConfirmModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Start Visit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRFrontOfficeFormCheckIn :appointment.sync="appointment" :editMode.sync="editMode" @refreshAppointment="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="appointmentFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Appointment Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRFrontOfficeFormAppointment :appointment.sync="appointment" :editMode.sync="editMode" @refreshAppointment="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">About Appointment</h3>
                    <div class="card-tools">
                        <button class="btn btn-tool nav-link" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button class="btn btn-block dropdown-item" v-if="canCheckIn(appointment)" @click="editAppointment(appointment)"><i class="fas fa-edit mr-1 text-primary"></i> Edit Appointment</button>
                            <button class="btn btn-block dropdown-item" v-if="canCheckIn(appointment)" @click="checkIn(appointment)"><i class="fas fa-calendar-check mr-1 text-success"></i> Check In Appointment</button>
                            <button class="btn btn-block dropdown-item" v-if="appointment.status == 'pending'" @click="reschedule(appointment)"><i class="fas fa-calendar-alt mr-1 text-warning"></i> Reschedule</button>
                            <button class="btn btn-block dropdown-item" v-if="canCancel(appointment)" @click="cancelAppointment(appointment)"><i class="fas fa-trash mr-1 text-danger"></i> Delete</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-book mr-1"></i> Patient</strong>
                    <p class="text-muted">{{ patientName(appointment?.patient) }}</p>
                    <hr>
                    <strong>Insurance:</strong>
                    <p class="text-muted"> {{ appointment?.care_plan?.name || 'Cash' }}</p>
                    <hr />
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Branch</strong>
                    <p class="text-muted">{{ appointment?.branch?.name || 'No Branch' }}</p>
                    <hr>

                    <strong><i class="fas fa-pencil-alt mr-1"></i> Service Type</strong>
                    <p class="text-muted">{{ appointment?.service_type?.name || 'No Service' }}</p>
                    <hr>
                    <strong v-if="appointment?.service_type?.name == 'Consultation'"><i class="fas fa-user-md mr-1"></i> Consultant</strong>
                    <p class="text-muted" v-if="appointment?.service_type?.name == 'Consultation'">{{ FullName(appointment?.consultant) }}</p>
                    <hr />
                    <strong><i class="fas fa-traffic-light mr-1"></i> Status</strong>
                    <p class="text-muted">{{ firstUp(appointment?.status || 'No Status') }}</p>

                    <hr>
                    <strong><i class="far fa-calendar"></i> Date</strong>
                    <p class="text-muted"> {{ ExcelDate(appointment?.date) }}</p>
                    <hr />
                    <strong><i class="far fa-clock"></i> Time</strong>
                    <p class="text-muted"> {{ appointment?.time_slot }}</p>
                    <hr />
                    <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
                    <p class="text-muted" v-html="appointment?.remarks"></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#patient" data-toggle="tab">Patient</a></li>
                        <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="patient">
                            <EMRPatientDetailSummary :patient.sync="appointment?.patient || {}" />
                        </div>
                        <div class="tab-pane" id="timeline">
                            
                        </div>
                        <div class="tab-pane" id="settings">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRFrontOfficeDetailAppointmentList from '@/emr/front_office/details/AppointmentList.vue';
import EMRFrontOfficeFormAppointment from '@/emr/front_office/forms/Appointment.vue';
import EMRFrontOfficeFormCheckIn from '@/emr/front_office/forms/CheckIn.vue';
import EMRPatientDetailSummary from '@/emr/patients/details/Summary.vue';
export default {
    components:{
        EMRFrontOfficeFormAppointment, EMRFrontOfficeFormCheckIn, EMRFrontOfficeDetailAppointmentList, EMRPatientDetailSummary
    },
    data() {
        return {
            loading: false,
            appointment: {},
            appointments: [],
            departments: [],
            editMode: false,
            service_types: [],
            specialties: [],
            statuses: ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'],
            filters: {
                date: '',
                service_type_id: '',
                specialty_id: '',
                status: 'pending',
                search: ''
            }
        }
    },
    methods: {
        addAppointment(){
            this.editMode = false;
            this.loading = true;
            this.appointment = {};
            $('#appointmentModal').modal('show');
            this.loading = false;
        },
        cancelAppointment(a) {
            if (!confirm('Cancel this appointment?')) return
            axios.post(`/api/appointments/${a.id}/cancel`).then(() => this.fetchAppointments())
        },
        canCheckIn(a) {
            return ['pending', 'confirmed'].includes(a.status)
        },
        canCancel(a) {
            return !['cancelled', 'completed'].includes(a.status)
        },
        checkIn(a) {
            this.loading = true;
            this.appointment = a;
            $('#appointmentConfirmModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#appointmentModal').modal('hide');
        },
        editAppointment(appointment){
            this.loading = true;
            this.editMode = true;
            this.appointment = appointment;
            $('#appointmentFormModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true
            this.closeModals();
            axios.get('/api/emr/hims/appointments/'+this.$route.params.id)
            .then(res => {
                this.appointment = res.data.appointment;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Appointment did not load successfully',})
            })
            .finally(() => {
                this.loading = false
            })
        },
        statusClass(status) {
            return {
                'bg-secondary': status === 'pending',
                'bg-primary': status === 'confirmed',
                'bg-success': status === 'checked_in',
                'bg-dark': status === 'completed',
                'bg-danger': status === 'cancelled',
                'bg-warning': status === 'no_show'
            }
        },
        viewAppointment(a) {
            this.$router.push(`/appointments/${a.id}`)
        },
    },
    mounted() {
        this.getAllInitials()
        //this.fetchDepartments()
        //this.fetchAppointments()
    },
    watch: {
        filters: {
            deep: true,
            handler() {
                this.getAllInitials();
            }
        }
    }
}
</script>