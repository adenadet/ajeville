<template>
<section>
    <div class="row">
        <div class="col-md-7">
            Put a queue here
        </div>
        <div class="col-md-5">
            View patient details here make summary in tabs.
            1. Use Patient Summary Card
            2. Allergies
            3. Admission Details
            4. Financial Info
            5. EMR History
        </div>
        1. List of Patients with active admission i.e. Clinical discharge = 0
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
        Fire.$on('refreshAppointment', response => {
            this.refreshAppointments(response);
        });
        Fire.$on('refreshPayment', response => {
            this.refreshAppointments(response);
            $('#paymentModal').modal('hide');
        });
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