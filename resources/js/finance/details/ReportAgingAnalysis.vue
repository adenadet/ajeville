<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Balance Sheet Report</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-xs btn-info ml-1" @click="downloadExcel" title="Download All Items"><i class="fa fa-file-excel"></i></button>
                <button type="button" class="btn btn-xs btn-info ml-1" @click="downloadPdf" title="Download All Items"><i class="fa fa-file-pdf"></i></button>
            </div>
        </div>
        <div class="card-body table-responsive bg-none">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Client</th>
                        <th>0 - 30 days</th>
                        <th>31 - 60 days</th>
                        <th>61 - 90 days</th>
                        <th>91 - 120 days</th>
                        <th>121 - 150 days</th>
                        <th>> 150 days</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in report" :key="row.id">
                        <td>{{ row.name }}</td>
                        <td>{{ currency(row.bucket_0_30) }}</td>
                        <td>{{ currency(row.bucket_31_60) }}</td>
                        <td>{{ currency(row.bucket_61_90) }}</td>
                        <td>{{ currency(row.bucket_91_120) }}</td>
                        <td>{{ currency(row.bucket_121_150) }}</td>
                        <td>{{ currency(row.bucket_over_150) }}</td>
                        <td class="fw-bold">{{ currency(row.balance) }}</td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th>{{ currency(total.bucket_0_30) }}</th>
                        <th>{{ currency(total.bucket_31_60) }}</th>
                        <th>{{ currency(total.bucket_61_90) }}</th>
                        <th>{{ currency(total.bucket_91_120) }}</th>
                        <th>{{ currency(total.bucket_121_150) }}</th>
                        <th>{{ currency(total.bucket_over_150) }}</th>
                        <th class="fw-bold">{{ currency(total.balance) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            report: [],
            loading: false,
            error: null,
            total: {
                bucket_0_30: 0,
                bucket_31_60: 0,
                bucket_61_90: 0,
                bucket_91_120: 0,
                bucket_121_150: 0,
                bucket_over_150: 0,
                balance: 0,
            },
        };
    },
    methods: {
        async fetchReport() {
            try {
                const res = await axios.get("/api/finance/balance-sheet");
                this.report = res.data;
            } catch (error) {
                console.error("Error fetching balance sheet:", error);
            }
        },
        formatCurrency(value) {
            if (value == null) return "-";
            return "₦" + new Intl.NumberFormat().format(value);
        },
        downloadPDF() {
            const doc = new jsPDF();
            doc.text("Balance Sheet Report", 14, 16);
            doc.text("As of: " + this.report.date, 14, 24);

            autoTable(doc, {
                head: [["Section", "Description", "Amount"]],
                body: [
                    ["Assets", "Cash", this.formatCurrency(this.report.assets.current_assets.cash)],
                    ["Assets", "Accounts Receivable", this.formatCurrency(this.report.assets.current_assets.accounts_receivable)],
                    ["Assets", "Inventory", this.formatCurrency(this.report.assets.current_assets.inventory)],
                    ["Assets", "Net Fixed Assets", this.formatCurrency(this.report.assets.non_current_assets.net)],
                    ["Liabilities", "Current Liabilities", this.formatCurrency(this.report.liabilities.current)],
                    ["Liabilities", "Non-Current Liabilities", this.formatCurrency(this.report.liabilities.non_current)],
                    ["Equity", "Capital", this.formatCurrency(this.report.equity.capital)],
                    ["Equity", "Retained Earnings", this.formatCurrency(this.report.equity.retained_earnings)],
                ],
            });

            doc.save("balance_sheet.pdf");
        },
        downloadExcel() {
            const data = [
                ["Section", "Description", "Amount"],
                ["Assets", "Cash", this.report.assets.current_assets.cash],
                ["Assets", "Accounts Receivable", this.report.assets.current_assets.accounts_receivable],
                ["Assets", "Inventory", this.report.assets.current_assets.inventory],
                ["Assets", "Net Fixed Assets", this.report.assets.non_current_assets.net],
                ["Liabilities", "Current Liabilities", this.report.liabilities.current],
                ["Liabilities", "Non-Current Liabilities", this.report.liabilities.non_current],
                ["Equity", "Capital", this.report.equity.capital],
                ["Equity", "Retained Earnings", this.report.equity.retained_earnings],
            ];

            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Balance Sheet");

            XLSX.writeFile(workbook, "balance_sheet.xlsx");
        },
    },
    mounted() {
        //this.fetchReport();
    },
    props:{
        report_data: Object,
    },
    watch:{
        report_data(){
            this.loading = true;
            this.report = this.report_data;
            this.loading = false;
        }
    }
};
</script>