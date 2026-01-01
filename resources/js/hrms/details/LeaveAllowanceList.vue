<template>
<div class="modal fade" id="allowanceForm">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h4 class="modal-title">Allowance Form</h4>
                <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
            </div>
            <div class="modal-body p-0">
                <!--HrmsFormLeaveAllowance :editMode.sync="editMode" :leave_request.sync="leave_request" source="mine" @refreshPage="getAllInitials"/-->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmForm">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h4 class="modal-title">Confirm Allowance</h4>
                <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
            </div>
            <div class="modal-body p-0">
                <!--HrmsFormLeaveAllowanceConfirm :editMode.sync="editMode" :leave_allowance.sync="leave_allowance" source="mine" @refreshPage="getAllInitial" /-->
            </div>
        </div>
    </div>
</div>
<div class="card-body  table-responsive p-0" style="height: 700px;">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-striped table-hover">
        <thead class="bg-dark">
            <tr><th>#</th><th>Employee</th><th>Leave Request</th><th>Status</th><th>Amount</th><th></th></tr>
        </thead>
        <tbody v-if="allowances.length != 0">
            <tr v-for="(allowance, index) in allowances">
                <td>{{ addOne(index) }}</td>
                <td>{{ allowance.employee != null ? FullName(allowance.employee.user) : 'Wondering' }}</td>
                <td>{{ allowance.leave_request != null ? FullName(allowance.leave_request) : 'N/A' }}</td>
                <td>{{ allowance.status }}</td>
                <td>{{ currency(allowance.amount) }}</td>
                <td>
                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewAllowance(allowance)"><i class="fa fa-eye mr-1 text-primary"></i> View Allowance</button>
                        <button v-if="source == 'admin'" class="dropdown-item btn btn-block btn-sm" @click="confirmAllowance(allowance)"><i class="fa fa-check mr-1 text-warning"></i> Confirm Allowance</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Allowance has been created</td></tr>
        </tbody>
    </table>
</div>
</template>
<script>
export default {
    data() {
        return {
            allowance: {},
            editMode: false,
            loading: false,
        }
    },
    mounted() {},
    methods: {
        addAllowance(){
            this.allowance = {};
            this.editMode = false;
            $('#allowanceForm').modal('show');
        },
        closeModals(){
            $('#allowanceForm').modal('hide');
            $('#viewAllowance').modal('hide');
        },
        confirmAllowance(allowance){
            this.allowance = allowance;
            this.editMode = false;
            $('#allowanceForm').modal('show');
        },
        viewAllowance(allowance){
            this.allowance = allowance;
            this.editMode = false;
            $('#viewAllowance').modal('show');
        }
    },
    props: {
        allowances: Array,
        source: String,
    },
    watch:{
        allowances(){
            this.loading = true;

            this.loading = false;
        }
    }
}
</script>