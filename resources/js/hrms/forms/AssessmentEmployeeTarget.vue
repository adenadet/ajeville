<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateTarget() : createTarget()">
        <div class="row">
            <div class="col-md-12" v-if="employee != null">
                <div class="form-group">
                    <label>User</label>
                    <div class="form-control">
                        {{ FullName(employee.user) }}
                    </div>
                    <input type="hidden" v-model="employeePeriodTargetData.employee_id" />
                </div>
            </div>
            <div class="col-md-12" v-else>
                <div class="form-group">
                    <label>Supervisor</label>
                    <model-list-select v-model="employeePeriodTargetData.employee_id"  :list="employees" optionValue="employee_id" placeholder="Select Employee" :custom-text="codeAndNameAndDesc"></model-list-select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Period</label>
                    <select class="form-control" name="period_id" id="period_id" v-model="employeePeriodTargetData.period_id">
                        <option value=""></option>
                        <option v-for="period in periods" :value="period.id">{{ period.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" v-model="employeePeriodTargetData.end_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status"  v-model="employeePeriodTargetData.status">
                        <option value="">--Select Status---</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor v-model:content="employeePeriodTargetData.notes" theme="snow" content-type="html" class="form-control"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary mt-3" type="submit">{{editMode ? 'Update' : 'Create'}}</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            employeePeriodTargetData : new Form({
                id: '',
                end_date: '',
                name: '',
                notes: '',
                start_date: '',
                status: '',
            }),
            employees: [],
            loading: false,
            periods: [],
        }
    },
    emits: ['refreshEmployeePeriodTarget'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createEmployeePeriodTarget() {
            this.loading = true;
            this.employeePeriodTargetData.post('/api/hrms/employee_period_targets')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Employee Period Target created successfully!',
                    });
                    this.$emit('refreshEmployeePeriodTarget', response);
                    this.loading = false;
                })
                .catch(error => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while creating education.',
                    });
                    this.loading = false;
                });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/designations/initials')
            .then(response =>{
                this.refreshPage(response);
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Designation Form not loaded successfully',
                })
            });
            this.loading = false;
        },
        updatePeriod() {
            this.loading = true;
            this.employeePeriodTargetData.put(`/api/hrms/employee_period_targets/${this.employeePeriodTargetData.id}`)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Employee Period Target updated successfully!',
                });
                this.$emit('refreshEmployeePeriodTarget', response);
                this.loading = false;
            })
            .catch(error => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while updating period.',
                });
                this.loading = false;
            });
        },
    },
    props: {
        editMode: Boolean,
        employee_period_target: Object,
        period: Object,
        source: String,
    },
    watch: {
        employee_period_target(){
            this.loading = true;
            this.employeePeriodTargetData.fill(this.hrItem);
            this.loading = false;
        }
    }
}
</script>