<template>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Cash Flow Statement <span class="small text-muted">Period: {{ propsPeriod }}</span></h2>
            <div class="card-tools">
                <button class="btn" @click="exportCsv">Export CSV</button>
                <button class="btn ghost" @click="downloadPdf">Download PDF</button>
            </div>
        </div>

        <div v-if="!report_data || Object.keys(report_data).length === 0" class="empty">No data</div>

        <div v-else class="card-body">
            <div class="summary-row">
                <div><strong>Operating:</strong> {{ currency(report_data.operating) }}</div>
                <div><strong>Investing:</strong> {{ currency(report_data.investing) }}</div>
                <div><strong>Financing:</strong> {{ currency(report_data.financing) }}</div>
                <div><strong>Net Change in Cash:</strong> {{ currency(report_data.net_change_in_cash) }}</div>
            </div>

            <table class="report-table">
                <thead>
                <tr>
                    <th>Activity</th>
                    <th class="num">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr><td>Operating Activities</td><td class="num">{{ currency(report_data.operating) }}</td></tr>
                <tr><td>Investing Activities</td><td class="num">{{ currency(report_data.investing) }}</td></tr>
                <tr><td>Financing Activities</td><td class="num">{{ currency(report_data.financing) }}</td></tr>
                <tr class="total-row"><td>Net Change in Cash</td><td class="num">{{ currency(report_data.net_change_in_cash) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CashFlowReport',
    props: {
        report_data: { type: Object, default: () => ({}) }
    },
    computed: {
        propsPeriod() {
        if (!this.report_data || !this.report_data.period) return '-';
        return `${this.report_data.period.start} → ${this.report_data.period.end}`;
        }
    },
    methods: {
        formatCurrency(v) {
        const n = Number(v || 0);
        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        exportCsv() {
        if (!this.report_data) return;
        const rows = [
            ['Category','Amount'],
            ['Operating', this.report_data.operating || 0],
            ['Investing', this.report_data.investing || 0],
            ['Financing', this.report_data.financing || 0],
            ['Net Change in Cash', this.report_data.net_change_in_cash || 0]
        ];
        const csv = '\uFEFF' + rows.map(r => r.map(c => {
            const s = c === null || c === undefined ? '' : String(c);
            if (s.includes(',') || s.includes('"') || s.includes('\n')) return `"${s.replace(/"/g,'""')}"`;
            return s;
        }).join(',')).join('\r\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `cashflow_${this.report_data.period ? this.report_data.period.start : ''}_${this.report_data.period ? this.report_data.period.end : ''}.csv`;
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
            const doc = new jsPDF('landscape', 'pt', 'a4');
            doc.setFontSize(14);
            doc.text('Cash Flow Statement', 40, 40);
            doc.setFontSize(10);
            const periodText = this.report_data.period ? `${this.report_data.period.start} → ${this.report_data.period.end}` : '';
            doc.text(`Period: ${periodText}`, 40, 58);

            const body = [
            ['Operating Activities', this.formatCurrency(this.report_data.operating)],
            ['Investing Activities', this.formatCurrency(this.report_data.investing)],
            ['Financing Activities', this.formatCurrency(this.report_data.financing)],
            ['Net Change in Cash', this.formatCurrency(this.report_data.net_change_in_cash)]
            ];

            autoTable(doc, {
            startY: 80,
            head: [['Activity','Amount']],
            body: body,
            styles: { fontSize: 9 },
            headStyles: { fillColor: [41, 128, 185] }
            });

            doc.save(`cashflow_${this.report_data.period ? this.report_data.period.start : ''}_${this.report_data.period ? this.report_data.period.end : ''}.pdf`);
        } catch (e) {
            console.error(e);
            alert('Failed to generate PDF');
        } finally {
            this.$emit('toggle-loading', false);
        }
        }
    }
};
</script>