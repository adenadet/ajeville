<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <EMRPatientFormSearch />
        </div>
        <EMRPatientDetailFull v-show="found == 1 || patient.id != null"  />
        <div class="overlay dark" v-if="found != 1 && patient.id"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    </section>
</template>
<script>
export default {
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data() {
        return {
            applicant: {},
            applicants: {},
            appointments: {},
            editMode: true,
            nations: [],
            patients: {},
            services: [],
            user: {},
            found: 0,
            editMode: true,
            loading: false,
        }
    },
    mounted() {
        /*Fire.$on('refresh', response => {
            this.refreshAppointments(response);
            this.closeModal();
        });
        Fire.$on('getPatient', patient_id => {
            this.getPatient(patient_id);
        });*/
    },
    methods: {
        getPatient(id){
            this.found = 1;
        },
    },
    props: {

    },
}
</script>