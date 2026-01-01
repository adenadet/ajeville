<template>
    <section class="p-0">
            <div class="modal fade" id="appointmentModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                            <h4 class="modal-title" v-show="editMode">Edit Appointment</h4>
                            <h4 class="modal-title" v-show="!editMode">New Appointment</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <EServiceFormAppointment :editMode="editMode" :appointment.sync="appointment" @refreshAppointment="refreshAppointments"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="paymentModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                            <h4 class="modal-title">Make Payment</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <EServiceFormPayment :appointment.sync="appointment" :paySpecific.sync="paySpecific" @refreshPayment="refreshAppointments" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="applicantModal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                            <h4 class="modal-title">Create Patient</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <EServiceFormPatient :editMode.sync="editMode" :applicant.sync="applicant" @refreshPatient="refreshAppointments"/> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="receiptModal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                            <h4 class="modal-title">Appointment Receipt</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span class="text-white" aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <EServicePayment :appointment.sync="appointment" /> 
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-header bg-navy">
                <h3 class="card-title">All Appointments</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-success" @click="addApplicant" v-if="source == 'front_admin'"><i class="fa fa-user-plus"></i> Create Applicant</button>
                    <button class="btn btn-sm btn-primary" @click="addAppointment" v-if="source == 'front_admin'"><i class="fa fa-calendar-plus"></i> Book Appointment</button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Service</th>
                            <th>Applicant</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="appointments.data == null || appointments == null || appointments.total == 0">
                        <tr>
                            <td colspan="6" class="text-center">You have not made any appointments yet</td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                            <td>{{addOne(index)}}</td>
                            <td>{{appointment.service_id != null && appointment.service != null ? appointment.service.name : ''}}</td>
                            <td>{{FullName(appointment.patient)}}</td>
                            <td>{{ExcelDate(appointment.date) }}</td>
                            <td>{{appointment.schedule}}</td>
                            <td><span class="tag tag-success">{{appointment.status == 0 ? 'Unpaid' :(appointment.status == 1 ? 'Paid' :(appointment.status == 2 ? 'Reschedule' :(appointment.status == 3 ? 'Cancelled' : (appointment.status == 8 ? 'Certificate Sent' :'Done'))))}}</span></td>
                            <td>
                                <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'front_admin'">
                                    <router-link :to="'/eservices/front_admin/appointment/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Appointment</button></router-link>
                                    <button v-show="appointment.status == 0" class="dropdown-item btn btn-block btn-sm" @click="makePayment(appointment)"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                                    <button v-show="appointment.status == 1" class="dropdown-item btn btn-block btn-sm" @click="viewPayment(appointment)"><i class="fa fa-file-pdf mr-1 text-success"></i> View Receipt</button>
                                    <button v-show="appointment.status <= 1 || appointment.status == null" class="dropdown-item btn btn-default btn-sm" @click="rescheduleAppointment(appointment)"><i class="fa fa-calendar mr-1"></i> Reschedule Appointment</button>
                                    <button v-show="appointment.status == 1" class="dropdown-item btn btn-block btn-sm" @click="resendAppointment(appointment.id)"><i class="fa fa-envelope mr-1 text-warning"></i> Resend Confirmation</button>
                                    <button v-show="appointment.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deleteAppointment(appointment.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                                </div>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else-if="source == 'front_office'">
                                    <router-link :to="'/eservices/front_office/appointment/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Appointment</button></router-link>
                                    <button v-show="appointment.status == 0" class="dropdown-item btn btn-block btn-sm" @click="makePayment(appointment)"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                                    <button v-show="appointment.status == 1" class="dropdown-item btn btn-block btn-sm" @click="viewPayment(appointment)"><i class="fa fa-file-pdf mr-1 text-success"></i> View Receipt</button>
                                    <button v-show="appointment.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deleteAppointment(appointment.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                                </div> 
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'medical_officer'">
                                    <router-link :to="'/eservices/doctor/consultation/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye"></i> View </button></router-link>
                                    <a v-if="appointment.status == 4" :href="'/eservices/doctor/consent/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-edit"></i> Sign Manually</button></a>
                                </div>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else-if="source == 'pending'">
                                    <router-link :to="'/eservices/doctor/consultation/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Appointment</button></router-link>
                                </div>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else-if="source == 'radiologist'">
                                    <router-link :to="'/eservices/radiologist/report/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Appointment</button></router-link>
                                </div>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else-if="source == 'reviews'">
                                    <router-link :to="'/eservices/doctor/consultation/'+appointment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Appointment</button></router-link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            applicant: {},
            appointment: {},
            editMode: true,
            form: new Form({}),
            patients: [],
            paySpecific: false,
            services: [],
            user: {},
        }
    },
    emits:[],
    mounted() {},
    methods: {
        addApplicant(){
            this.loading = true;
            this.editMode = false;
            this.applicant = {};
            $('#applicantModal').modal('show');
            this.loading = false;
        },
        addAppointment(){
            this.loading = true;
            this.editMode = false;
            this.appointment = {};
            this.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.loading = false;
        },
        deleteAppointment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/emr/appointments/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Appointment has been deleted.', 'success');
                        this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        closeModals(){
            $('#appointmentModal').modal('hide');
            $('#patientModal').modal('hide');
            $('#applicantModal').modal('hide');
            $('#receiptModal').modal('hide');
        },
        getInitials(page = 1) {
            axios.get('/api/emr/appointments?page='+page)
            .then(response => {this.refreshAppointments(response)})
            .catch(() => {
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        makePayment(appointment){
            this.loading = true;
            this.paySpecific = true;
            this.appointment = appointment;
            $('#paymentModal').modal('show');
            this.loading = false;
        },
        refreshAppointments(response) {
            this.closeModals();
            this.$emit('refreshAppointment', response);
        },
        rescheduleAppointment(appointment) {
            this.loading = true;
            this.editMode = true;
            this.appointment = appointment;
            $('#appointmentModal').modal('show');
            this.loading = false;
        },
        resendAppointment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The candidate would get a mail with the confirmation letter",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend confirmation!'
                })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/emr/registrations/resend/'+id)
                    .then(response=>{
                        this.$swal.fire(response.data.status, response.data.message, response.data.status);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewPayment(appointment){
            this.appointment = appointment;
            $('#receiptModal').modal('show');
            this.loading = false;
        },
    },
    props: {
        appointments: Object,
        source: String,
    }
}
</script>