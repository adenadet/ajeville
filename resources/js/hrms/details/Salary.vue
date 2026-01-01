<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Basic Salary</label>
                <div class="form-control">{{ employee.basic_salary }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Daily Wages</label>
                <div class="form-control">{{ employee.daily_wages }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Income Tax</label>
                <div class="form-control">{{ employee.salary_income_tax }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Overtime Rate</label>
                <div class="form-control">{{ employee.salary_overtime }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Commission</label>
                <div class="form-control">{{ employee.salary_commission }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Leave </label>
                <div class="form-control">{{ employee.salary_overtime }}</div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editting: true,
            loading: false,
        }
    },
    mounted() {},
    methods: {
        getInitials() {
            this.loading = true;
            axios.get('/api/hrms/leaves/'+this.leave_request_id+'?type='+this.source)
            .then(response => {
                this.leave_request = response.data.leave_request;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
    },
    props: {
        employee: Object,
        leave_request_id: Number,
        source: String,
    },
    watch:{}
}
</script>