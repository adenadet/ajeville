<template>
<section class="overlay-wrapper p-0">
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
        <tbody v-if="requests.length > 0">
            <tr v-for="(request, index) in requests">
                <td>{{ addOne(index) }}</td>
                <td>{{ request.date }}</td>
                <td>{{ patientName(request.patient)  }}</td>
                <td><span class="tag tag-success">Approved</span></td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request)"><i class="fa fa-eye mr-1 text-primary"></i> View Request</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateRequest(request)"><i class="fa fa-edit mr-1 text-warning"></i> Update Request</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="cancelRequest(request)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Request</button>
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
            cancelled_requests: 0,
            completed_requests: 0,
            completed_referred_in: 0,
            completed_referred_out: 0,
            new_requests: 0,
            pending_referred_in: 0,
            pending_referred_out: 0,
            transaction: {},
            transactions: {},
            unpaid_requests: 0,
            editMode: true,
        }
    },
    emits: ['radiologyRequestsRefresh'],
    mounted() {
        //this.getInitials();
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
            //this.$Progress.start();
            this.paySpecific = true;
            //Fire.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            //this.$Progress.finish();
        },
        refreshDashboard(response) {
            this.cancelled_requests = response.data.cancelled_requests;
            this.completed_requests = response.data.completed_requests;
            this.completed_referred_in = response.data.completed_referred_in;
            this.completed_referred_out = response.data.completed_referred_out;
            this.new_requests = response.data.new_requests;
            this.pending_referred_in = response.data.pending_referred_in;
            this.pending_referred_out = response.data.pending_referred_out;
            this.unpaid_requests = response.data.unpaid_requests;
        }
    },
    props: {
        requests: Array,
    }
}
</script>