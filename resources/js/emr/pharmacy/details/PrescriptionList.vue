<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap table-striped">
        <thead>
            <tr>
                <th>Unique ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Requested At</th>
                <th>Emergency</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="prescription in prescriptions">
                <td>{{ prescription.id }}</td>
                <td>{{ prescription.patient != null ? patientName(prescription.patient) : 'Old Patient' }}</td>
                <td>{{ prescription.doctor != null ? FullName(prescription.doctor) : prescription.doctor_name }}</td>
                <td>{{ ExcelDate(prescription.created_at)}}</td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'/emr/pharmacy/prescriptions/'+prescription.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Request</router-link>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td colspan="7">No Prescription meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import EMRPharmacyDetailPrescription from '@/emr/pharmacy/details/Prescription.vue';
import EMRPharmacyFormPrescription from '@/emr/pharmacy/forms/Prescription.vue';
export default {
    components:{
        EMRPharmacyDetailPrescription, EMRPharmacyFormPrescription,
    },
    data() {
        return {
            loading: false,
            editMode: true,
        }
    },
    mounted() {},
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
            this.pending_prescriptions = response.data.pending_prescriptions;
        }
    },
    props: {
        prescriptions:Array,
    },
}
</script>