<template>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form @submit.prevent="editMode ? updateOrder() : submitOrder()">
            <div class="row">
                <div class="col-sm-9">
                    <div class="form-group mb-3">
                        <label class="form-label">Customer</label>
                        <select class="form-control" v-model="salesOrderData.customer_id">
                            <option value="">Walk In</option>
                            <option v-for="cust in customers" :key="cust.id" :value="cust.id">{{ cust.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Customer LPO ID</label>
                        <input type="text" class="form-control" v-model="salesOrderData.customer_lpo" />
                    </div>
                </div>
            </div>
            <!-- Payment Terms -->
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Payment Terms</label>
                        <select class="form-control" v-model="salesOrderData.payment_term_id">
                            <option v-for="term in paymentTerms" :key="term.id" :value="term.id">{{ term.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <!-- Issuing Store -->
                    <div class="form-group">
                        <label class="form-label">Issuing Store</label>
                        <select class="form-control" v-model="salesOrderData.store_id">
                        <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Sales Type</label>
                        <select class="form-control" v-model="salesOrderData.sales_type_id">
                            <option value="">--Select Sales Type--</option>
                            <option value="1">Cash</option>
                            <option value="2">Credit (Postpaid)</option>
                            <option value="3">Debit (Prepaid)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Request Date</label>
                    <input type="date" class="form-control" v-model="salesOrderData.request_date" />
                </div>
                <div class="col">
                    <label class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" v-model="salesOrderData.delivery_date" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Price List</label>
                    <select class="form-control" v-model="plan_id">
                        <option value="">--Select Price List--</option>
                        <option v-for="branch_price_list in price_lists" :value="branch_price_list.price_list_id">{{branch_price_list.price_list.name}}</option>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label">Logistics</label>
                    <input type="number" step="any"  class="form-control" v-model.number="salesOrderData.logistics" />
                </div>
                <div class="col">
                    <label class="form-label">Discount</label>
                    <input type="number" step="any" class="form-control" v-model.number="salesOrderData.discount" />
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-secondary">
                    <h4 class="card-title">Order Items</h4>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" @click.prevent="addItem">Add Item</button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">              
                    <table class="table table-bordered">
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
                            <tr v-for="(item, index) in salesOrderData.items" :key="index">
                                <td>
                                    <input class="form-control" v-model="item.item_name" @input="searchItems(index, item.item_name)" list="item-list"/>
                                    <datalist id="item-list"><option v-for="i in searchedItems" :key="i.id" :value="i.name">{{ i.name }}</option></datalist>
                                </td>
                                <td><input class="form-control" type="number" v-model.number="item.quantity" @input="updateTotal(index)" /></td>
                                <td>
                                    <select class="form-control" v-model="item.package_id">
                                        <option v-for="p in packageTypes" :key="p.id" :value="p.id">{{ p.name }}</option>
                                    </select>
                                </td>
                                <td><input class="form-control" type="number" v-model.number="item.package_quantity" /></td>
                                <td><div class="form-control" v-html.number="item.unit_price"></div></td>
                                <td>{{ currency(item.unit_price * item.package_quantity * item.quantity) }}</td>
                                <td><button class="btn btn-danger btn-sm" @click.prevent="removeItem(index)">×</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit" :disabled="salesOrderData.items.length < 1">Submit Order</button>
            </div>
        </form>
    </section>      
</template>
<script>
export default {
    data() {
        return {
            customers: [],
            loading: false,
            paymentTerms: [],
            packageTypes: [],
            plan_id: '',
            price_lists: [],
            salesOrderData: new Form ({
                id: null,
                unique_id: null,
                customer_id: '',
                customer_lpo: '',
                payment_term_id: '',
                store_id: '',
                sales_type_id: '',
                request_date: '',
                delivery_date: '',
                logistics: 0,
                discount: 0,
                items: [],
            }),
            stores: [],
            salesTypes: [],
            searchedItems: [],
        };
    },
    emits: ['orderFormReload'],
    mounted() {
        this.getInitials();
    },
    methods: {
        addItem() {
            if (this.plan_id == '') {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Please select a Price List first',
                });
                return;
            }
            this.salesOrderData.items.push({
                item_id: '',
                item_name: '',
                quantity: 1,
                package_id: 1,
                package_quantity: 1,
                unit_price: 0,
                total_price: 0,
            });
        },
        async getDetails(order_id) {
            await axios.get(`/api/sales/orders/${order_id}`)
            .then(response => {
                this.salesOrderData.fill(response.data.order);
                this.salesOrderData.items = response.data.order.order_items.map(item => ({
                    ...item,
                    item_name: item.item.name, // Assuming item has a name property
                }));
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Form not loaded successfully',
                });
            });
        },
        async getInitials() {
            await axios.get('/api/sales/orders/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Form not loaded successfully',
                })
            });
        },
        async searchItems(index, query) {
            if (query.length < 3) return;
            const res = await axios.get(`/api/inventory/items/quick_search?q=${query}&plan_id=${this.plan_id}`);
            this.searchedItems = res.data.items;
            const selected = res.data.items.find(i => i.name === query);
            if (selected) {
                this.salesOrderData.items[index].item_id = selected.id;
                this.salesOrderData.items[index].unit_price = selected.price;
            }
        },
        refreshPage(response){
            this.customers = response.data.customers;
            this.paymentTerms = response.data.payment_terms;
            this.packageTypes = response.data.package_types;
            this.price_lists = response.data.price_lists;
            this.stores = response.data.stores;
            this.salesTypes = response.data.sales_types;      
        },
        removeItem(index) {
            this.salesOrderData.items.splice(index, 1);
        },
        updateTotal(index) {
            const item = this.salesOrderData.items[index];
            item.total_price = (item.quantity * item.unit_price).toFixed(2);
        },
        async submitOrder() {
            try {
                this.loading = true;
                this.salesOrderData.post('/api/sales/orders')
                .then(response =>{
                    this.$emit('orderFormReload', response);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Order has been created',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.salesOrderData.reset();
                })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                    });
                });
                this.loading = false;
            } 
            catch (err) {
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            }
        },
        async updateOrder() {
            try {
                this.loading = true;
                this.salesOrderData.put('/api/sales/orders/'+this.order_id)
                .then(response =>{
                    this.$emit('orderFormReload', response);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Order has been updated',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.salesOrderData.reset();
                })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                    });
                });
                this.loading = false;
            } 
            catch (err) {
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            }
        },
    },
    props:{
        order: Object,
        order_id: String,
        editMode: Boolean,
    },
    watch: {
        order_id(){
            this.loading = true;
            if (this.order_id != null){this.getDetails(this.order_id)}
            else{ this.salesOrderData.fill({}); this.loading = false;}
        }
    },
};
</script>