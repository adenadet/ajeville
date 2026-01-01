<template>
    <section>
        <form @submit.prevent="editMode ? updateAllowance() : createAllowance()">
            <div class="row">
                <div class="col-md-12" v-if="!editMode">
                    <div class="form-group">
                        <label>Employee</label>
                        <div class="form-control">{{ FullName(employee.user) }}</div>
                    </div>
                </div>
                <div class="col-md-12" v-else>
                    <div class="form-group">
                        <label>Employee</label>
                        <model-list-select v-model="allowanceForm.supervisor_id" :list="staffs" optionValue="employee_id" placeholder="Select Supervisor/Exco Lead" :custom-text="codeAndNameAndDesc"></model-list-select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Leave Request</label>
                        <select class="form-control">
                            <option value="">--Select Leave Request--</option>
                            <option v-for="leave in leave_requests" :value="leave.id">{{ leave.start_date }} - {{ leave.end_date }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label></label>
                        
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
export default {
    data() {
        return {
            allowanceForm: new Form({
                amount: '', 
                employee_id: '',
                id: '',
                leave_request_id: '',
                status: '',
            }),
            editMode: false,
            loading: false,
        }
    },
    emits:['refreshAllowance'],
    mounted() {},
    methods: {
        createAllowance(){
            this.loading = true;
            this.allowanceForm.post('/api/hrms/leave_allowances')
            .then(response =>{
                this.$emit('refreshAllowance', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Leave Allowance request has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        updateAllowance(){
            this.loading = true;
            this.allowanceForm.put('/api/hrms/leave_allowances/'+this.allowanceForm.id)
            .then(response =>{
                this.$emit('refreshAllowance', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Leave Allowance has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
    },
    props: {
        allowances: Array,
        source: String,
    }
}
</script>