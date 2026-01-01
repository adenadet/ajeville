<template>
<section class="content-fluid">
    <div class="row">
        <div class="col-md-4">
            <HimsPatientDetailSummary :patient="patient"/>
        </div>
        <div class="col-md-2">
            <HimsPatientDetailPhysician :patient="patient" :physicians="physicians" />
            <HimsPatientDetailContact :patient="patient" :contacts="contacts" />
        </div>
        <div class="col-md-6">
            <HimsPatientDetailConsultations :patient="patient" :consultations="consultations" />
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            appointment: {},
            appointments: {},
            editMode: true,
            nations: [],
            areas: [],
            states: [],
            user: {}
        }
    },
    mounted() {
        this.getInitials();
        /*Fire.$on('refreshAppointment', response => {
            this.refreshAppointments(response);
        });
        Fire.$on('refreshPayment', response => {
            this.refreshAppointments(response);
            $('#paymentModal').modal('hide');
        });*/
    },
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
            //this.applicant = {};
            Fire.$emit('ApplicantDataFill', {});
            $('#applicantModal').modal('show');
            this.$Progress.finish();
        },
        addAppointment(){
            this.$Progress.start();
            this.editMode = false;
            this.appointment = {};
            Fire.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        getInitials() {
            axios.get('/api/emr/appointments').then(response => {
                //this.refreshAppointments(response)
                Fire.$emit('refreshAppointment', response);
            })
                .catch(() => {
                    this.$Progress.fail();
                    toast.fire({
                        icon: 'error',
                        title: 'Your appointments did not loaded successfully',
                    })
                });
        },
        makePayment(appointment){
            this.$Progress.start();
            this.paySpecific = true;
            Fire.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
            this.nations = response.data.nations;
            this.states =  response.data.states;
            this.areas = response.data.areas;
        }
    },
    props: {}
}
</script>