<template>
    <section class="container-fluid">
        Put An Admission Form Here
        List of Things to add:
        Admitting doctor
        Admission Reason
        Admission Admission Note
        Admission Doctor Note
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
            loading: false,
        }
    },
    mounted() {
        //this.getAllInitials();
        Fire.$on('visitUpdated', () =>{
            this.closeModal();
        });
    },
    methods: {
        closeModal(){
            $('#paymentModal').modal('hide');
        },
        getAllInitials(page=1) {
            this.$Progress.start();
            this.loading = true;
            axios.get('/api/emr/hims/visits/'+this.$route.params.id+'?page='+page).then(response => {
                this.refresh(response);
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        sortStaff(){},
        refresh(response) {
            this.$store.dispatch('setPatientCookie', response.data.patient);
            this.$store.dispatch('setVisitCookie', response.data.visit);
        },
    },
    props: {
        editMode: Boolean,
    }
}
</script>