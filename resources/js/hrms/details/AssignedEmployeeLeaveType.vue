<template>
<div class="row overlay-wrapper">
    <div class="modal fade" id="leaveTypeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Leave Type</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <!--HrmsFormAssignLeaveTypeMultipleEmployee :editMode.sync="editMode" :leave_type.sync="leave_type" @refreshPage="refreshPage"/-->
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-navy">
                <h3 class="card-title">Employee Leave Assignment</h3>
                <div class="card-tools">
                    <button class="btn btn-xs btn-primary" @click="assignToEmployee()" type="button"><i class="fa fa-plus mr-1"></i> Assign To Employee</button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Leave Type</th>
                            <th>Total No Days</th>
                            <th v-if="source== 'admin'">Employee</th>
                            <th v-if="source== 'admin'">Department</th>
                            <th>Days Used</th>
                            <th>Days Unconfirmed</th>
                            <th>Days Balance</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(assigned_leave_type, index) in assigned_leave_types">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ assigned_leave_type.leave_type.name }}</td>
                            <td>{{ assigned_leave_type.leave_type.no_of_days }}</td>
                            <td v-if="source== 'admin'">{{ assigned_leave_type.employee != null ? FullName(assigned_leave_type.employee.user) : 'Not Found' }}</td>
                            <td v-if="source== 'admin'">{{ assigned_leave_type.employee != null ? assigned_leave_type.employee.department.name : 'No Department' }}</td>
                            <td>{{assigned_leave_type.days_used}}</td>
                            <td>{{assigned_leave_type.pending_days}}</td>
                            <td>{{assigned_leave_type.balance}}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>               
</template>
<script>
export default {
    data() {
        return {
            assigned_leave_types: {},
            editMode: true,
            loading: false,
            //leave_type: {},
        }
    },
    emits:['reloadEmployeeLeaveTypes'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        assignToEmployee(){
            //this.leave_type = {};
            this.editMode = false;
            $('#leaveTypeModal').modal('show');
        },
        closeModal(){
            $('#leaveTypeModal').modal('hide');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/employee_leave_types/'+this.leave_type.id)
            .then(response => {
                this.refreshLeaveType(response); this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your Employee Leave Type did not loaded successfully',
                })
            });
        },
        refreshLeaveType(response) {
            this.assigned_leave_types = response.data.assigned_leave_types;
            this.closeModal();
        },
        refreshPage(){
            this.closeModal();
            this.getAllInitials();
        }
    },
    props: {
        source: String,
        leave_type: Object,
        leave_type_id: Number,
    },
    watch:{
        assigned_leave_types(){

        },
        leave_type(){
            if(this.leave_type.id != null){
                this.getAllInitials();
            }
        }
    }
}
</script>