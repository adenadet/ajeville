<template>
    <section class="content-header pt-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <EServiceFormSearch search_type="consultations" @searchedAppointments="refreshConsultations"/>
                    <div class="card">
                        <EServiceDetailAppointmentList source="reviews" :appointments.sync="consultations" />
                        <div class="card-footer">
                            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="consultations.per_page != null ? consultations.per_page : 52" :records="consultations.total != null ? consultations.total : 550" ></pagination>
                        </div>
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
            consultation: {},
            consultations: {},
            current_page: 1,
            editMode: true,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1) {
            this.loading = true
            axios.get('/api/emr/consultations/reviews?page='+page)
            .then(response => {
                this.refreshConsultations(response)
            })
            .catch(() => {
                this.loading = true;
                this.$toast.fire({icon: 'error', title: 'Your consultations did not loaded successfully',});
            });
        },
        refreshConsultations(response) {
            this.consultations = response.data.appointments;
            this.nations = response.data.nations;
        },
    },
    props: {}
}
</script>