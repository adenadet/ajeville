<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="modal-sm">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pending Invoices</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesDetailOrderList :orders.sync="orders"  view="admin"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Customer Aging Analysis Report</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-xs btn-info ml-1" @click="downloadCSV" title="Download All Items"><i class="fa fa-file-excel mr-1"></i> Download CSV</button>
                <button type="button" class="btn btn-xs btn-info ml-1" @click="downloadPdf" title="Download All Items"><i class="fa fa-file-pdf mr-1"></i>Download PDF</button>
            </div>
        </div>
        <div class="card-body table-responsive bg-none p-0" style="height: 600px">
            <table class="table table-striped table-hover table-bordered text-nowrap">
                <thead>
                    <tr class="bg-gray-100">
                        <th>Customer</th>
                        <th v-for="label in bucketLabels" :key="label">{{ label }}</th>
                        <th>Total</th>
                        <th>Unapplied Credit</th>
                        <th>Current Balance</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody v-if="report_data.length > 0">
                    <tr v-for="data in report_data" :key="data.customer_id">
                        <td>{{ data.customer.name }}</td>
                        <td v-for="label in bucketLabels" :key="label">{{ currency(data.buckets[label] || 0) }}</td>
                        <td>{{ currency(data.total_outstanding) }}</td>
                        <td>{{ currency(data.unapplied_credit) }}</td>
                        <td>{{ currency(data.customer.balance) }}</td>
                        <td>
                            <button @click="openDetails(data)" class="btn btn-tool text-dark">View</button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                <tr >
                        <td colspan="8" class="p-4 center small">No outstanding balances found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AgingReport',
    data() {
        return {
            bucketLabels: ['0-30', '31-60', '61-90', '91+'],
            showModal: false,
            detailRow: {},
            status: '',
            orders: [],
            loading: false,
        };
    },
    methods: {
        async downloadCSV() {
            this.loading = true;
            try {
                if (!this.report_data || this.report_data.length === 0) {
                    alert('No data available to download.');
                    this.loading = false;
                    return;
                }

                // Step 1: Prepare CSV headers
                const headers = [
                    'Customer', ...this.bucketLabels, 'Total Outstanding', 'Unapplied Credit', 'Current Balance'
                ];

                // Step 2: Build CSV rows
                const rows = this.report_data.map(data => {
                    const row = [
                        data.customer?.name || '',
                        ...this.bucketLabels.map(label => data.buckets[label] || 0),
                        data.total_outstanding || 0,
                        data.unapplied_credit || 0,
                        data.customer?.balance || 0
                    ];
                    return row;
                });

                // Step 3: Convert to CSV string
                const csvContent = [
                    headers.join(','), // header row
                    ...rows.map(row => row.join(','))
                ].join('\n');

                // Step 4: Create a downloadable Blob
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);

                // Step 5: Create a temporary link and trigger download
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `AgingReport_${new Date().toISOString().split('T')[0]}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            } 
            catch (error) {
                console.error('Error generating CSV:', error);
                alert('Error while generating CSV file.');
            }                 
            this.loading = false;
        },
        async downloadPDF() {
            this.loading = true;
            try {
                if (!this.report_data || this.report_data.length === 0) {
                    alert('No data available to download.');
                    this.loading = false;
                    return;
                }

                // Import jsPDF and autoTable dynamically (to avoid SSR issues)
                const { jsPDF } = await import('jspdf');
                const autoTable = (await import('jspdf-autotable')).default;

                // Step 1: Create PDF instance
                const doc = new jsPDF('landscape'); // landscape gives more width for columns

                // Step 2: Add title and date
                doc.setFontSize(16);
                doc.text('Customer Aging Analysis Report', 14, 15);
                doc.setFontSize(10);
                doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 22);

                // Step 3: Define columns
                const headers = [
                    ['Customer', ...this.bucketLabels, 'Total Outstanding', 'Unapplied Credit', 'Current Balance']
                ];

                // Step 4: Prepare table data
                const rows = this.report_data.map(data => [
                    data.customer?.name || '',
                    ...this.bucketLabels.map(label => this.currency(data.buckets[label] || 0)),
                    this.currency(data.total_outstanding || 0),
                    this.currency(data.unapplied_credit || 0),
                    this.currency(data.customer?.balance || 0)
                ]);

                // Step 5: Generate the PDF table
                autoTable(doc, {
                    head: headers,
                    body: rows,
                    startY: 30,
                    styles: { fontSize: 8 },
                    headStyles: { fillColor: [41, 128, 185], textColor: 255 },
                    alternateRowStyles: { fillColor: [245, 245, 245] },
                    margin: { top: 25 },
                });

                // Step 6: Save the file
                const fileName = `AgingReport_${new Date().toISOString().split('T')[0]}.pdf`;
                doc.save(fileName);

            } 
            catch (error) {
                this.$swal.fire('Error!', 'Error while generating PDF file.', 'error');
            } 
            this.loading = false;
        },
        openDetails(row) {
            this.detailRow = row;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.detailRow = {};
        },
    },
    mounted() {
        // load customers and aging on mount
        //this.fetchCustomers().then(() => {this.fetchAging();});
    },
    props:{
        report_data: Object,
    },
    watch: {
        report_data(){

        }
    },
};
</script>

