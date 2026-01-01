<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="deductionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">Employee Deduction Detail</h4>
                    <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <HrmsDetailEmployeeDeduction :deduction.sync="deduction" :editMode.sync="editMode" @refreshDeductionForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deductionFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">New Employee Deduction</h4>
                    <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeDeduction :deduction="deduction" :editMode="editMode" @refreshDeductionForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Employee</th>
                <th>Deduction Name</th>
                <th>Month</th>
                <th>Amount</th>
                <th>Deduction Type</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="true">
            <tr v-for="(deduction, index) in deductions" :key="deduction.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ deduction.employee != null ? FullName(deduction.employee.user) : 'No Employee' }}</td>
                <td>{{ deduction.name }}</td>
                <td>{{ ExcelMonthYear(deduction.month) }}</td>
                <td>{{ currency(deduction.amount) }}</td>
                <td>{{ currency(deduction.type) }}</td>
                <td :title="deduction.description" v-html="readMore(deduction.description, 60, '...')"></td>
                <td>
                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                        <i class="fa text-small fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewEmployeeDeduction(deduction)"><i class="fa fa-eye mr-1 text-primary"></i> View Employee Deduction</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateEmployeeDeduction(deduction)"><i class="fa fa-edit mr-1 text-warning"></i> View Employee Deduction</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateEmployeeDeduction(deduction.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Employee Deduction</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Deductiones meet your criteria</td>
            </tr>
        </tbody>
    </table>
</section>
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
            deduction: {},
            editMode: false,
            loading: false,
        }
    },
    emits:['refreshDeductionList'],
    mounted() {},
    methods: {
        addDeduction(){
            this.loading = true;
            this.deduction = {};
            $('#deductionFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#deductionModal').modal('hide');
            $('#deductionFormModal').modal('hide');
        },
        deactivateEmployeeDeduction(id){
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
                    this.loading = true;
                    this.form.delete('/api/hrms/employee_deductions/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(){
            this.loading = true;
            this.closeModals();
            this.$emit('refreshDeductionList');
            this.loading = false;
        },
        updateEmployeeDeduction(deduction){
            this.loading = true;
            this.editMode = true;
            this.deduction = deduction;
            $('#deductionFormModal').modal('show');
            this.loading = false;
        },
        viewEmployeeDeduction(deduction){
            this.loading = true;
            this.deduction = deduction;
            $('#deductionModal').modal('show');
            this.loading = false;
        },
    },
    props: {
        deductions: Array,
        employee: Object,
        source: String,
    },
    watch:{
        deductions(){
            alert(this.deductions.length)
        }
    },
}
</script>