<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Prescriptions</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body p-0" style="height:600px;">
                    <EMRPharmacyDetailPrescriptionList :prescriptions.sync="prescriptions.data" />
                </div>
                <!--div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="prescriptions.per_page != null ? prescriptions.per_page : 52" :records="prescriptions.total != null ? prescriptions.total : 550" ></pagination>
                </div-->
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRPharmacyDetailPrescriptionList from '@/emr/pharmacy/details/PrescriptionList.vue';

export default {
    components: {
        EMRPharmacyDetailPrescriptionList
    },
    data() {
        return {
            prescriptions: [],
            editMode: true,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
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
        getInitials(page=1) {
            axios.get('/api/emr/pharmacy/prescriptions?page='+page)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },

        refreshDashboard(response) {
            this.prescriptions = response.data.prescriptions;
        },
    },
    props: {}
}
</script>