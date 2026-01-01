<template>
<section class="overlay-wrapper">
    <div class="card">
        <div class="card-header bg-dark">
            <h2 class="card-title">Profit & Loss <span class="text-muted">Period: {{ propsPeriod }}</span></h2>
            <div class="card-tools">
                <button class="btn" @click="exportCsv">Export CSV</button>
                <button class="btn ghost" @click="downloadPdf">Download PDF</button>
            </div>
        </div>
        <div v-if="!report_data || Object.keys(report_data).length === 0"  class="card-body table-responsive p-0" style="height:600px;">No data</div>
        <div v-else class="card-body table-responsive p-0" style="height:600px;">
            <div class="summary-row">
                <div><strong>Revenue:</strong> {{ currency(report_data.revenue) }}</div>
                <div><strong>COGS:</strong> {{ currency(report_data.cogs) }}</div>
                <div><strong>Gross Profit:</strong> {{ currency(report_data.gross_profit) }}</div>
                <div><strong>Operating Expenses:</strong> {{ currency(report_data.operating_expenses) }}</div>
                <div><strong>Net Profit:</strong> {{ currency(report_data.net_profit) }}</div>
            </div>
            <table class="report_data-table">
                <thead>
                    <tr><th>Line</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Revenue</td>
                        <td>{{ currency(report_data.revenue) }}</td>
                    </tr>
                    <tr>
                        <td>Cost of Goods Sold</td>
                        <td>{{ currency(report_data.cogs) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gross Profit</strong></td>
                        <td><strong>{{ currency(report_data.gross_profit) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Operating Expenses</td>
                        <td>{{ currency(report_data.operating_expenses) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Operating Profit</strong></td>
                        <td><strong>{{ currency(report_data.operating_profit) }}</strong></td>
                    </tr>
                    <tr v-if="report_data.other_income || report_data.other_expense">
                        <td>Other Income</td>
                        <td>{{ currency(report_data.other_income) }}</td>
                    </tr>
                    <tr v-if="report_data.other_expense">
                        <td>Other Expense</td>
                        <td>{{ currency(report_data.other_expense) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Net Profit</td>
                        <td>{{ currency(report_data.net_profit) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>

<script>
export default {
    name: 'ProfitLossReport',
    props: {
        report_data: {
        type: Object,
        default: () => ({})
        }
    },
    computed: {
        propsPeriod() {
        if (!this.report_data || !this.report_data.period) return '-';
            return `${this.report_data.period.start} → ${this.report_data.period.end}`;
        }
    },
    methods: {
        exportCsv() {
            if (!this.report_data) return;
            const rows = [
                ['Line', 'Amount'],
                ['Total Revenue', this.report_data.revenue || 0],
                ['Cost of Goods Sold', this.report_data.cogs || 0],
                ['Gross Profit', this.report_data.gross_profit || 0],
                ['Operating Expenses', this.report_data.operating_expenses || 0],
                ['Operating Profit', this.report_data.operating_profit || 0],
                ['Other Income', this.report_data.other_income || 0],
                ['Other Expense', this.report_data.other_expense || 0],
                ['Net Profit', this.report_data.net_profit || 0],
            ];
            const csv = '\uFEFF' + rows.map(r => r.map(c => {
                const s = c === null || c === undefined ? '' : String(c);
                if (s.includes(',') || s.includes('"') || s.includes('\n')) return `"${s.replace(/"/g,'""')}"`;
                return s;
            })
            .join(','))
            .join('\r\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `profit_loss_${this.report_data.period ? this.report_data.period.start : ''}_${this.report_data.period ? this.report_data.period.end : ''}.csv`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        },
        async downloadPdf() {
            if (!this.report_data) return;
            this.$emit('toggle-loading', true);
            try {
                const { jsPDF } = await import('jspdf');
                const autoTable = (await import('jspdf-autotable')).default;
                const doc = new jsPDF('portrait', 'pt', 'a4');
                doc.setFontSize(14);
                doc.text('Profit & Loss', 40, 40);
                doc.setFontSize(10);
                const periodText = this.report_data.period ? `${this.report_data.period.start} → ${this.report_data.period.end}` : '';
                doc.text(`Period: ${periodText}`, 40, 58);

                const body = [
                    ['Total Revenue', currency(this.report_data.revenue)],
                    ['Cost of Goods Sold', currency(this.report_data.cogs)],
                    ['Gross Profit', currency(this.report_data.gross_profit)],
                    ['Operating Expenses', currency(this.report_data.operating_expenses)],
                    ['Operating Profit', currency(this.report_data.operating_profit)],
                    ['Other Income', currency(this.report_data.other_income)],
                    ['Other Expense', currency(this.report_data.other_expense)],
                    ['Net Profit', currency(this.report_data.net_profit)]
                ];

                autoTable(doc, {
                    startY: 80, head: [['Line','Amount']], body: body, styles: { fontSize: 9 }, headStyles: { fillColor: [41, 128, 185] }
                });

                doc.save(`profit_loss_${this.report_data.period ? this.report_data.period.start : ''}_${this.report_data.period ? this.report_data.period.end : ''}.pdf`);
            } 
            catch (e) {
                console.error(e);
                alert('Failed to generate PDF');
            } 
            finally {
                this.$emit('toggle-loading', false);
            }
        }
    }
};
</script>