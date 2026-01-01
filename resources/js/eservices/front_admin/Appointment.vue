<template>
<section class="content">
    <EServiceDetailAppointment :appointment.sync="appointment" source="front_admin" />
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
            editMode: true,
            nations: [],
            report: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        editApplicant(patient){
            this.$Progress.start();
            this.editMode = true;
            this.applicant = patient;
            this.$emit('ApplicantDataFill', patient);
            $('#applicantModal').modal('show');
            this.$Progress.finish();
        },
        getInitials() {
            axios.get('/api/emr/appointments/'+this.$route.params.id)
            .then(response => {
                this.refreshAppointment(response);
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        addPayment(){
            this.$Progress.start();
            this.paySpecific = false;
            //this.unpaid_appointments;
            this.$emit('PaymentDataFill', {});
            this.$emit('UnpaidAppointments', this.unpaid_apppointments);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        makePayment(appointment){
            this.$Progress.start();
            this.paySpecific = true;
            this.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshAppointment(response) {
            this.appointment = response.data.appointment;
            this.nations = response.data.nations;
            this.report = response.data.appointment.report;

            $('#applicantModal').modal('hide');
            $('#appointmentModal').modal('hide');
            $('#arrivalModal').modal('hide');
        },
        to_doctor(appointment){
            this.$Progress.start();
            //this.editMode = false;
            this.appointment = appointment;
            this.$emit('ArrivalDataFill', appointment);
            $('#arrivalModal').modal('show');
            this.$Progress.finish();
        },
    },
}
</script>