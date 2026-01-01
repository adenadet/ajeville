<template>
<form @submit.prevent="assignManager()">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>User</label>
                <div class="form-control">
                    {{ FullName(employee.user) }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Supervisor</label>
                <model-list-select v-model="employeeData.supervisor_id" :list="staffs" optionValue="employee_id" placeholder="Select Supervisor/Exco Lead" :custom-text="codeAndNameAndDesc"></model-list-select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Line Manager</label>
                <model-list-select v-model="employeeData.reports_to" :list="staffs" optionValue="employee_id" placeholder="Select Line Manager" :custom-text="codeAndNameAndDesc"></model-list-select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-upload mr-1"></i> Submit</button>
        </div>
    </div>
</form>
</template>
<script>
export default {
    data() {
        return {
            employeeData: new Form({
                id: '',
                reports_to: '',
                supervisor_id: '',
            }),
            staffs: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        assignManager(){
            this.loading = true;
            this.employeeData.put('/api/hrms/employees/assign_manager/'+this.employeeData.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Employee has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        codeAndNameAndDesc (item) {
            if (item.user == null){ return item.username+' - Old Staff';}
            return `${item.username} - ${item.user.first_name} ${item.user.last_name}`;
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/employees/initials')
            .then(response => {
                this.staffs = response.data.staffs;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your employee form did not loaded successfully',})
                this.loading = false;
            });
        },
    },
    props: {
        editMode: Boolean,
        employee: Object,
    },
    watch:{
        employee(){
            this.employeeData.fill(this.employee);
            if (this.employee.user == null){
                this.employeeData.user = {
                    alt_phone:'', 
                    area_id:'', 
                    branch_id:'', 
                    city:'', 
                    department_id:'', 
                    dob:'', 
                    email:'',
                    first_name: '', 
                    id:'', 
                    image:'', 
                    joined_at: '',
                    unique_id: '',
                    last_name:'', 
                    middle_name:'', 
                    password:'', 
                    personal_email: '', 
                    phone:'', 
                    roles:'',
                    sex:'', 
                    state_id:'', 
                    street:'', 
                    street2:'',
                    unique_id: '',           
                };
            }
        }
    }
}
</script>