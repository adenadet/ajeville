<template>
    <section class="content-header p-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-navy">
                            <h3 class="card-title">All Appointments</h3>
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
                                <tbody v-if="appointments.data == null || appointments == null">
                                    <tr>
                                        <td colspan="6" class="text-center">You have not made any appointments yet</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                                        <td>{{ addOne(index)  }}</td>
                                        <td>{{ appointment.service_id != null && appointment.service != null ?
                                                appointment.service.name : ''
                                        }}</td>
                                        <td>{{ FullName(appointment.patient)}}</td>
                                        <td>{{ ExcelDate(appointment.date)  }}</td>
                                        <td>{{ appointment.schedule }}</td>
                                        <td><span class="tag tag-success">{{ appointment.status == 0 ? 'Unpaid' : (appointment.status == 1 ? 'Paid' : (appointment.status == 2 ? 'Reschedule' : (appointment.status == 3 ? 'Cancelled' : (appointment.status == 8 ? 'Certificate Sent' : 'Done'))))}}</span></td>
                                        <td>
                                            <div class="btn btn-group">
                                                <router-link
                                                    :to="'/eservices/front_office/appointment/' + appointment.id"><button
                                                        class="btn btn-primary btn-sm"><i
                                                            class="fa fa-eye"></i></button></router-link>
                                                <button v-show="appointment.status == 0" class="btn btn-success btn-sm"
                                                    @click="makePayment(appointment)"><i
                                                        class="fa fa-credit-card"></i></button>
                                                <button v-show="appointment.status > 0"
                                                    class="btn btn-success btn-sm"><i
                                                        class="fa fa-file-pdf"></i></button>
                                                <button v-show="appointment.status <= 1 || appointment.status == null" class="btn btn-warning btn-sm" @click="rescheduleAppointment(appointment)"><i class="fa fa-calendar"></i></button>
                                                <button v-show="appointment.status == 0" class="btn btn-danger btn-sm"
                                                    @click="deleteAppointment(appointment.id)"><i
                                                        class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="payments.per_page != null ? payments.per_page : 52" :records="payments.total != null ? payments.total : 550" >
                            </pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            applicant: {},
            appointment: {},
            appointments: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            nations: [],
            patients: [],
            payments:{},
            services: [],
            user: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addApplicant() {
            this.editMode = false;
            this.$emit('ApplicantDataFill', {});
            $('#applicantModal').modal('show');
        },
        addAppointment() {
            this.editMode = false;
            this.appointment = {};
            this.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
        },
        deleteAppointment(id){
            Swal.fire({
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
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials(page = 1) {
            this.loading = true;
            axios.get('/api/emr/appointments/missed')
                .then(response => { this.refreshAppointments(response); this.loading = false; })
                .catch(() => {
                    this.loading = false;
                    toast.fire({ icon: 'error', title: 'Your appointments did not loaded successfully', })
                });
        },
        makePayment(appointment) {
            this.$Progress.start();
            this.paySpecific = true;
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
            this.services = response.data.services;
            this.nations = response.data.nations;
            this.patients = response.data.patients;
        },
        rescheduleAppointment(appointment) {
            this.$Progress.start();
            this.editMode = true;
            this.appointment = appointment;
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
    },
    props: {}
}
</script>