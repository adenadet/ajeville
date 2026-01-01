<template>
<section class="overlay-wrapper p-0">
    {{ referrals }}
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Patient</th>
                <th>Category</th>
                <th>Item</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody v-if="referrals.length > 0">
            <tr v-for="(referral, index) in referrals">
                <td>{{ addOne(index) }}</td>
                <td>{{ referral.date }}</td>
                <td>{{ patientName(referral.patient)  }}</td>
                <td><span class="tag tag-success">Approved</span></td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(referral)"><i class="fa fa-eye mr-1 text-primary"></i> View Request</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateRequest(referral)"><i class="fa fa-edit mr-1 text-warning"></i> Update Request</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="cancelRequest(referral)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Request</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Request meets your criteria</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            referral: {},
        }
    },
    emits: ['radiologyReferralsRefresh'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        addApplicant(){
            this.loading = true;
            this.editMode = false;
            $('#applicantModal').modal('show');
            this.loading = false;
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
            axios.get('/api/emr/radiology/dashboard?page='+page)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                //this.$Progress.fail();
                this.$toast.fire({
                    icon: 'error', title: 'Your appointments did not loaded successfully',
                })
            });
        },
        makePayment(appointment){
            this.paySpecific = true;
            $('#paymentModal').modal('show');
        },
        refreshDashboard(response) {
            
        }
    },
    props: {
        referrals: Array,
        source: String,
    },
    watch:{
        referrals(){
            
        }
    }
}
</script>