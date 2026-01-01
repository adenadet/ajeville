<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>    
        <form @submit.prevent="changeEmployeeStatus">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Employee</label>
                        <div class="form-control">
                            {{ FullName(employee.user) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6" v-if="employeeData.employment_status != 1">
                    <div class="form-group">
                        <label>Date of Leaving</label>
                        <input required class="form-control" type="date" name="date_of_leaving" id="date_of_leaving" v-model="employeeData.date_of_leaving">
                    </div>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-sm btn-primary" type="submit">Submit</button>
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
            employeeData: new Form({
                date: '',
                employee_id: '',
                employment_status: '',
            }),
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {
    },
    methods: {
        changeEmployeeStatus(){
            this.loading = true;
            this.employeeData.put('/api/hrms/employees/update_status/'+this.employee.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Employee Status has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
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
        }
    }
}
</script>