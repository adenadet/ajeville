<template>
<section class="container-fluid overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        
    <div class="row">
        <div class="col-md-4 no-print">
            <EMRVisitDetailSummary :visit="visit" :patient="patient"/>
        </div>
        <div class="col-md-8">
            <FinanceDetailPatientTransactions source="visit" />
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            visit: {},
            transactions: [],
            patient: {},
            loading: false,
        }
    },
    mounted() {
        this.getAllInitials();
        /*Fire.$on('visitUpdated', () =>{
            this.closeModal();
        });*/
    },
    methods: {
        closeModal(){
            $('#paymentModal').modal('hide');
        },
        getAllInitials(page=1) {
            this.loading = true;
            axios.get('/api/emr/hims/visits/'+this.$route.params.id+'?page='+page).then(response => {
                this.refresh(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
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