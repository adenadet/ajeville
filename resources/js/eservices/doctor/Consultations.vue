<template>
    <section class="content-header pt-0">
        <div class="row">
            <div class="col-12">
                <EServiceFormSearch search_type="consultations" @searchedAppointments="refreshConsultations"/>
                <div class="card overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <EServiceDetailAppointmentList source="medical_officer" :appointments.sync="consultations" />
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="consultations.per_page != null ? consultations.per_page : 52" :records="consultations.total != null ? consultations.total : 550" ></pagination>
                    </div>
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
            loading: false,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1){
            axios.get('/api/emr/consultations/?page='+page)
            .then(response => {this.refreshConsultations(response)})
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your consultations did not loaded successfully',
                })
            });
        },
        refreshConsultations(response) {
            this.consultations = response.data.appointments;
            this.nations = response.data.nations;
        }
    },
    props: {}
}
</script>