<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="assignEmployeeLeaveType()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Leave Type </label>
                    <select class="col-sm-12" v-if="leave_type == null" v-model="assignEmployeeLeaveTypeData.leave_type_id">
                        <option value="">--Select Leave Type--</option> 
                        <option v-for="leave_type in leave_types" :key="leave_type.id">{{leave_type.name}}</option>
                    </select> 
                    <Multiselect v-if="leave_type == null" v-model="assignEmployeeLeaveTypeData.leave_type_id" :options="leave_types" track-by="name" label="name" placeholder="Select Leave Type"></Multiselect>
                    <div class="form-control" v-else>{{ leave_type.name }}
                        <input type="hidden" v-model="assignEmployeeLeaveTypeData.leave_type_id">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Department</label>
                    <select value="" class="form-control" v-model="assignEmployeeLeaveTypeData.department_id" @change="updateEmployeeList()">
                        <option value="">--Select Department--</option>
                        <option v-for="department in departments" :key="department.id" :value="department.id">{{department.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Employee</label>
                    <Multiselect v-model="assignEmployeeLeaveTypeData.employees" :options="employee" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick some" label="name" track-by="name" :preselect-first="true">
                        <template #selection="{ values, search, isOpen }">
                            <span class="multiselect__single"
                                v-if="values.length"
                                v-show="!isOpen">{{ values.length }} options selected</span>
                        </template>
                    </Multiselect>
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
import Multiselect from 'vue-multiselect';
export default {
    components: {
        Multiselect
    },
    data() {
        return {
            assignEmployeeLeaveTypeData: new Form({
                leave_type_id: '',
                department_id: '',
                employees: [],
            }),
            departments: [],
            leave_types: [],
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {
    },
    methods: {
        assignEmployeeLeaveType(){
            this.loading = true;
            this.assignEmployeeLeaveTypeData.post('/api/hrms/leave_types/multiple')
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
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/leave_types/initials')
            .then(response => {
                this.refreshForm(response); this.loading = false;       
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your employee form did not loaded successfully',})
                this.loading = false;
            });
        },
    },
    props: {
        editMode: Boolean,
        leave_type: Object,
    },
    watch:{
        leave_type(){
            this.assignEmployeeLeaveTypeData.leave_type_id = this.leave_type.id;
            this.assignEmployeeLeaveTypeData.employees = this.leave_type.employees;
        }
    }
}
</script>