<template>
    <section class="content-header pt-0">
        <div class="container-fluid">
            <div class="row overlay-wrapper">
                <div class="col-12">
                    <EServiceFormSearch search_type="consultations" @searchedAppointments="refreshConsultations"/>
                    <div class="card">
                        <EServiceDetailAppointmentList source="pending" :appointments.sync="consultations" />
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
            applicant: {},
            consultation: {},
            consultations: {},
            current_page: 1,
            editMode: true,
            nations: [],
            user: {},
        }
    },
    mounted() {
        this.getAllInitials();
        /*Fire.$on('refreshConsultation', response => {
            this.refreshConsultations(response);
        });
        Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('/api/emr/consultations/search?q='+query)
            .then((response ) => {this.applicants = response.data.applicants;})
            .catch(()=>{});
        });
        Fire.$on('refreshAppointment', response => {
            this.refreshConsultations(response);
        });*/
    },
    methods: {
        getAllInitials(page=1) {
            let query = this.$parent.search;
            axios.get('/api/emr/consultations/pending?page='+page+'&query='+query)
            .then(response => {
                this.refreshConsultations(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your consultations did not loaded successfully',
                })
            });
        },
        refreshConsultations(response) {
            this.consultations = response.data.appointments;
            this.nations = response.data.nations;
        },
        issueCertificate(consultation){
            this.consultation = consultation;
            $('#certificateModal').modal('show');
        },
    },
    props: {}
}
</script>