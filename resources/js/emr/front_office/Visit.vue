<template>
<section class="container-fluid overlay-wrapper">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>

    <div class="row">
        <div class="col-md-4 no-print">
            <EMRFrontOfficeDetailVisit source="front_office" />
        </div>
        <div class="col-md-8">
            <EMRFinanceDetailPatientTransactions source="visit" />
        </div>
    </div>
</section>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
        };
    },

    watch: {
        '$route.params.id': {
            immediate: true,
            handler() {
                this.loadVisit();
            }
        }
    },

    methods: {
        async loadVisit(page = 1) {
            this.loading = true;
            // Clear previous visit/patient
            this.$store.dispatch('clearVisitContext');
            try {
                const response = await axios.get(`/api/emr/hims/visits/${this.$route.params.id}?page=${page}`);
                this.$store.dispatch('setPatientCookie', response.data.patient);
                this.$store.dispatch('setVisitCookie', response.data.visit);

            } catch (error) {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                });
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>