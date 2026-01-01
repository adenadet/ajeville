<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <h3 class="profile-username text-center">{{  leave_type.name }}</h3>
                        <p class="text-muted text-center">{{  leave_type.leave_category }}</p>

                        <ul class="list-group list-group-unbordered mb-3 text-left">
                            <li class="list-group-item">
                                <b>Start Date</b> <a class="float-right">{{ ExcelDate(leave_type.start_date) }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>End Date</b> <a class="float-right">{{  ExcelDate(leave_type.end_date) }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b> <a class="float-right">{{ leave_type.status }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="assigned-tab" data-toggle="pill" href="#assigned" role="tab" aria-controls="assigned" aria-selected="true">Assigned</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="request-tab" data-toggle="pill" href="#request" role="tab" aria-controls="request" aria-selected="false">Requests</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-five-tabContent">
                        <div class="tab-pane fade show active p-0" id="assigned" role="tabpanel" aria-labelledby="assigned-tab">
                            <HrmsDetailEmployeeLeaveTypes :leave_type_id.sync="Number($route.params.id)" :leave_type.sync="leave_type"/>
                        </div>
                        <div class="tab-pane fade" id="request" role="tabpanel" aria-labelledby="request-tab">
                            <div class="overlay-wrapper">
                                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                                <HrmsDetailLeaveRequestList :requests.sync="requests" source="admin"/>
                                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
                            </div>
                        </div>
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
            assigned: {},
            current_page: 1,
            editMode: true,
            loading: false,
            leave_type: {},
            requests: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        closeModal(){
            $('#leaveTypeModal').modal('hide');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leave_types/'+this.$route.params.id)
            .then(response => {
                this.refreshLeaveType(response); 
                console.log(this.leave_type.id);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Leave Type did not loaded successfully',
                })
            });
        },
        refreshLeaveType(response) {
            this.leave_type = response.data.leave_type;
            this.requests = response.data.requests;
            this.closeModal();
        }
    },
    props: {}
}
</script>