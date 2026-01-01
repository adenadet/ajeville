<template>
<div class="card">
    <div class="card-header bg-navy">
        <h3 class="card-title">Assigned Leaves</h3>
        <div class="card-tools">
        </div>
    </div>
    <div class="card-body  p-0">
        <table class="table table-hover table-striped">
            <thead class="bg-dark">
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Leave Type</th>
                    <th>Validity</th>
                    <th>Used</th>
                    <th>Balance</th>
                    <th>Pending</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="leave_types != null && leave_types.length != null">
                <tr v-for="(employee_leave_type, index) in leave_types">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ employee_leave_type.leave_type != null ? employee_leave_type.leave_type.name : 'Old Leave Type' }}</td>
                    <td>{{ employee_leave_type.leave_type != null ? employee_leave_type.leave_type.start_date+" - "+ employee_leave_type.leave_type.end_date: 'Undefined'}}</td>
                    <td>{{ employee_leave_type.days_used }}</td>
                    <td>{{ employee_leave_type.balance }}</td>
                    <td>{{ employee_leave_type.pending_days }}</td>
                    <td>{{ employee_leave_type.leave_type != null && employee_leave_type.leave_type.end_date <= today ? 'Expired' : 'Available'  }}</td>
                    <td>
                        <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                            <i class="fa text-small fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewLeaveType(employee_leave_type)"><i class="fa fa-eye mr-1 text-primary"></i> View Requests</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="cancelLeaveType(employee_leave_type)"><i class="fa fa-user mr-1 text-danger"></i> Cancel Leave Type</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</template>
<script>
export default {
    computed:{
        today(){
            return new Date().toJSON().slice(0, 10);
        }
    },
    data() {
        return {
            editMode: false,
            editting: true,
            employee_leave_type: {},
            loading: false,
        }
    },
    emits:['employeeReload'],
    mounted() {},
    methods: {
        cancelLeaveType(employee_leave_type){

        },
        closeModal(){
            $('#cancelModal').modal('hide');
            $('#requestModal').modal('hide');
        },
        viewLeaveType(employee_leave_type){},
    },
    props: {
        leave_types: Array,
        source: String,
    },
    watch:{
        employee(){

        }
    }
}
</script>