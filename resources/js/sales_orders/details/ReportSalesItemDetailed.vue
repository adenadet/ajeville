<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Sales Item Report</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-xs btn-info ml-1" @click="downloadExcel" title="Download All Items"><i class="fa fa-file-excel"></i></button>
                <button type="button" class="btn btn-xs btn-info ml-1" @click="downloadPDF" title="Download All Items"><i class="fa fa-file-pdf"></i></button>
            </div>
        </div>
        <div class="card-body table-responsive bg-none p-0">
            <table class="table table-bordered table-striped text-nowrap table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S/N</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity Sold</th>
                        <th>Avg. Selling Price</th>
                        <th>Avg. Cost Price</th>
                        <th>Total Sales Value</th>
                        <th>Cost Price</th>
                        <th>Total Discount</th>
                        <th>Gross Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in report" :key="row.id">
                        <td>{{ addOne(index) }}</td>
                        <td>{{ row.item_name }}</td>
                        <td>{{ row.category }}</td>
                        <td>{{ row.quantity_sold }}</td>
                        <td>{{ currency(row.avg_unit_price) }}</td>
                        <td>{{ currency(row.avg_cost_price) }}</td>
                        <td>{{ currency(row.gross_sales) }}</td>
                        <td>{{ currency(row.total_cost) }}</td>
                        <td>{{ currency(row.total_discount) }}</td>
                        <td>{{ currency(row.gross_profit) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import * as XLSX from "xlsx";
export default {
    data() {
        return {
            report: [],
            loading: false,
            error: null,
        };
    },
    methods: {
        downloadExcel() {
            if (!this.report.length) {
                alert("No data available to export");
                return;
            }

            const totals = this.getGrandTotals();

            const data = [
                ["S/N", "Item Name", "Category", "Quantity Sold", "Avg Selling Price", "Avg Cost Price", "Total Sales Value", "Total Cost", "Total Discount", "Gross Profit"]
            ];

            this.report.forEach((row, index) => {
                data.push([
                    index + 1,
                    row.item_name,
                    row.category ?? "-",
                    row.quantity_sold,
                    this.currency(row.avg_unit_price),
                    this.currency(row.avg_cost_price),
                    this.currency(row.gross_sales),
                    this.currency(row.total_cost),
                    this.currency(row.total_discount),
                    this.currency(row.gross_profit),
                ]);
            });

            /** Grand Totals Row */
            data.push([
                "", "GRAND TOTAL", "", totals.quantity_sold, "", "",  totals.gross_sales, totals.total_cost, totals.total_discount, totals.gross_profit,
            ]);

            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();

            XLSX.utils.book_append_sheet(workbook, worksheet, "Sales Item Report");

            XLSX.writeFile(workbook, this.reportFilename("xlsx"));
        },
        downloadPDF() {
            if (!this.report.length) {
                alert("No data available to export");
                return;
            }

            const totals = this.getGrandTotals();
            const doc = new jsPDF("l", "mm", "a4");

            doc.setFontSize(14);
            doc.text(`Sales Item Report (${this.start_date} to ${this.end_date})`, 14, 15);

            const body = this.report.map((row, index) => ([
                index + 1,
                row.item_name,
                row.category ?? "-",
                row.quantity_sold,
                this.currency(row.avg_unit_price),
                this.currency(row.avg_cost_price),
                this.currency(row.gross_sales),
                this.currency(row.total_cost),
                this.currency(row.total_discount),
                this.currency(row.gross_profit),
            ]));

            /** Grand Totals Row */
            body.push([
                "", "GRAND TOTAL", "", totals.quantity_sold, "", "", this.currency(totals.gross_sales), this.currency(totals.total_cost), this.currency(totals.total_discount), this.currency(totals.gross_profit),
            ]);

            autoTable(doc, {
                startY: 22,
                head: [["S/N", "Item Name", "Category", "Qty Sold", "Avg Sell Price", "Avg Cost Price", "Total Sales", "Total Cost", "Discount", "Gross Profit"]],
                body,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [33, 33, 33] },
                didParseCell(data) {
                    if (data.row.index === body.length - 1) {
                        data.cell.styles.fontStyle = "bold";
                    }
                }
            });

            doc.save(this.reportFilename("pdf"));
        },
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
        getGrandTotals() {
            return this.report.reduce((totals, row) => {
                totals.quantity_sold   += Number(row.quantity_sold || 0);
                totals.gross_sales     += Number(row.gross_sales || 0);
                totals.total_cost      += Number(row.total_cost || 0);
                totals.total_discount  += Number(row.total_discount || 0);
                totals.gross_profit    += Number(row.gross_profit || 0);
                return totals;
            }, {
                quantity_sold: 0,
                gross_sales: 0,
                total_cost: 0,
                total_discount: 0,
                gross_profit: 0,
            });
        },
        reportFilename(extension) {
            return `sales_item_report_${this.start_date}_to_${this.end_date}.${extension}`;
        },
    },
    mounted() {},
    props:{
        end_date: String,
        report_data: Object,
        start_date: String,
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