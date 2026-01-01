<template>
<section class="p-0">
    <div class="modal fade" id="confirmRequestFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Line Manager Decision</h4>
                    <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6">
                        <HrmsDetailLeaveRequest :leave_request_id.sync="leave_request_id" :source="source" @refreshPage="refreshList"/>
                    </div>
                    <div class="col-md-6">
                        <HrmsFormLeaveRequestConfirm :leave_request_id.sync="leave_request_id" @refreshPage="refreshList"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="requestFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body p-0">
                    <HrmsFormLeaveRequest :editMode.sync="editMode" :leave_request.sync="leave_request" :source="source" @refreshPage="refreshList"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="requestModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Request Detail</h4>
                    <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <HrmsDetailLeaveRequest :leave_request_id.sync="leave_request_id" :source="source" @refreshPage="refreshList"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0" style="height: 600px;">
        <table class="table table-hover table-head-fixed text-nowrap table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Staff</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Requested On</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="requests == null">
                <tr><td colspan="8" class="text-center">You have not made any requests yet</td></tr>
            </tbody>
            <tbody v-else>
                <tr v-for="(request, index) in requests" :key="request.id">
                    <td>{{addOne(index)}}</td>
                    <td>{{request.employee != null ? FullName(request.employee.user) : 'Deactivated Staff'}}</td>
                    <td>{{request.leave_type_id != null && request.leave_type != null ? request.leave_type.name : ''}}</td>
                    <td>{{ExcelDate(request.from_date) }}</td>
                    <td>{{ExcelDate(request.to_date) }}</td>
                    <td>{{ExcelDate(request.updated_at) }}</td>
                    <td>{{request.status == 0 ? 'Unapproved' : 
                            (request.status == 2 ? 'Completed' :
                            (request.status == 1 ? (
                                dateCompareToday(request.from_date, '>') ? 'Approved'  
                                    :(dateCompareToday(request.from_date, '<=') 
                                        ? (dateCompareToday(request.end_date, '>=') ? 'Ongoing' : 'Completed')
                                        : 'Completed')
                                )
                            :(request.status == 4 ? 'Rejected ': (request.status == 10 ? 'Cancelled ': 'Old Status'))
                            )
                        )}}</td>
                    <td>
                        <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v text-dark"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'admin'">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request.id)"><i class="fa fa-eye mr-1 text-primary"></i> View request</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="createAllowance(request)"><i class="fa fa-eye mr-1 text-warning"></i> Create Allowance request</button>
                            <button v-if="request.status < 1" class="dropdown-item btn btn-block btn-sm" @click="confirmRequest(request)"><i class="fa fa-check mr-1 text-warning"></i> Confirm request</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'mine'">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request.id)"><i class="fa fa-eye mr-1 text-primary"></i> View request</button>
                            <button v-if="request.status < 1 || request.status > 6" class="dropdown-item btn btn-block btn-sm" @click="editRequest(request)"><i class="fa fa-edit mr-1 text-warning"></i> Edit request</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'team'">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request.id)"><i class="fa fa-eye mr-1 text-primary"></i> View request</button>
                            <button v-if="request.status < 1" class="dropdown-item btn btn-block btn-sm" @click="confirmRequest(request)"><i class="fa fa-check mr-1 text-warning"></i> Confirm request</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            form: new Form({}),
            leave_request: {},
            leave_request_id: 0,
            user: {},
        }
    },
    emits:['refreshRequests'],
    mounted() {},
    methods: {
        closeModals(){
            $('#confirmRequestFormModal').modal('hide');
            $('#requestFormModal').modal('hide');
            $('#requestModal').modal('hide');
            $('#applicantModal').modal('hide');
            $('#receiptModal').modal('hide');
        },
        confirmRequest(request){
            this.leave_request = request;
            this.leave_request_id = request.id;
            $('#confirmRequestFormModal').modal('show');
        },
        deleteAppointment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/emr/appointments/'+id)
                    .then(response=>{this.$swal.fire('Deleted!', 'Appointment has been deleted.', 'success');})
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});});
                }
            });
        },
        editRequest(request){
            this.leave_request = request;
            this.editMode = true;
            $('#requestFormModal').modal('show');
        },
        refreshList(){
            this.closeModals();
            this.$emit('refreshRequests');
            $('#requestFormModal').modal('hide');
        },
        requestLeave(){
            this.leave_request = {};
            this.editMode = false;
            $('#requestFormModal').modal('show');
        },
        viewRequest(id){
            this.leave_request_id = id;
            $('#requestModal').modal('show');
        }
    },
    props: {
        requests: Array,
        source: String,
    },
    watch:{
        source(){
            if (source == 'mine'){}
        }
    }
}
</script>