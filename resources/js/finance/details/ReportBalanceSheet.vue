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
        <div class="card-body table-responsive bg-none" >
            <div v-if="report.date" class="mb-3">
                <strong>As of: </strong> {{ report.date }}
            </div>

            <!-- Assets -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Assets</div>
                <div class="card-body">
                    <h5>Current Assets</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><td>Cash</td><td>{{ currency(report.assets.current_assets.cash) }}</td></tr>
                            <tr><td>Accounts Receivable</td><td>{{ currency(report.assets.current_assets.accounts_receivable) }}</td></tr>
                            <tr><td>Inventory</td><td>{{ currency(report.assets.current_assets.inventory) }}</td></tr>
                        </tbody>
                    </table>

                    <h5>Non-Current Assets</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><td>Fixed Assets</td><td>{{ currency(report.assets.non_current_assets.fixed_assets) }}</td></tr>
                            <tr><td>Depreciation</td><td>{{ currency(report.assets.non_current_assets.depreciation) }}</td></tr>
                            <tr><td>Net</td><td>{{ currency(report.assets.non_current_assets.net) }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <h5>Total Assets: {{ currency(report.total_assets) }}</h5>
                </div>
            </div>

            <!-- Liabilities -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">Liabilities</div>
                <div class="card-body">
                    <table class="table table-bordered">
                    <tbody>
                        <tr><td>Current Liabilities</td><td>{{ currency(report.liabilities.current) }}</td></tr>
                        <tr><td>Non-Current Liabilities</td><td>{{ currency(report.liabilities.non_current) }}</td></tr>
                    </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <h5>Total Liabilities: {{ currency(report.liabilities.total) }}</h5>
                </div>
            </div>

            <!-- Equity -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Equity</div>
                <div class="card-body">
                    <table class="table table-bordered">
                    <tbody>
                        <tr><td>Capital</td><td>{{ formatCurrency(report.equity.capital) }}</td></tr>
                        <tr><td>Retained Earnings</td><td>{{ formatCurrency(report.equity.retained_earnings) }}</td></tr>
                    </tbody>
                    </table>
                    <h5>Total Equity: {{ formatCurrency(report.equity.total_equity) }}</h5>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import axios from "axios";
import jsPDF from "jspdf";
//import autoTable from "jspdf-autotable";
//import * as XLSX from "xlsx";

export default {
    data() {
        return {
            loading: false,
            report: {
                assets: { current_assets: {}, non_current_assets: {}, total_assets: 0 },
                liabilities: { current: 0, non_current: 0, total_liabilities: 0 },
                equity: { capital: 0, retained_earnings: 0, total_equity: 0 },
                date: null,
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