<template>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Items List</h3>
            <div class="card-tools">
                <button class="btn btn-sm btn-success" @click="saveItems" :disabled="loading">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </div>
        <div class="card-body overlay-wrapper p-0 table-responsive">
            <div class="overlay dark p-0" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <vue-excel-editor v-model="items_list" :page-size="50" :page-options="[50, 100, 200, 500]" class="editor p-0">
                <vue-excel-column v-for="column in columns" :key="column.field" :field="column.field" :label="column.label" :type="column.type" :options="column.type === 'select' ? column.options : undefined"
                :to-text="column.type === 'select' ? column.toText : undefined" 
                :to-value="column.type === 'select' ? column.toValue : undefined"/>
            </vue-excel-editor>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            items_list: [],
            columns: [],
            dropdowns: {
                brands: [],
                categories: [],
                classifications: [],
                package_types: [],
            }
        }
    },
    methods: {
        async getInitials() {
            this.loading = true
            
            try {
                const res = await axios.get('/api/inventory/items/initials')
                this.dropdowns.brands         = this.mapDropdown(res.data.brands || [])
                this.dropdowns.categories     = this.mapDropdown(res.data.categories || [])
                this.dropdowns.classifications= this.mapDropdown(res.data.classifications || [])
                this.dropdowns.package_types  = this.mapDropdown(res.data.package_types || [])
                this.buildColumns()
            } 
            catch (error) {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to load dropdown options'
                })
            } 
            finally {
                this.loading = false
            }
        },
        buildColumns() {
            this.columns = [
                { field: "name", label: "Item Name", type: "string" },
                {
                    field: "category_id",
                    label: "Category",
                    type: "select",
                    options: this.dropdowns.categories,
                    toText: (val) => this.findLabelById(this.dropdowns.categories, val),
                    toValue: (label) => this.findIdByLabel(this.dropdowns.categories, label),
                    display: (row) => this.findLabelById(this.dropdowns.categories, row.category_id),
                },
                {
                    field: "brand_id",
                    label: "Brand",
                    type: "select",
                    options: this.dropdowns.brands,
                    toText: (val) => this.findLabelById(this.dropdowns.brands, val),
                    toValue: (label) => this.findIdByLabel(this.dropdowns.brands, label),
                    display: (row) => this.findLabelById(this.dropdowns.brands, row.brand_id),
                },
                {
                    field: "classification_id",
                    label: "Classification",
                    type: "select",
                    options: this.dropdowns.classifications,
                    toText: (val) => this.findLabelById(this.dropdowns.classifications, val),
                    toValue: (label) => this.findIdByLabel(this.dropdowns.classifications, label),
                    display: (row) => this.findLabelById(this.dropdowns.classifications, row.classification_id),
                },
                { field: "barcode", label: "Barcode", type: "string" },
                { field: "last_landing_cost", label: "Landing Cost", type: "number" },
                {
                    field: "package_type_id",
                    label: "Package Type",
                    type: "select",
                    options: this.dropdowns.package_types,
                    toText: (val) => this.findLabelById(this.dropdowns.package_types, val),
                    toValue: (label) => this.findIdByLabel(this.dropdowns.package_types, label),
                    display: (row) => this.findLabelById(this.dropdowns.package_types, row.package_type_id),
                },
                { field: "package_quantity", label: "Package Quantity", type: "number" },
                {
                    field: "status",
                    label: "Status",
                    type: "select",
                    options: [
                        { label: "Active", value: "active" },
                        { label: "Inactive", value: "inactive" },
                    ],
                    toText: (val) => (val === "active" ? "Active" : "Inactive"),
                    toValue: (label) => (label === "Active" ? "active" : "inactive"),
                    display: (row) => (row.status === "active" ? "Active" : "Inactive"),
                },
            ];
        },
        mapDropdown(list) {
            return list.map(i => ({ label: i.name || i.label, value: Number(i.id || i.value) }))
        },
        findLabelById(list, value) {
            const item = list.find((i) => i.id === value || i.value === value);
            return item ? item.name || item.label : value;
        },
        findIdByLabel(list, label) {
            const item = list.find(i => (i.name || i.label).toLowerCase().trim() === String(label).toLowerCase().trim());
            return item ? item.id || item.value : null;
        },
        async saveItems() {
            if (!this.items_list.length) {
                return this.$toast.fire({
                    icon: 'warning',
                    title: 'No items to save.'
                })
            }

            this.loading = true
            try {
                await axios.post('/api/inventory/items/bulk_update', {
                    items: this.items_list
                })
                this.$toast.fire({
                    icon: 'success',
                    title: 'Items updated successfully.'
                })
            } 
            catch (error) {
                console.error('Bulk update failed:', error)
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to save items.'
                })
            } 
            finally {
                this.loading = false
            }
        }
    },
    async mounted() {
        await this.getInitials()
    },
    props: {
        editMode: Boolean,
        items: Array
    },
    watch: {
        items: {
            handler(val) {
                this.items_list = val.map(item => ({
                    ...item,
                    brand_id: item.brand?.id || item.brand_id || null,
                    category_id: item.category?.id || item.category_id || null,
                    classification_id: item.classification?.id || item.classification_id || null,
                    package_type_id: item.package_type?.id || item.package_type_id || null,
                }))
            },
            immediate: true
        }
    },
}
</script>

<style scoped>
.editor {
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px;
    margin-top: 10px;
}
</style>
