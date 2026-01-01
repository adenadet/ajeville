<template>
<section class="overlay-wrapper p-0">

    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction Name</th>
                <th>Debit</th>
                <th>Credit</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <!-- 'trans_type', 'reference_type', 'reference_id', -->
            <tr v-for="transaction in report_data">
                <td>{{ ExcelDate(transaction.date) }}</td>
                <td>{{ firstUp(transaction.reference_type)}} []</td>
                <td>{{ transaction.trans_type == 'debit' ? currency(transaction.amount) : '0.00' }}</td>
                <td>{{ transaction.trans_type == 'credit' ? currency(transaction.amount) : '0.00' }}</td>
                <td></td>
            </tr>            
        </tbody>
        <tfooter>
            <th></th>
            <th>Summary</th>
            <th>{{ report_data.total_debit }}</th>
            <th>{{ report_data.total_credit }}</th>
            <th></th>
        </tfooter>
    </table>
</section>
</template>
<script>
export default {
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