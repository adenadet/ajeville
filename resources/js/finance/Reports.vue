<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row p-0">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark">Report Query</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Report Type</label>
                                <select class="form-control" name="report_type" id="report_type" v-model="reportData.report_type">
                                    <option value="">---Select Report Type---</option>
                                    <option value="balance_sheet">Balance Sheet</option>
                                    <option value="aging_analysis_receivables">Aging Analysis Receivables</option>
                                    <option value="receivables">Receivables</option>
                                    <option value="payables">Payables</option>
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
                    <div class="row" v-if="array_contains(requires_customer, reportData.report_type)">
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
                    <div class="row">
                        <div class="col-md-12"><button class="btn btn-sm btn-success" @click="getReport()">Report</button></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card" v-if="report_type == ''">
                <div class="card-header"><h3 class="card-title">Awaiting Query</h3></div>
                <div class="card-body" style="height: 600px"></div>
            </div>
            <FinanceDetailReportAgingAnalysisReceivables :report_data.sync="report_data" v-else-if="report_type == 'aging_analysis_receivables'" /> 
            <FinanceDetailReportBalanceSheet :report_data.sync="report_data" v-else-if="report_type == 'balance_sheet'" /> 
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
            date_only: ['aging_analysis_receivables', 'balance_sheet'],
            date_start_end: [],
            requires_customer: ['aging_analysis_receivables'],
            available_reports: [],
            editMode: false,
            loading: false,
            query: '',
            reportData: new Form({
                report_type: '',
                customers: [],
                customer_type: 0,
                vendors: [],
                start_date: '',
                end_date: '',
                date: '',
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
            axios.get('/api/finance/reports')
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
            await this.reportData.post('/api/finance/reports')
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