<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Cash Flow — Last 30 days</h3>
                </div>
                <div class="card-body p-0">
                    <apexchart type="bar" height="420" :options="chartOptions" :series="series"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
       <div class="col-md-4 col-lg-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>100<sup style="font-size: 20px">%</sup></h3>

                    <p>Expenditure Actual vs Budget</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-md-4 col-lg-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>100<sup style="font-size: 20px">%</sup></h3>

                    <p>Income Actual vs Budget</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
            <!--div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                </div>    
                <div class="card-body p-0">
                    <div class="text-sm" v-for="budget in budgets" :key="budget.id">
                        <p>{{ budget.department }}:
                        {{ currency(budget.actual) }} / {{ currency(budget.budgeted) }}</p>
                    </div>
                </div>
            </div-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-lg-4">    
            <div class="card card-success card-outline">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Bank Balances</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <FinanceDetailBranchAccountList :branch_accounts.sync="accounts" source="dashboard"/> 
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-8">
            <div class="card card-warning card-outline">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Recent Transactions</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <FinanceDetailTransactionList :transactions.sync="recent_transactions.data" /> 
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payables and Receivables</h3>
                </div>
                <div class="card-body p-0">
                    <div class="text-sm">
                        <p><strong>Payables:</strong> {{ currency(payables.total) }}</p>
                        <p><strong>Receivables:</strong> {{ currency(receivables.total) }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Draft Journal Entries</h3>
                </div>
                <div class="card-body p-0">
                    <p class="text-2xl font-bold">{{ pendingJournals }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">    
            <!-- Reconciliation Status -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reconciliation Status</h3>
                </div>
                <div class="card-body p-0">
                    <div v-for="rec in reconciliation" :key="rec.id" class="mb-2">
                        <div class="flex justify-between text-sm">
                        <span>{{ rec.account }}</span>
                        <span :class="{'text-red-500': !rec.reconciled, 'text-green-600': rec.reconciled}">
                            {{ rec.reconciled ? 'Reconciled' : 'Pending' }}
                        </span>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    
        <!-- Tax Liabilities -->
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tax Liabilities</h3>
                </div>
                <div class="card-body p-0">        
                    <ul class="text-sm">
                        <li v-for="tax in taxes" :key="tax.id">
                        <span>{{ tax.type }}: {{ currency(tax.amount) }} due {{ tax.due_date }}</span>
                        </li>
                    </ul>
                </div>
            </div>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Upcoming Payables</h3>
                </div>
                <div class="card-body p-0 table-responsive" style="height: 300px;">
                    <FinanceDetailTransactionList :transactions.sync="overdue_payables.data" /> 
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">Overdue Receivables</h3>
                </div>
                <div class="card-body p-0 table-responsive" style="height: 300px;">
                    <FinanceDetailTransactionList :transactions.sync="overdue_receivables.data" /> 
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import VueApexCharts from "vue3-apexcharts";
import FinanceDetailBranchAccountList from '@/finance/details/BranchAccountList.vue';
import FinanceDetailTransactionList from '@/finance/details/TransactionList.vue';

export default {
    components:{
        FinanceDetailBranchAccountList, FinanceDetailTransactionList,
        apexchart: VueApexCharts,
    },
    data() {
        return {
            loading: false,
            accounts: [],
            banks: [
                { id: 1, name: 'Access Bank', balance: 500000 },
                { id: 2, name: 'GTBank', balance: 1200000 },
            ],
            categories: [],
            chartOptions: {},
            COLORS: {
                sold: "#1E90FF",     // blue
                received: "#28A745", // green
                outward: "#FF8C00"   // orange
            },
            days: 60,
            payables: { total: 300000 },
            rawData: [],
            receivables: { total: 450000 },
            recentTransactions: [
                { id: 1, date: '2025-08-01', type: 'Deposit', amount: 100000 },
                { id: 2, date: '2025-08-01', type: 'Payment', amount: -45000 },
            ],
            series: [],
            overdue_payables:  {data: []},
            overdue_receivables: {data: []},
            pendingJournals: 3,
            rawData: {},
            reconciliation: [
                { id: 1, account: 'Access Bank', reconciled: false },
                { id: 2, account: 'GTBank', reconciled: true },
            ],
            recent_transactions: {data: [], total: 0},
            upcomingPayments: [
                { id: 1, vendor: 'Office Depot', amount: 40000, due_date: '2025-08-04' },
            ],
            taxes: [
                { id: 1, type: 'VAT', amount: 60000, due_date: '2025-08-10' },
            ],
            budgets: [
                { id: 1, department: 'Marketing', actual: 120000, budgeted: 200000 },
            ],
            notifications: [
                { id: 1, type: 'warning', message: '3 invoices overdue by more than 30 days.' },
            ]
        };
    },
    methods: {
        buildChartOptions: function () {
            var self = this
            return {
                chart: {
                    type: "bar",
                    stacked: false,
                    toolbar: { show: true },
                    zoom: { enabled: true },
                    animations: { enabled: true, easing: "easeinout", speed: 350 }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ["transparent"]
                },
                colors: [self.COLORS.sold, self.COLORS.received, self.COLORS.outward],
                xaxis: {
                    categories: self.categories,
                    labels: {
                        rotate: -45,
                        trim: true,
                        style: { fontSize: "11px" }
                    },
                    tickPlacement: "between"
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                        if (Math.abs(val) >= 1e9) return (val / 1e9).toFixed(1) + "B"
                        if (Math.abs(val) >= 1e6) return (val / 1e6).toFixed(1) + "M"
                        if (Math.abs(val) >= 1e3) return (val / 1e3).toFixed(1) + "K"
                        return val.toFixed(0)
                        }
                    },
                    title: { text: "Amount (NGN / local currency)" }
                },
                legend: { position: "top", horizontalAlign: "right" },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            try {
                                var nf = new Intl.NumberFormat(undefined, { style: "currency", currency: "NGN", maximumFractionDigits: 2 })
                                return nf.format(val)
                            } catch (e) {
                                return Number(val).toLocaleString()
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 800,
                    options: {
                        plotOptions: { bar: { columnWidth: "70%" } },
                        legend: { position: "bottom", horizontalAlign: "center" }
                    }
                }]
            }
        },
        normalizeAndPrepareSeries(){
            console.log(this.rawData);
            var n = Number(this.days) || 60
            var dateList = []
            var today = new Date()
            // build oldest -> newest
            for (var i = n - 1; i >= 0; i--) {
                var d = new Date(today)
                d.setDate(today.getDate() - i)
                dateList.push(this.isoDateYMD(d))
            }
            var map = {}
            for (var idx = 0; idx < this.rawData.length; idx++) {
                var row = this.rawData[idx]
                var ds = row && row.date !== undefined ? row.date : ""
                if (ds instanceof Date) ds = this.isoDateYMD(ds)
                else ds = String(ds)
                map[ds] = {
                    total_sold: Number(row.total_sold ?? 0) || 0,
                    payments_received: Number(row.payments_received ?? 0) || 0,
                    payments_outward: Number(row.payments_outward ?? 0) || 0
                }
            }

            var soldArr = []
            var receivedArr = []
            var outwardArr = []
            var cats = []

            for (var j = 0; j < dateList.length; j++) {
                var key = dateList[j]
                var item = map[key] || { total_sold: 0, payments_received: 0, payments_outward: 0 }
                soldArr.push(Number(item.total_sold) || 0)
                receivedArr.push(Number(item.payments_received) || 0)
                outwardArr.push(Number(item.payments_outward) || 0)
                cats.push(this.shortLabel(key))
            }

            this.series = [
                { name: "Product sold", data: soldArr },
                { name: "Payments received", data: receivedArr },
                { name: "Payments outward", data: outwardArr }
            ]

            console.log(this.series);
            this.categories = cats
            this.chartOptions = this.buildChartOptions()
        },
        isoDateYMD: function (date) {
            var y = date.getFullYear()
            var m = String(date.getMonth() + 1).padStart(2, "0")
            var d = String(date.getDate()).padStart(2, "0")
            return y + "-" + m + "-" + d
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/finance/dashboard')
            .then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
            this.normalizeAndPrepareSeries();
            this.loading = false;
        },
        refreshPage(response){
            this.accounts = response.data.accounts; 
            this.overdue_receivables = response.data.overdue_receivables;
            this.recent_transactions = response.data.recent_transactions;
            this.rawData = response.data.cashflow_reports;
        },
        shortLabel: function (dateStr) {
            var d = new Date(dateStr + "T00:00:00")
            return d.toLocaleDateString(undefined, { month: "short", day: "numeric" })
        },
    },
    mounted() {
        this.getAllInitials();
    }
};
</script>
