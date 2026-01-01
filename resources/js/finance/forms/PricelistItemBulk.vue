<template>
<section class="overlay-wrapper p-0">
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title">Items List</h4>
            <div class="card-tools">
                <button class="btn btn-xs btn-success" @click="saveChanges" :disabled="loading"><i class="fas fa-save mr-1"></i> Save Changes</button>
            </div>
        </div>
        <div class="card-body overlay-wrapper p-0 table-responsive">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <vue-excel-editor v-model="pricelistItems" :page-size="50" :page-options="[50, 100, 200, 500]" class="editor">
                <vue-excel-column 
                v-for="column in columns" :key="column.field" :field="column.field" :label="column.label" :type="column.type" 
                :options="column.type === 'select' ? column.options : undefined" :to-text="column.type === 'select' ? column.toText : undefined" 
                :to-value="column.type === 'select' ? column.toValue : undefined"/>
            </vue-excel-editor>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            columns: [
                { key: 'name', label: 'Item Name', type: 'string', field: 'name', },
                { key: 'price', label: 'Price', type: 'number', field: 'price', },
                { field: 'covered', key: 'covered', label: 'Covered', type: 'select', options: [
                        { label: "Yes", value: 1 },
                        { label: "No", value: 0 },
                    ],
                    toText: (val) => (val === 1 || val === 1  ? "Yes" : "No"),
                    toValue: (label) => (label === "Yes" ? 1 : 0), },
                { field: 'coverage', key: 'coverage', label: 'Coverage', type: 'number' },
                { field: 'requires_code', key: 'requires_code', label: 'Requires Code', type: 'select', options: [
                        { label: "Yes", value: 1 },
                        { label: "No", value: 0 },
                    ],
                    toText: (val) => (val === 1 || val === "Active"  ? "Active" : "No"),
                    toValue: (label) => (label === "Active" ? 1 : 0), },
                { field: 'max_sessions', key: 'max_sessions', label: 'Max Sessions', type: 'number' },
                { field: 'max_cost_per_session', key: 'max_cost_per_session', label: 'Max Cost/Session', type: 'number' },
                { field: 'pricing_type_id', key: 'pricing_type_id', label: 'Pricing Type', type: 'number' },
            ],
            loading: false,
            rows: [],
        }
    },
    mounted() {},
    methods: {
        pricelistItems: {},
        saveChanges() {
            this.loading = true;
            console.log(this.pricelistItems);
            axios.put(`/api/finance/price_lists/${this.$route.params.id}/update_items`, {
                items: this.pricelistItems
            })
            .then(() => this.$swal.fire({icon: 'success', title: 'The Pricelist details has been updated', showConfirmButton: false, timer: 1500})
            )
            .catch(() => alert('Failed to save')) /*handler(newItems) {
                
            },
            immediate: true,*/
            this.pricelistItems = this.price_list_items;
            this.loading = false;
        }
    },
    props:{
        price_list_items: {
            type: Object,
            default: () => {}
        },
    },
    watch:{
        price_list_items(){
            this.loading = true;
            this.pricelistItems = this.price_list_items;
            this.loading = false;
        }
    },
}
</script>
<style scoped>
.editor {
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 0px;
    margin-top: 0px;
}
</style>