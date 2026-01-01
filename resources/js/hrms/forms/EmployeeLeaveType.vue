<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form @submit.prevent="assignLeaveType()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Employee</label>
                    <input type="hidden" v-model="employeeData.id">
                    <div class="form-control">
                        {{ FullName(employee.user) }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Leave Types </label>
                    <div class="row">
                        <div class="col-sm-3" v-for="leave_type in leave_types" :key="leave_type.id">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="leave_types[]" id="leave_types[]" v-model="employeeData.leave_types" :value="leave_type.id" :checked="employeeData.leave_types.includes(leave_type.id)">
                                <label class="form-check-label">{{leave_type.name}}</label>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            </div>
        </div>
        </form>
    
    </section>
</template>
<script>
import { Multiselect} from 'vue-multiselect';
import { MultiListSelect } from 'vue-search-select';

export default {
    components:{
        Multiselect, MultiListSelect,
    },
    data(){
        return {
            employeeData: new Form({
                employee_id: '',
                leave_types: [],
            }),
            leave_types: [],
            loading: false,
        }
    },
    emits: ['refreshPage'],
    methods:{
        addUser(){
            this.editMode = false;
            this.user = {};
            $('#userModal').modal('show');
        },
        assignLeaveType(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, assign leaves!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.employeeData.post('/api/hrms/employee_leave_types')
                    .then(response=>{
                        this.$swal.fire('Assigned!', response.data.message, 'success');
                        this.$emit('refreshPage');
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials(){
            this.loading = true
            axios.get('/api/hrms/employee_leave_types/initials')
            .then(response =>{
                this.leave_types = response.data.leave_types;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Users not loaded successfully',})
            });
        },
        modifyEmployee(employee){
            this.employee = employee;
            this.editMode = true;
            $('#employeeModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
    props:{
        employee: Object,
        source: String,
    },
    watch:{
        employee(){
            this.employeeData.employee_id = this.employee.id;
            this.employeeData.leave_types = [];
            for(var i =0; i < this.employee.leave_types.length; i++){
                this.employeeData.leave_types.push(this.employee.leave_types[i].leave_type_id);
            }
            this.loading = false;
        }
    }
}
</script>