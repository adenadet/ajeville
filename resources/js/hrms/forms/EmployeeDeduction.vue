<template>
<section class="overlay-wrapper p-0">
<div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>  
<form @submit.prevent="editMode ? updateDeduction() : createDeduction()">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Employee</label>
                <div class="form-control" v-if="deduction.employee != null && deduction.employee.user != null">
                    {{ FullName(deduction.employee.user) }}
                </div>
                <model-list-select v-else-if="!editMode" v-model="deductionData.employee_id" :list="employees" optionValue="employee_id" placeholder="Select Supervisor/Exco Lead" :custom-text="codeAndNameAndDesc"></model-list-select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Name</label>
                <input type="text" v-model="deductionData.name" class="form-control" placeholder="Name of Deduction" name="name" id="name" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Month</label>
                <input type="month" v-model="deductionData.month" class="form-control" placeholder="Month to be paid" name="month" id="month"  />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" v-model="deductionData.amount" class="form-control" placeholder="Amount" name="amount" id="amount"  />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Description</label>
                <QuillEditor v-model:content="deductionData.description" content-type="html" theme="snow" placeholder="Description for audit purposes" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-upload mr-1"></i> Submit</button>
        </div>
    </div>
</form>
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data() {
        return {
            deductionData: new Form({
                id: '',
                employee_id: '',
                name: '',
                amount: '',
                description: '',
                month: '',
                type: '',
                start_date: '',
                end_date: '',
                iteration: '',
            }),
            employees: [],
            loading: false,
        }
    },
    emits:['refreshDeductionForm'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createDeduction(){
            this.loading = true;
            this.deductionData.post('/api/hrms/employee_deductions')
            .then(response=>{
                this.$swal.fire('Done!', 'Deduction has been created', 'success');
                this.$emit('refreshDeductionForm');
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
            });
            this.loading = false;   
        },
        codeAndNameAndDesc (item) {
            if (item.user == null){ return item.unique_id+' - Old Staff';}
            return `${item.user.first_name} ${item.user.last_name} [${item.username}] `;
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/employees/initials')
            .then(response => {
                this.staffs = response.data.staffs;
                this.employees = response.data.employees;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your employee form did not loaded successfully',})
                this.loading = false;
            });
        },
        updateDeduction(){
            this.loading = true;
            this.deductionData.put('/api/hrms/employee_deductions/'+this.deductionData.id)
            .then(response=>{
                this.$swal.fire('Done!', 'Deduction has been updated', 'success');
                this.$emit('refreshDeductionForm');
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
            });
            this.loading = false;
        },
    },
    props: {
        deduction: Object,
        editMode: Boolean,
        employee: Object,
    },
    watch:{
        deduction(){
            this.deductionData.fill(this.deduction);
        },
        employee(){
            if (this.editMode == false && this.employee != null){
                this.deductionData.employee_id = this.employee.id;
            }
        }
    }
}
</script>