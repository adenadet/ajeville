<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0 bg-success">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Payments</h3>
                        <a class="text-white" href="/escrows/payments">View Report</a>
                    </div>
                </div>
                <div class="card-body">
                    <GeneralChartBar :chartData.sync="chartData" :chartOptions.sync="chartOptions" />
                </div>
            </div>    
        </div>
        <div class="col-lg-6">
            <div class="card mt-5">
                <div class="card-header bg-navy">
                    <h3 class="card-title">Transactions</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <EscrowDetailTransactionList :transactions="transactions.data" source="main" />
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mt-5 border-0">
                <div class="card-header border-0 bg-success">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Payments</h3>
                        <a class="text-white" href="/escrows/payments">View Report</a>
                    </div>
                </div>
                <div class="card-body">
                    <EscrowDetailProductList :products="products.data" source="mine" @getValues="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
        // Chart Data
        chartData: {
            labels: [],
            datasets: [
            {
                label: 'Total Paid (₦)',
                backgroundColor: '#4CAF50',
                data: [],
            },
            ],
        },

        // Chart Options
        chartOptions: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Payments to you in the Last 30 Days',
                },
            },
            scales: {y: { beginAtZero: true },},
        },

        // State
        current_page: 1,
        editMode: false,
        loading: false,
        query: '',
        status: 'all',

        // Data Sets
        products: { data: [] },
        transactions: { data: [] },
        payments: [],
        transaction: {},
        };
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addTransaction() {
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#transactionModal').modal('hide');
        },
        getAllInitials() {
            this.loading = true;
            axios.get(`/api/escrows/dashboard?type=my&page=${this.current_page}&status=${this.status}&query=${this.query}`)
            .then(response => {
                this.refreshPage(response);

                // Build an empty date map for the last 30 days
                const dateMap = {};
                const today = new Date();
                for (let i = 29; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(today.getDate() - i);
                    const key = date.toISOString().slice(0, 10);
                    dateMap[key] = 0;
                }

                // Use response.data.payments instead of rawData
                response.data.payments.forEach(payment => {
                    if (payment.date in dateMap) {
                        dateMap[payment.date] = payment.total_paid;
                    }
                });

                // Replace the entire chartData object to trigger reactivity
                this.chartData = {
                    labels: Object.keys(dateMap),
                    datasets: [
                        {
                            label: 'Total Paid (₦)',
                            backgroundColor: '#4CAF50',
                            data: Object.values(dateMap),
                        },
                    ],
                };

                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Transactions loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Items not loaded successfully',
                });
            });
        },
        refreshPage(response) {
            this.payments = response.data.payments;
            this.products = response.data.products;
            this.transactions = response.data.transactions;
            this.closeModals();
        },
    },
};
</script>
