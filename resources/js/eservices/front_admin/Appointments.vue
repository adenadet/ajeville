<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <EServiceFormSearch search_type="front_admin" @searchedAppointments="refreshAppointments"/>
        <div class="card">
            <EServiceDetailAppointmentList source="front_admin" :appointments.sync="appointments" />
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="appointments.per_page != null ? appointments.per_page : 52" :records="appointments.total != null ? appointments.total : 550" >
                </pagination>
            </div>
        </div>
    </section>
</template>
<script>
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
export default {
    data() {
        return {
            applicant: {},
            appointment: {},
            appointments: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,
            nations: [],
            patients: [],
            services: [],
            user: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        closeModals(){
            $('#appointmentModal').modal('hide');
            $('#patientModal').modal('hide');
            $('#applicantModal').modal('hide');
            $('#receiptModal').modal('hide');
        },
        getAllInitials(page = 1) {
            this.loading = true;
            axios.get('/api/emr/appointments?page='+page)
            .then(response => {this.refreshAppointments(response); this.loading = false;})
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',});
                this.loading = true;
            });
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
            Fire.$emit('AppointmentDataFill', this.appointment);
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        resendAppointment(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "The candidate would get a mail with the confirmation letter",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend confirmation!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.get('/api/emr/registrations/resend/'+id)
                    .then(response=>{
                        //if (response.data.status == 'error')
                        Swal.fire(response.data.status, response.data.message, response.data.status);
                        //this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewPayment(appointment){
            this.appointment = appointment;
            $('#receiptModal').modal('show');
            this.$Progress.finish();
        },
    },
    props: {}
}
</script>