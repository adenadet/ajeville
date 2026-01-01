<template>
<section class="overlay-wrapper">
    <div class="card">
        <div class="card-header">
            Payslip Details
        </div>
        <div class="card-body">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4><i class="fas fa-globe"></i> Ajeville <!--small class="float-right">Date: 2/10/2014</small--></h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-6 invoice-col">
                        <b><u>Employee Details</u></b>
                        <address>
                            <strong>{{ FullName(employee.user) }}</strong><br>
                            Date of Birth: {{ employee.user != null ? employee.user.dob : 'None Given' }}<br /> 
                            Email: {{ employee.user != null ? employee.user.email : 'None Given' }}<br />
                            Phone: {{ employee.user != null ? employee.user.phone : 'None Given' }}
                        </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 invoice-col">
                        <b><u>Employment Details</u></b>
                        <address>
                            <!--strong>John Doe</strong><br-->
                            Unique ID: {{ employee.unique_id}}<br>
                            Grade: {{ employee.salary_grade != null ? employee.salary_grade.name : 'Not Assigned' }}<br>
                            Department: {{ employee.department != null ? employee.department.name : 'Not Assigned' }}<br>
                            Designation: {{ employee.designation != null ? employee.designation.name : 'Not Assigned' }}<br />
                        </address>
                    </div>
                    <div class="col-sm-3 invoice-col">
                        <b><u>Government Details</u></b>
                        <address>
                            <!--strong>John Doe</strong><br-->
                            TIN: {{ employee.tin}}<br>
                            NIN: {{ employee.nin}}<br>
                            BVN: {{ employee.bvn }}<br>
                        </address>
                    </div>
                </div>
                <div class="row" v-if="payslip.period != null">
                    Payslip Information
                    <div class="col-md-4">
                        Period Name
                    </div>
                    <div class="col-md-4">
                        <strong>Period Start Date</strong><br />
                        {{ ExcelDate(payslip.start_date) }}
                    </div>
                    <div class="col-md-4">
                        <strong>Period End Date</strong><br />
                        {{ ExcelDate(payslip.start_date) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <h3>Earnings</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Reporting Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="earning in payslip.earnings">
                                    <td>{{ earning.name }}</td>
                                    <td>{{ currency(earning.amount) }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ currency(payslip.earnings_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <h3>Deductions</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Reporting Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="deduction in payslip.deductions">
                                    <td>{{ deduction.name }}</td>
                                    <td>{{ currency(deduction.amount) }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ currency(payslip.deductions_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <hr />
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>PFA Code</th>
                                    <th>PFA Name</th>
                                    <th>PFA Pin</th>
                                    <th>CTSS Code</th>
                                    <th>Union Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="earning in payslip.earnings">
                                    <td>{{ employee.pension.code }}</td>
                                    <td>{{ employee.pension.name }}</td>
                                    <td>{{ employee.pension.pin }}</td>
                                    <td>{{ employee.pension.ctss_code }}</td>
                                    <td>{{ employee.pension.union_code }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ currency(payslip.earnings_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <h3>Deductions</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Reporting Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="deduction in payslip.deductions">
                                    <td>{{ deduction.name }}</td>
                                    <td>{{ currency(deduction.amount) }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ currency(payslip.deductions_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-if="employee.account">
                    <hr />
                    <h3>Payment Information</h3>
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bank Code</th>
                                    <th>Bank Name</th>
                                    <th>Account Number</th>
                                    <th>Net Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ employee.account.bank.code }}</td>
                                    <td>{{ employee.account.bank.name }}</td>
                                    <td>{{ employee.account.account_number }}</td>
                                    <td>{{ payslip.net_pay }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--div class="row no-print">
                    <div class="col-12">
                    <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                    <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
                        Payment
                    </button>
                    <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                        <i class="fas fa-download"></i> Generate PDF
                    </button>
                    </div>
                </div-->
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            payslip: {employee: {}, salary:{}, deductions: {}, bonuses: {}, allowances: {},},
            leave_request: {},
            loading: false,
        }
    },
    mounted() {},
    methods: {
        getInitials() {
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