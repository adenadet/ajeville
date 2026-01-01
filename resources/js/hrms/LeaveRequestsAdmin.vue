<template>
<section class="content-header pt-0">
    <div class="modal fade" id="leaveRequestFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body p-0">
                    <HrmsFormLeaveRequest :editMode.sync="editMode" :leave_request.sync="leave_request" source="admin" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uploadFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body p-0">
                    <HrmsFormLeaveRequestImport :editMode.sync="editMode" :leave_request.sync="leave_request" source="mine" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card overlay-wrapper">
                <div class="card-header bg-navy">
                    <h3 class="card-title">All Requests</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 350px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-default"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-sm btn-primary" @click="addLeaveRequest"><i class="fas fa-plus"></i></button>
                                <button type="button" class="btn btn-sm btn-info" @click="uploadLeaveRequest"><i class="fas fa-upload"></i></button>
                            </div>
                        </div>
                    </div>
                </div>        
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <HrmsDetailLeaveRequestList source="admin" :requests.sync="requests" />
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
            request: {},
            requests: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addLeaveRequest(){
            this.loading = true;
            $('#leaveRequestFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#leaveRequestFormModal').modal('hide');
            $('#uploadFormModal').modal('hide');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leaves?type=all&page='+page)
            .then(response => {
                this.refreshRequests(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your leave requests did not loaded successfully',
                })
            });
        },
        refreshRequests(response) {
            this.requests = response.data.requests;
        },
        uploadLeaveRequest(){
            this.loading = true;
            $('#uploadFormModal').modal('show');
            this.loading = false;
        },
    },
    props: {}
}
</script>
