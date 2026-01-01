// Vue 3 + Composition API
import { unref } from 'vue';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

export default function useInvoiceTools(target) {
    const downloadPdf = async (filename = `invoice-${Date.now()}.pdf`) => {
        const el = unref(target);
        if (!el) return console.warn('Invoice element not found');

        // High‑resolution canvas so small text stays sharp
        const canvas = await html2canvas(el, { scale: 2 });
        const img = canvas.toDataURL('image/png');

        const pdf = new jsPDF({ unit: 'mm', format: 'a4', orientation: 'portrait' });
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = (canvas.height * pageWidth) / canvas.width;

        pdf.addImage(img, 'PNG', 0, 0, pageWidth, pageHeight);
        pdf.save(filename);
    };

    const printInvoice = () => {
        const el = unref(target);
        if (!el) return console.warn('Invoice element not found');

        const html = `
            <html>
                <head>
                    <title>Print Invoice</title>
                    <style>
                        @page { margin: 0; }
                        body  { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; }
                    </style>
                </head>
                <body>
                    ${el.outerHTML}
                </body>
            </html>
        `;

        const win = window.open('', '_blank', 'width=800,height=600');
        win.document.write(html);
        win.document.close();
        win.focus();
        win.print();
        win.close();
    };
    return { downloadPdf, printInvoice };
}
