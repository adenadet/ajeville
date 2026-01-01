<template>
<section class="content">
    <EServiceDetailAppointment :appointment.sync="appointment" source="front_office" @refreshAppointment="getAllInitials"/>
</section>
</template>
<script> 
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
        this.getAllInitials();
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
        getAllInitials() {
            this.loading = true;
            axios.get('/api/emr/consultations/'+this.$route.params.id)
            .then(response => {
                this.loading = false;
                this.refreshAppointment(response);
                this.$emit('refreshAppointment', response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        addPayment(){
            this.$Progress.start();
            this.paySpecific = false;
            Fire.$emit('PaymentDataFill', {});
            Fire.$emit('UnpaidAppointments', this.unpaid_apppointments);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        makePayment(appointment){
            this.$Progress.start();
            this.paySpecific = true;
            Fire.$emit('PaymentDataFill', appointment);
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
            Fire.$emit('ArrivalDataFill', appointment);
            $('#arrivalModal').modal('show');
            this.$Progress.finish();
        },
    },
}
</script>