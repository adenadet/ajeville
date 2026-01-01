<template>
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">OPD Queue</h3>
                    </div>
                    <EMRConsultantDetailQueueList :consultations="consultations.data" type="queue" source="consultant" />
                    <div class="card-footer">
                        <div class="col-12">
                            <pagination v-model="current_page" @paginate="getInitials" :per-page="consultations.per_page != null ? consultations.per_page : 52" :records="consultations.total != null ? consultations.total : 550" ></pagination>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <!--EMRConsultantDetailSummary :consultation="consultation" /--> 
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            consultation: {},
            consultations: {},
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
            axios.get('/api/emr/consultations/consultants').then(response => {
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