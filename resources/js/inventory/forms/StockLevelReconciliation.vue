<template>
  <div class="card">
        <div class="card-header bg-dark text-white">
            <h3>Stock Level Reconciliation</h3>
        </div>
        <div class="card-body p-0">
            <vue-excel-editor  v-model="reconciliationData" :columns="columns" :editable="true">
            </vue-excel-editor>
            
            <button class="btn btn-primary mt-3" @click="submitReconciliation">
                Save Reconciliation
            </button>
        </div>
  </div>
</template>
<script>
import VueExcelEditor from 'vue-excel-editor';

export default {
    components: { VueExcelEditor },
    data() {
        return {
            reconciliationData: [],
            columns: [
                { label: 'Item Name', field: 'item_name', readonly: true },
                { label: 'Batch No', field: 'batch_no', readonly: true },
                { label: 'Expiry Date', field: 'expiry_date', readonly: true },
                { label: 'System Stock', field: 'stock_level', readonly: true },
                { label: 'Counted Stock', field: 'counted_stock', type: 'number' },
                { label: 'Reason', field: 'reason', type: 'text' },
            ],
        };
    },
    mounted() {
        this.loadData();
    },
    methods: {
        async loadData() {
            const res = await fetch(`/api/stock-reconciliation?store_id=1`);
            this.reconciliationData = await res.json();
        },
        async submitReconciliation() {
        await fetch(`/api/stock-reconciliation/save`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.reconciliationData),
        });
        alert("Reconciliation saved successfully!");
        }
    }
};
</script>
