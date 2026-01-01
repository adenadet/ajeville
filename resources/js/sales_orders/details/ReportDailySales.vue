<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Cahier Sales Report from {{ start_date }} to {{ end_date }}</h3>
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
                        <th>Cashier</th>
                        <th>Transactions</th>
                        <th>Total Sales</th>
                        <th>Discounts</th>
                        <th>Tax</th>
                        <th>Logistics</th>
                        <th>Net</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in report" :key="row.id">
                        <td>{{ addOne(index) }}</td>
                        <td>{{ row.first_name }} {{ row.last_name }}</td>
                        <td class="text-right">{{ row.transactions_count }}</td>
                        <td class="text-right">{{ currency(row.total_sales) }}</td>
                        <td class="text-right">{{ currency(row.total_discounts) }}</td>
                        <td class="text-right">{{ currency(row.total_tax) }}</td>
                        <td class="text-right">{{ currency(row.total_logistics) }}</td>
                        <td class="text-right">{{ currency(row.net_sales) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td colspan="2">Total </td>
                        <td class="text-right">{{ currency(totals.total_sales)}}</td>
                        <td class="text-right">{{ currency(totals.total_discounts)}}</td>
                        <td class="text-right">{{ currency(totals.total_tax)}}</td>
                        <td class="text-right">{{ currency(totals.total_logistics)}}</td>
                        <td class="text-right">{{ currency(totals.net_sales)}}</td>
                    </tr>
                </tfoot>
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
    computed: {
        totals() {
            return this.report.reduce((t, r) => {
                t.total_sales     += Number(r.total_sales || 0);
                t.total_discounts += Number(r.total_discounts || 0);
                t.total_tax       += Number(r.total_tax || 0);
                t.total_logistics += Number(r.total_logistics || 0);
                t.net_sales       += Number(r.net_sales || 0);
                return t;
            }, {
                total_sales: 0,
                total_discounts: 0,
                total_tax: 0,
                total_logistics: 0,
                net_sales: 0
            });
        }
    },
    data() {
        return {
            report: [],
            loading: false,
            error: null,
        };
    },
    methods: {
        downloadExcel() {
            if (!this.report.length) {alert("No data available to export"); return;}

            const totals = this.getGrandTotals();

            const data = [
                ["S/N", "Cashier", "Transactions", "Total Sales", "Discount",
                "Tax", "Logistics", "Net"]
            ];

            this.report.forEach((row, index) => {
                data.push([
                    index + 1,
                    row.first_name+' '+row.last_name,
                    row.transactions_count,
                    this.currency(row.total_sales),
                    this.currency(row.total_discounts),
                    this.currency(row.total_tax),
                    this.currency(row.total_logistics),
                    this.currency(row.net_sales),
                ]);
            });

            data.push([
                "", "TOTAL", "",
                this.totals.total_sales,
                this.totals.total_discounts,
                this.totals.total_tax,
                this.totals.total_logistics,
                this.totals.net_sales
            ]);
            /** Grand Totals Row */
           
            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Cashier Sales Report");
            XLSX.writeFile(workbook, this.reportFilename("xlsx"));
        },
        downloadPDF() {
            if (!this.report.length) {alert("No data available to export"); return;}

            const totals = this.getGrandTotals();
            const doc = new jsPDF("l", "mm", "a4");

            doc.setFontSize(14);
            doc.text(`Sales Item Report (${this.start_date} to ${this.end_date})`, 14, 15);

            const body = this.report.map((row, index) => ([
                index + 1,
                row.first_name+' '+row.last_name,
                row.transactions_count,
                this.currency(row.total_sales),
                this.currency(row.total_discounts),
                this.currency(row.total_tax),
                this.currency(row.total_logistics),
                this.currency(row.net_sales),
            ]));

            /** Grand Totals Row */
            body.push(["", "TOTAL", "", this.currency(this.totals.total_sales), this.currency(this.totals.total_discounts), this.currency(this.totals.total_tax), this.currency(this.totals.total_logistics), this.currency(this.totals.net_sales)]);

            autoTable(doc, {
                startY: 22,
                head: [["S/N", "Cashier", "Transactions", "Total Sales", "Discount", "Tax", "Logistics", "Net"]],
                body,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [33, 33, 33] },
                didParseCell(data) {if (data.row.index === body.length - 1) {data.cell.styles.fontStyle = "bold";}}
            });

            doc.save(this.reportFilename("pdf"));
        },
        getGrandTotals() {
            return this.report.reduce((totals, row) => {
                totals.total_sales          += Number(row.total_sales || 0);
                totals.total_discounts      += Number(row.total_discounts || 0);
                totals.total_tax            += Number(row.total_tax || 0);
                totals.total_logistics      += Number(row.total_logistics || 0);
                totals.net_sales            += Number(row.net_sales|| 0);
                
                return totals;
            }, {
                total_sales: 0,
                total_discounts: 0,
                total_tax: 0,
                total_logistics: 0,
                net_sales: 0,
            });
        },
        reportFilename(extension) {
            return `cashier_sales_report_${this.start_date}_to_${this.end_date}.${extension}`;
        },
    },
    mounted() {},
    props:{
        end_date: String,
        report_data: {
            type: Array,
            default: ()=> []
        },
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