<template>
    <section class="overlay-wrapper">
        <form @submit.prevent>
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="custom-tabs-five-overlay-tab" data-toggle="pill" href="#custom-tabs-five-overlay" role="tab" aria-controls="custom-tabs-five-overlay" aria-selected="true">Details</a></li>
                        <li class="nav-item"><a class="nav-link" id="custom-tabs-five-overlay-dark-tab" data-toggle="pill" href="#custom-tabs-five-overlay-dark" role="tab" aria-controls="custom-tabs-five-overlay-dark" aria-selected="false">Order Items</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-five-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-five-overlay" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-tab">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name">PO Name</label>
                                    <input type="text" required class="form-control" name="name" id="name" v-model="purchaseOrderData.name" placeholder="PO Name"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" name="type_id" id="type_id" v-model="purchaseOrderData.type_id" required>
                                        <option value="">--Select Type--</option>
                                        <option value="FPO">Foreign</option>
                                        <option value="LPO">Local</option>
                                        <option value="OPO">Ordinary</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="unique_id">PO Unique ID</label>
                                    <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number" />
                                    <div class="form-control">{{ purchaseOrderData.unique_id }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Store</label>
                                    <select class="form-control" name="store_id" id="store_id" v-model="purchaseOrderData.store_id" required>
                                        <option value="">--Select Store--</option>
                                        <option v-for="store in stores" :key="store.id" :value="store.id">
                                            {{ store.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Terms</label>
                                    <select class="form-control" name="payment_term_id" id="payment_term_id" v-model="purchaseOrderData.payment_term_id" required>
                                        <option value="">--Select Payment Term--</option>
                                        <option v-for="payment_term in payment_terms" :key="payment_term.id" :value="payment_term.id">
                                            {{ payment_term.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" class="form-control" name="date" id="date" v-model="purchaseOrderData.date" placeholder="Date" required/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Delivery Date</label>
                                    <input type="date" class="form-control" name="delivery_date" id="delivery_date" v-model="purchaseOrderData.delivery_date" placeholder="Delivery Date" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control"  name="category_id" id="category_id" v-model="purchaseOrderData.category_id" required>
                                        <option value="">--Select Category --</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vendor</label>
                                    <select class="form-control" v-model="purchaseOrderData.vendor_id" required>
                                        <option value="">--Select Vendor--</option>
                                        <option v-for="vendor in all_vendors" :key="vendor.id" :value="vendor.id">
                                            {{ vendor.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Taxes</label>
                                    <input type="number" step="0.01" class="form-control" id="taxes" name="taxes" v-model.number="purchaseOrderData.taxes"/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Logistics</label>
                                    <input class="form-control" type="number" step="0.01" id="logistics" name="logistics" v-model.number="purchaseOrderData.logistics"/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Discount</label>
                                    <input class="form-control" type="number" step="0.01" id="discount" name="discount" v-model.number="purchaseOrderData.discount"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Other Information</label>
                                    <textarea class="form-control" rows="4" v-model="purchaseOrderData.description" placeholder="Description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4"><strong>Sub Total:</strong> {{ currency(sub_total) }}</div>
                            <div class="col-md-4"><strong>Other Costs (Taxes + Logistics - Discount):</strong>{{ currency(other_costs) }}</div>
                            <div class="col-md-4"><strong>Grand Total:</strong> {{ currency(grand_total) }}</div>
                        </div>
                    </div>
                    <div class="tab-pane fade card p-0" id="custom-tabs-five-overlay-dark" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-dark-tab">
                        <div class="card-header bg-dark">
                            <div class="card-tools">
                                <button class="btn btn-primary btn-xs" type="button" @click="addItem">
                                    <i class="fa fa-plus mr-1"></i>Add Item
                                </button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Package Type</th>
                                        <th>Package Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in purchaseOrderData.order_items" :key="index">
                                        <td>
                                            <input class="form-control" v-model="item.item_name" @input="searchItems(index, item.item_name)":list="'item-list-' + index"/>
                                            <datalist :id="'item-list-' + index">
                                                <option v-for="i in searchedItems" :key="i.id" :value="i.name">{{ i.name }}</option>
                                            </datalist>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" required min="1" v-model.number="item.quantity" @input="updateTotal(index)"/>
                                        </td>
                                        <td>
                                            <select class="form-control" v-model="item.package_id" required>
                                                <option v-for="p in package_types" :key="p.id" :value="p.id">
                                                    {{ p.name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" v-model.number="item.package_quantity" required min="1" @input="updateTotal(index)"/>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" step="0.01" v-model.number="item.unit_price" @input="updateTotal(index)"/>
                                        </td>
                                        <td>{{ currency(item.total_price) }}</td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" @click.prevent="removeItem(index)">×</button>
                                        </td>
                                    </tr>
                                    <tr v-if="purchaseOrderData.order_items.length === 0">
                                        <td colspan="7" class="text-center">No items added.</td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" @click="editMode ? updatePurchaseOrder() : createPurchaseOrder()" :disabled="loading">
                            {{ editMode ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</template>
<script>
export default {
    computed: {
        all_vendors() {
            if (this.purchaseOrderData.category_id) {
                return this.vendors.filter(
                    (vendor) => vendor.category_id === this.purchaseOrderData.category_id
                );
            }
            return this.vendors;
        },
        grand_total() {
            return this.sub_total + this.other_costs;
        },
        other_costs() {
            const taxes = Number(this.purchaseOrderData.taxes) || 0;
            const logistics = Number(this.purchaseOrderData.logistics) || 0;
            const discount = Number(this.purchaseOrderData.discount) || 0;
            return taxes + logistics - discount;
        },
        sub_total() {
            const items = this.purchaseOrderData.order_items || [];
            if (!items.length) return 0;
            return items.reduce((sum, it) => sum + (Number(it.total_price) || 0), 0);
        },
    },
    data() {
        return {
        loading: false,
        categories: [],
        package_types: [], // expected to be filled from initials API
        payment_terms: [],
        stores: [],
        vendors: [],
        searchedItems: [],
        // Using your Form helper (keeps same API you used)
        purchaseOrderData: new Form({
            id: "",
            additional_cost: "",
            date: "",
            delivery_date: "",
            description: "",
            logistics: 0,
            name: "",
            order_items: [],
            payment_term_id: "",
            status: "",
            store_id: null,
            taxes: 0,
            type_id: "",
            unique_id: "",
            vendor_id: "",
            category_id: "",
            discount: 0,
        }),
        };
    },
    emits: ["purchaseOrderReload"],
    methods: {
        addItem() {
            this.purchaseOrderData.order_items.push({
                item_id: "",
                item_name: "",
                quantity: 1,
                package_id: this.package_types.length ? this.package_types[0].id : 1,
                package_quantity: 1,
                unit_price: 0,
                total_price: 0,
            });
        },
        removeItem(index) {
            this.purchaseOrderData.order_items.splice(index, 1);
        },
        updateTotal(index) {
            const it = this.purchaseOrderData.order_items[index];
            if (!it) return;
            const qty = Number(it.quantity) || 0;
            const pkgQty = Number(it.package_quantity) || 1;
            const unit = Number(it.unit_price) || 0;
            it.total_price = +(qty * pkgQty * unit).toFixed(2);
            this.$set ? this.$set(this.purchaseOrderData.order_items, index, it) : (this.purchaseOrderData.order_items[index] = it);
        },
        async searchItems(index, query) {
            if (query.length < 3) return;
            const res = await axios.get(`/api/inventory/items/quick_search?q=${query}`);
            this.searchedItems = res.data.items;
            const selected = res.data.items.find(i => i.name === query);
            if (selected) {
                this.purchaseOrderData.order_items[index].item_id = selected.id;
            }
        },
        createPurchaseOrder() {
            this.loading = true;
            if (!this.purchaseOrderData.order_items || this.purchaseOrderData.order_items.length === 0) {
                this.$toast.fire({
                    icon: "error",
                    title: "Please add at least one item to the purchase order",
                });
                this.loading = false;
                return;
            }

            this.purchaseOrderData.order_items.forEach((_, idx) => this.updateTotal(idx));

            this.purchaseOrderData.post("/api/procurement/purchase_orders")
            .then((response) => {
                this.loading = false;
                this.$toast.fire({
                    icon: "success",
                    title: "Purchase Order created successfully",
                });
                this.$emit("purchaseOrderReload");
                if (typeof this.purchaseOrderData.reset === "function") {
                    this.purchaseOrderData.reset();
                } 
                else {
                    this.purchaseOrderData = new Form({
                        id: "",
                        date: "",
                        delivery_date: "",
                        description: "",
                        logistics: 0,
                        name: "",
                        order_items: [],
                        payment_term_id: "",
                        status: "",
                        store_id: null,
                        taxes: 0,
                        type_id: "",
                        unique_id: "",
                        vendor_id: "",
                        category_id: "",
                        discount: 0,
                    });
                }
            })
            .catch((err) => {
                this.loading = false;
                const title = err?.response?.data?.message || "Purchase Order not created successfully";
                this.$toast.fire({
                    icon: "error",
                    title,
                });
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get("/api/procurement/purchase_orders/initials")
            .then((response) => {
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: "error",
                    title: "Purchase Order Form not loaded successfully",
                });
            });
        },
        loadEdit() {
            if (!this.purchase_order || Object.keys(this.purchase_order).length === 0) return;
            if (typeof this.purchaseOrderData.fill === "function") {
                this.purchaseOrderData.fill(this.purchase_order);
            } 
            else {
                Object.keys(this.purchase_order).forEach((k) => {
                    this.purchaseOrderData[k] = this.purchase_order[k];
                });
            }
            (this.purchaseOrderData.order_items || []).forEach((it, idx) => {
                if (it.total_price == null) this.updateTotal(idx);
            });
        },
        refreshPage(response) {
            this.categories = response.data.categories || [];
            this.package_types = response.data.package_types || [];
            this.payment_terms = response.data.payment_terms || [];
            this.stores = response.data.stores || [];
            this.vendors = response.data.vendors || [];
        },
        updatePurchaseOrder() {
            if (!this.purchaseOrderData.id) {
                this.$toast.fire({ icon: "error", title: "Missing purchase order id" });
                return;
            }
            this.loading = true;
            this.purchaseOrderData.put("/api/procurement/purchase_orders/" + this.purchaseOrderData.id)
            .then((response) => {
                this.loading = false;
                this.$emit("purchaseOrderReload");
                this.$toast.fire({
                    icon: "success",
                    title: "Purchase Order updated successfully",
                });
                if (typeof this.purchaseOrderData.reset === "function") {
                    this.purchaseOrderData.reset();
                }
            })
            .catch((err) => {
                this.loading = false;
                const title = err?.response?.data?.message || "Purchase Order not updated successfully";
                this.$toast.fire({
                    icon: "error",
                    title,
                });
            });
        },
    },
    mounted() {
        this.getAllInitials();
        if (this.editMode) {
            this.loadEdit();
        }
    },
    props: {
        editMode: {
            type: Boolean,
            default: false,
        },
        purchase_order: {
            type: Object,
            default: () => ({}),
        },
    },
    watch: {
        purchase_order: {
            handler() {
                this.loadEdit();
            },
            deep: true,
        },
    },
};
</script>
