<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ firstUp(action) }} Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRRadiologyFormAction :action.sync="action" :request.sync="request" />
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">            
            <h3 class="profile-username text-center">{{ patientName(request?.patient) }}</h3>
            <p class="text-muted text-center">{{ ExcelDate(request?.date) }}</p>
            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Request By:</b> <span class="float-right">{{ FullName(request?.creator) }}</span></li>
                <li class="list-group-item"><b>Request At:</b> <span class="float-right">{{ request?.created_at }}</span></li>
                <li class="list-group-item" v-if="request?.collector != null"><b>Taken By:</b> <span class="float-right">{{ FullName(request?.collector) }}</span></li>
                <li class="list-group-item" v-if="request?.reporter != null"><b>Reported By:</b> <span class="float-right">{{ FullName(request?.reporter) }}</span></li>
                <li class="list-group-item" v-if="request?.secondary_reporter != null"><b>Secondary Report By:</b> <span class="float-right">{{ FullName(request?.secondary_reporter)}}</span></li>
                <li class="list-group-item"><b>Status:</b> 
                    <span class="float-right badge badge-warning" v-if="request?.status == 0">Unconfirmed</span>
                    <span class="float-right badge badge-primary" v-if="request?.status == 1">Confirmed</span>
                    <span class="float-right badge badge-info" v-if="request?.status == 2">Sample Collected</span>
                    <span class="float-right badge badge-info" v-if="request?.status == 5">Referred Out</span>
                    <span class="float-right badge badge-info" v-if="request?.status == 10">Reported</span>
                    <span class="float-right badge badge-info" v-if="request?.status == 13">Awaiting Secondary Report</span>
                    <span class="float-right badge badge-info" v-if="request?.status == 15">Secondary Reported</span>
                    <span class="float-right badge badge-success" v-if="request?.status == 20">Approved</span>
                    <span class="float-right badge badge-danger" v-if="request?.status == 100">Cancelled</span>
                </li>
            </ul>
            <button v-if="request?.status == 0" @click="makePayment(request)" class="btn btn-primary btn-block"><b><i class="fa fa-check mr-1"></i> Get Approval</b></button>
            <button v-if="request?.status == 1" @click="collectSample(request)" class="btn btn-block bg-purple"><b><i class="fa fa-x-ray mr-1"></i> Collect Sample</b></button>
            <button v-if="request?.status == 2" @click="reportRequest(request)" class="btn btn-info btn-block"><b><i class="fa fa-clipboard mr-1"></i> Report Test</b></button>
            <button v-if="request?.status == 13" @click="secondaryReportRequest(request)" class="btn btn-info btn-block"><b><i class="fa fa-paste mr-1"></i> Report Test (Secondary)</b></button>
            <button v-if="request?.status == 10 && request?.status < 100" @click="approveReport(request)" class="btn btn-success btn-block"><b><i class="fa fa-check-double mr-1"></i>Approve Test Report </b></button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            loading: false,
        }
    },
    emits: ['radiologyRequestRefresh'],
    mounted() {
    },
    methods: {
        collectSample(){
            this.loading = true;
            this.editMode = false;
            $('#sampleModal').modal('show');
            this.loading = true;
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
            axios.get('/api/emr/radiology/requests/'+this.request_id)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error', title: 'Requests did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        makePayment(appointment){
            this.paySpecific = true;
            $('#paymentModal').modal('show');
        },
        refreshDashboard(response) {
            this.request = response.data.request;
        }
    },
    props: {
        request: Object,
    }
}
</script>