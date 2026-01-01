<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Prescriptions</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                <th style="width: 10px">#</th>
                                <th>Unique ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Requested At</th>
                                <th>Label</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prescription, index) in prescriptions">
                                    <td>{{ index | addOne }}</td>
                                    <td>{{ prescription.id }}</td>
                                    <td>{{ prescription.patient != null ? (prescription.patient | patientName) : 'Old Patient' }}</td>
                                    <td>{{ prescription.doctor != null ? (prescription.doctor | fullName) : prescription.doctor_name }}</td>
                                    <td>{{ prescription.created_at | excelDate }}</td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <router-link :to="'/pharmacy/prescriptions/'+prescription.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Request</router-link>
                                            <!--button v-if="request.status == 0" class="btn btn-block dropdown-item"><i class="fas fa-cash-register mr-2"></i> Receive Deposit</button-->
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import VueBootstrapTable from 'vue-bootstrap-table2';

export default {
    components: {
        VueBootstrapTable
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