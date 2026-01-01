<template>
<section class="">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Report Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Report Type</label>
                                <select class="form-control" name="report_type" id="report_type" v-model="reportData.report_type">
                                    <option value="">---Select Report Type---</option>
                                    <option value="daily_sales">Daily Sales Report</option>
                                    <option value="user_sales">Cashier Sales</option>
                                    <option value="discount">Discount Report</option>
                                    <option value="gross_sales">Gross Sales</option>
                                    <option value="payments">Payment Report</option>
                                    <option value="returns">Returns Report</option>
                                    <option value="sales_items">Sales Item Report</option>
                                    <option value="total_sales">Total Sales</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="array_contains(date_only, reportData.report_type)">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Report Date</label>
                                <input class="form-control" type="date" required name="date" id="date" v-model="reportData.date">
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="array_contains(date_start_end, reportData.report_type)">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Report Start Date</label>
                                <input class="form-control" type="date" required name="start_date" id="start_date" v-model="reportData.start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Report End Date</label>
                                <input class="form-control" type="date" required name="end_date" id="end_date" v-model="reportData.end_date">
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="array_contains(requires_customers, reportData.report_type)">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Customers</label>
                                <select class="form-control" name="customer_type" id="customer_type" v-model="reportData.customer_type">
                                    <option value=0>All Customers</option>
                                    <option value=1>Select Customers</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" v-if="reportData.customer_type == 1">
                            <div class="form-group">
                                <label>Select Customers</label>
                                <multiselect id="multiselect" v-model="reportData.customers" :options="customers" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick some" label="name" track-by="id" :preselect-first="true">
                                </multiselect>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="array_contains(requires_users, reportData.report_type)">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Users</label>
                                <select class="form-control" name="user_type" id="user_type" v-model="reportData.user_type">
                                    <option value=0>All Users</option>
                                    <option value=1>Select Users</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" v-if="reportData.user_type == 1">
                            <div class="form-group">
                                <label>Select Users</label>
                                <multiselect id="multiselect" v-model="reportData.users" :options="users" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick some" label="name" track-by="id" :preselect-first="true">
                                </multiselect>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><button class="btn btn-sm btn-success" @click="getReport()">Report</button></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 p-0">
            <div class="card" v-if="report_type == ''">
                <div class="card-header"><h3 class="card-title">Awaiting Query</h3></div>
                <div class="card-body" style="height: 600px"></div>
            </div>
            <SalesDetailReportDailySales :report_data.sync="report_data" v-else-if="report_type == 'daily_sales'" :end_date="reportData.end_date" :start_date="reportData.start_date" /> 
            <SalesDetailReportSalesItemDetailed :report_data.sync="report_data" v-else-if="report_type == 'sales_items'" :end_date="reportData.end_date" :start_date="reportData.start_date" /> 
        </div>
    </div>    
</section>
</template>
<script>
import Multiselect from 'vue-multiselect'

export default {
    components: {
        Multiselect
    },
    data() {
        return {
            customers: [],
            date_only: ['detailed_tax'],
            date_start_end: ['daily_sales', 'tax', 'payments', 'discounts', 'total_sales', 'sales_items', 'returns', 'gross_sales', 'user_sales', 'customer_sales'],
            requires_customers: ['customer_sales'],
            requires_users: ['daily_sales',],
            available_reports: [],
            editMode: false,
            loading: false,
            query: '',
            reportData: new Form({
                customers: [],
                customer_type: 0,
                date: '',
                end_date: '',
                report_type: '',
                start_date: '',
                users: [],
                vendors: [],
            }),
            report_data: {},
            report_type: '',
        }
    },
    emits:['salesOrderReload'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        array_contains(array, variable){
            return array.includes(variable);
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/sales/reports')
            .then(response =>{
                this.banks = response.data.banks;
                this.customers = response.data.customers;
                this.modes = response.data.modes;
                this.vendors = response.data.vendors;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Report Form not loaded successfully',});
            });
            this.loading = false;
        },
        async getReport(){
            this.loading = true;
            this.report_type = this.reportData.report_type;
            await this.reportData.post('/api/sales/reports')
            .then(response =>{
                this.report_type = this.reportData.report_type;
                this.refreshResultPage(response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'Report has been generated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;
        },
        refreshResultPage(response){
            this.report_data = response.data.report_data;
            console.log("Got here");
        },
        refreshPage(response) {
            this.closeModals();
            this.$emit('salesOrderReload');
        },
    },
}
</script>