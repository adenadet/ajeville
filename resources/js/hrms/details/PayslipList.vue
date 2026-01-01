<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-hover table-bordered text-nowrap">
        <thead>
            <tr>
                <th rowspan="2">Staff</th>
                <th rowspan="2">Unique ID</th>
                <th rowspan="2">Month</th>
                <th colspan="5">Earnings</th>
                <th colspan="5">Deductions</th>
                <th rowspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th>Basic</th>
                <th>Housing</th>
                <th>Transport</th>
                <th>Meal</th>
                <th>Others</th>
                <th>Loan Repayments</th>
                <th>Salary Advance Repayment</th>
                <th>Cooperative Contributions</th>
                <th>Pension</th>
                <th>PAYE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>183</td>
                <td>{{ExcelMonthYear('11-7-2014')}}</td>
                <td>Approved</td>
                <td></td>
                <td>219</td>
                <td>Alexander Pierce</td>
                <td>11-7-2014</td>
                <td><span class="tag tag-warning">Pending</span></td>
                <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            leave_request: {},
            loading: false,
        }
    },
    mounted() {},
    methods: {
        getInitials(leave_request_id) {
            this.loading = true;
            axios.get('/api/hrms/leaves/'+leave_request_id+'?type='+this.source)
            .then(response => {
                this.leave_request = response.data.leave_request;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
    },
    props: {
        payslips: Array,
        source: String,
    },
    watch:{}
}
</script>