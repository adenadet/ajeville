<template>
<form @submit.prevent="editMode ? updateEmployee() : createEmployee()">
    <div class="row" v-if="employee.user == null">
        <div class="col-md-12" v-if="editMode">
            <div class="form-group">
                <label>User</label>
                <div class="form-control">
                    {{ FullName(employee.user) }}
                </div>
            </div>
        </div>
        <div class="col-md-12" v-else-if="!editMode">
            <div class="form-group">
                <label>Create from </label>
                <select class="form-control" v-model="employeeData.from_user">
                    <option value="existing">Existing User</option>
                    <option value="new">New User</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" v-if="employeeData.from_user == 'existing'">
        <div class="col-md-12">
            <div class="form-group">
                <label>Select User</label>
                <select v-if="!editMode" class="form-control" v-model="employeeData.user_id" name="user_id" id="user_id">
                    <option value="">--Select Users--</option>
                    <option v-for="user in inactive_users" :value="user.id">{{FullName(user)}}</option>
                </select>
            </div>
        </div>    
    </div>
    <div class="row" v-else-if="employeeData.from_user == 'new'">
        <div class="col-sm-4">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="employeeData.user.first_name" :class="{'is-invalid' : employeeData.errors.has('first_name') }">
                <has-error :form="employeeData" field="first_name"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="employeeData.user.middle_name" :class="{'is-invalid' : employeeData.errors.has('middle_name') }"/>
                <has-error :form="employeeData" field="middle_name"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Last Name*</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="employeeData.user.last_name" :class="{'is-invalid' : employeeData.errors.has('last_name') }" />
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Address*</label>
                <input type="text" class="form-control" id="street" name="street" placeholder="Enter Address *" required v-model="employeeData.user.street" :class="{'is-invalid' : employeeData.errors.has('street') }" />
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Address2</label>
                <input type="text" class="form-control" id="street2" name="street2" placeholder="Enter Street Desc" v-model="employeeData.user.street2" :class="{'is-invalid' : employeeData.errors.has('street2') }">
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>City*</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Enter City *" v-model="employeeData.user.city" :class="{'is-invalid' : employeeData.errors.has('city') }">
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>State</label>
                <select class="form-control" id="state_id" name="state_id" placeholder="Enter State / County *" required v-model="employeeData.user.state_id" :class="{'is-invalid' : employeeData.errors.has('state_id') }" @change="updateAreas()">
                    <option value="">--Select State--</option>
                    <option v-for="state in states" v-bind:key="state.id" :value="state.id" >{{state.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>LGA</label>
                <select class="form-control" id="area_id" name="area_id" required v-model="employeeData.user.area_id" :class="{'is-invalid' : employeeData.errors.has('area_id') }">
                    <option value="">--Select area--</option>
                    <option v-for="area in areas" v-bind:key="area.id" :value="area.id" >{{area.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="employeeData.user.phone" :class="{'is-invalid' : employeeData.errors.has('phone') }">
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Personal Email Address</label>
                <input type="email" class="form-control" id="personal_email" name="personal_email" placeholder="Enter Email Address *" required v-model="employeeData.user.personal_email" :class="{'is-invalid' : employeeData.errors.has('personal_email') }">
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Sex</label>
                <select class="form-control" id="sex" name="sex" required v-model="employeeData.user.sex" :class="{'is-invalid' : employeeData.errors.has('sex') }">
                    <option value="">---Select Sex---</option>
                    <option value="Female">Female</option>
                    <option value="Male">Male</option>
                </select>
            </div>
        </div>
        <div class="col-md-5 col-sm-12">
            <label>Date of Birth</label>
            <div class="form-group">
                <input name="dob" id="dob" type="date" data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="Birth Date" v-model="employeeData.user.dob" :class="{'is-invalid' : employeeData.errors.has('dob') }">
            </div>
        </div>
        <div class="col-md-7 col-sm-12">
            <label>Profile Pic</label>
            <div class="form-group">
                <input type="file" class="form-control" placeholder="Birth Date" @change="updateProfilePic">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Supervisor</label>
                <select v-if="!editMode" class="form-control" v-model="employeeData.supervisor_id" name="supervisor_id" id="supervisor_id">
                    <option value="">--Select Users--</option>
                    <option v-for="employee in employees" :value="employee.id">{{FullName(employee.user)}}</option>
                </select>
                <!--model-list-select v-model="employeeData.supervisor_id" :list="staffs" optionValue="employee_id" placeholder="Select Supervisor/Exco Lead" :custom-text="codeAndNameAndDesc"></model-list-select-->
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Line Manager</label>
                <select v-if="!editMode" class="form-control" v-model="employeeData.reports_to" name="reports_to" id="reports_to">
                    <option value="">--Select Users--</option>
                    <option v-for="employee in employees" :value="employee.id">{{FullName(employee.user)}}</option>
                </select>
                <!--model-list-select v-model="employeeData.reports_to" :list="staffs" optionValue="employee_id" placeholder="Select Line Manager" :custom-text="codeAndNameAndDesc"></model-list-select-->
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Department {{ employee.department_id }}</label>
                <select class="form-control" v-model="employeeData.department_id">
                    <option value="">--Select Department --</option>
                    <option v-for="department in departments" :value="department.id">{{ department.name }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Designation</label>
                <select class="form-control" v-model="employeeData.designation_id">
                    <option value="">--Select Designation--</option>
                    <option v-for="designation in designations" :value="designation.id">{{ designation.name }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Employment Status</label>
                <select class="form-control" v-model="employeeData.employment_status">
                    <option value="">--Select Employment Status--</option>
                    <option value="0">Inactive</option>
                    <option value="1">Active</option>
                    <option value="2">Resigned</option>
                    <option value="3">Terminated</option>
                    <option value="4">Deceased</option>
                    <option value="5">Retired</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Official Email </label>
                <input type="email" class="form-control" v-model="employeeData.email" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Staff ID </label>
                <input type="text" class="form-control" v-model="employeeData.username" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Joined </label>
                <input type="date" class="form-control" v-model="employeeData.date_of_joining" />
            </div>
        </div>
        <div class="col-md-3" v-if="employeeData.employment_status != 1">
            <div class="form-group">
                <label>Left </label>
                <input type="date" class="form-control" v-model="employeeData.date_of_leaving" required/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary" type="submit">Edit</button>
        </div>
    </div>
</form>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    data() {
        return {
            areas: [],
            departments: [],
            designations: [],
            employees: [],
            employeeData: new Form({
                user:{
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
                },
                date_of_joinnig: '',
                date_of_leaving: '',
                department_id: '',
                designation_id: '',
                email: '',
                employee_id: '',
                employment_status: '',
                from_user: '',
                id:'', 
                office_shift_id: '',
                reports_to: '',
                sub_department_id: '',
                supervisor_id: '',
                username: '',
                user_id: '',
            }),
            inactive_users: [],
            loading: false,
            staffs: [],
            states: [],
            users: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        codeAndNameAndDesc (item) {
            if (item.user == null){ return item.username+' - Old Staff';}
            return `${item.username} - ${item.user.first_name} ${item.user.last_name}`
        },
        createEmployee(){
            this.loading = true;
            this.employeeData.post('/api/hrms/employees')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Leave Request has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/employees/initials')
            .then(response => {
                this.refreshForm(response); this.loading = false;       
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your employee form did not loaded successfully',})
                this.loading = false;
            });
        },
        nameAndDesc (item) {
            return `${item.first_name} ${item.last_name}`
        },
        refreshForm(response){
            this.areas = response.data.areas;
            this.departments = response.data.departments;
            this.designations = response.data.designations;
            this.employees = response.data.employees;
            this.inactive_users = response.data.inactive_users;
            this.staffs = response.data.staffs;
            this.states = response.data.states;
        },
        updateAreas(){
            var state = this.states.map(e => e.id).indexOf(this.employeeData.user.state_id);
            this.areas = this.states[state].areas;
        },
        updateEmployee(){
            this.loading = true;
            this.employeeData.put('/api/hrms/employees/'+this.employeeData.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false; this.$swal.fire({icon: 'success', title: 'The Employee has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false; this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.employeeData.user.image = reader.result;
                    //console.log(reader.result);
                    }
                reader.readAsDataURL(file)
            }
            else{this.$swal.fire({type: 'error', title: 'File is too large'});}
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