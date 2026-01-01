<template>
<section class="content-header pt-0">
    <div class="modal fade" id="requestForm">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body p-0">
                    <HrmsFormLeaveRequest :editMode.sync="editMode" :leave_request.sync="leave_request" source="mine" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card overlay-wrapper">
                <div class="card-header bg-navy">
                    <h3 class="card-title">Leave Requests</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-default" @click="addRequestLeave">Add New Request</button>
                    </div>
                </div>            
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <HrmsDetailLeaveRequestList source="mine" :requests.sync="requests.data" @refreshRequests="getAllInitials"/>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
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
            editMode: false,
            leave_request: {},
            loading: false,
            requests: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addRequestLeave(){
            this.leave_request = {};
            this.editMode = false;
            $('#requestForm').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leaves?type=mine&page='+page)
            .then(response => {
                this.requests = response.data.requests;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your leave requests did not loaded successfully',})
            });
        },
    },
    props: {}
}
</script>