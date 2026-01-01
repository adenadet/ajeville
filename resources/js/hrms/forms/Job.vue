<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateJob() : createJob()">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Supervisor</label>
                    <select class="form-control" v-model="jobData.supervisor_id">
                        <option value="">---Select Supervisor---</option>
                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{employee.username+' | '+FullName(employee.user)}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Line Manager</label>
                    <select class="form-control" v-model="jobData.reports_to">
                        <option value="">---Select Line Manager---</option>
                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{employee.username+' | '+FullName(employee.user)}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Department</label>
                    <select class="form-control" v-model="jobData.department_id">
                        <option value="">--Select Department --</option>
                        <option v-for="department in departments" :value="department.id">{{ department.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Designation</label>
                    <select class="form-control" v-model="jobData.designation_id">
                        <option value="">---Select Designation---</option>
                        <option v-for="designation in designations" :value="designation.id">{{ designation.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Employment Status</label>
                    <select  class="form-control" v-model="jobData.employment_status">
                        <option value="0">Applicant</option>
                        <option value="1">Active</option>
                        <option value="2">Resigned</option>
                        <option value="3">Terminated</option>
                        <option value="4">Deceased</option>
                        <option value="5">Retired</option>
                    </select> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Joined </label>
                    <input type="date" class="form-control" v-model="jobData.date_of_joining" />
                    <!--div class="form-control">{{ ExcelDate(employee.date_of_joining)}}</div-->
                </div>
            </div>
            <div class="col-md-3" v-if="jobData.employment_status != 1">
                <div class="form-group">
                    <label>Left </label>
                    <input type="date" class="form-control" v-model="jobData.date_of_leaving" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm btn-primary" type="submit">Edit</button>
            </div>
        </div>
    </form>

</section>
</template>
<script>
export default {
    data() {
        return {
            departments: [],
            designations: [],
            employees: [],
            jobData: new Form({
                user_id: '',
                date_of_joining: '',
                date_of_leaving: '',
                department_id: '',
                designation_id: '',
                email: '',
                employee_id: '',
                employment_status: '',
                id: '',
                office_shift_id: '',
                reports_to: '',
                sub_department_id: '',
                supervisor_id: '',
                username: '',
            }),
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createJob(){
            this.loading = true;
            this.jobData.post('/api/hrms/jobs')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Job Post has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/jobs/initials')
            .then(response => {
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your Job Form did not loaded successfully',})
                this.loading = false;
            });
        },
        refreshPage(response){
            this.departments = response.data.departments;
            this.designations = response.data.designations;
            this.employees = response.data.employees;
        },
        updateJob(){
            this.loading = true;
            this.jobData.put('/api/hrms/jobs/'+this.jobData.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Job Post has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        
    },
    props: {
        editMode: Boolean,
        job: Object,
    },
    watch:{
        job(){
            this.jobData.fill(this.job);
        }
    }
}
</script>