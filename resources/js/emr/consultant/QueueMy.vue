<template>
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-9 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h3 class="card-title">OPD Queue</h3>
                        </div>
                        <ConsultantDetailQueue :consultations="consultations" source="consultant" />
                    </div>
                </div>
                <div class="col-lg-3">
                    <ConsultantDetailSummary :consultation="consultation" :patient="consultation.patient"/> 
                </div>
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
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/consultations/consultants/my_queue')
            .then(response => {
                this.refreshAppointments(response);        
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
            this.consultations = response.data.consultations;
            this.nations = response.data.nations;
            this.states =  response.data.states;
            this.areas = response.data.areas;
        }
    },
}
</script>