<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="employeeDeductionFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">New Employee Bonus</h4>
                    <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeDeduction :deduction.sync="deduction" :editMode.sync="editMode" @refreshDeductionForm="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">Employee Deduction</h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <HrmsDetailEmployeeDeductionList :deduction.sync="deductions.data" @refreshDeductionList="getAllInitials" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="deductions.per_page != null ? deductions.per_page : 52" :records="deductions.total != null ? deductions.total : 550" ></pagination></div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            deduction: {},
            deductions: {data:[], total:0},
            editMode: false,
            loading: false,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/employee_deductions?type=admin&query='+this.query+'&status='+this.status)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Employee Deductions did not load successfully',});
            });
        }, 
        refreshPage(response){
            this.deductions = response.data.deductions;
        }
    },
}
</script>