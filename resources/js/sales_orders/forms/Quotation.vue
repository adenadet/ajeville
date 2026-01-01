<template>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form @submit.prevent="editMode ? updateQuotation() : submitQuotation()">
            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label class="form-label">Customer</label>
                    <select class="form-control" v-model="quotationData.customer_id">
                        <option value="">Walk In</option>
                        <option v-for="cust in customers" :key="cust.id" :value="cust.id">{{ cust.name }}</option>
                    </select>
                </div>
            </div>
            <!-- Payment Terms -->
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Payment Terms</label>
                        <select class="form-control" v-model="quotationData.payment_term_id">
                            <option v-for="term in paymentTerms" :key="term.id" :value="term.id">{{ term.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Issuing Store</label>
                        <select class="form-control" v-model="quotationData.store_id">
                        <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label class="form-label">Request Date</label>
                    <input type="date" class="form-control" v-model="quotationData.quote_date" />
                </div>

            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Logistics</label>
                    <input type="number" class="form-control" v-model.number="quotationData.logistics" />
                </div>
                <div class="col">
                    <label class="form-label">Discount</label>
                    <input type="number" class="form-control" v-model.number="quotationData.discount" />
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
                    <table class="table table-bquotationed">
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
                            <tr v-for="(item, index) in quotationData.items" :key="index">
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
                                <td><input class="form-control" type="number" v-model.number="item.unit_price" @input="updateTotal(index)" /></td>
                                <td>{{ currency(item.total_price) }}</td>
                                <td><button class="btn btn-danger btn-sm" @click.prevent="removeItem(index)">×</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit">Submit Order</button>
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
            quotationData: new Form ({
                id: null,
                unique_id: null,
                customer_id: '',
                payment_term_id: '',
                store_id: '',
                quote_date: '',
                taxes: 0,
                logistics: 0,
                discount: 0,
                items: [],
            }),
            stores: [],
            salesTypes: [],
            searchedItems: [],
        };
    },
    emits: ['quotationFormReload'],
    mounted() {
        this.getInitials();
    },
    methods: {
        addItem() {
            this.quotationData.items.push({
                item_id: '',
                item_name: '',
                quantity: 1,
                package_id: '',
                package_quantity: 1,
                unit_price: 0,
                total_price: 0,
            });
        },
        async getDetails(quotation_id) {
            await axios.get(`/api/sales/quotations/${quotation_id}`)
            .then(response => {
                this.quotationData.fill(response.data.quotation);
                this.quotationData.items = response.data.quotation.quotation_items.map(item => ({
                    ...item,
                    item_name: item.item.name, // Assuming item has a name property
                }));
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Quotation Form not loaded successfully',
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
                    title: 'Quotation Form not loaded successfully',
                })
            });
        },
        async searchItems(index, query) {
            if (query.length < 2) return;
            const res = await axios.get(`/api/inventory/items/quick_search?q=${query}&plan_id=`);
            this.searchedItems = res.data.items;
            const selected = res.data.items.find(i => i.name === query);
            if (selected) {
                this.quotationData.items[index].item_id = selected.id;
                this.quotationData.items[index].unit_price = selected.price;
            }
        },
        refreshPage(response){
            this.customers = response.data.customers;
            this.paymentTerms = response.data.payment_terms;
            this.packageTypes = response.data.package_types;
            this.stores = response.data.stores;
            this.salesTypes = response.data.sales_types;      
        },
        removeItem(index) {
            this.quotationData.items.splice(index, 1);
        },
        updateTotal(index) {
            const item = this.quotationData.items[index];
            item.total_price = (item.quantity * item.unit_price).toFixed(2);
        },
        async submitOrder() {
            try {
                this.loading = true;
                //const res = await axios.post('/api/sales-quotations', this.form);
                this.quotationData.post('/api/sales/quotations')
                .then(response =>{
                    this.loading = false;
                    this.$emit('quotationFormReload', response);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Quotation has been created',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                    });
                });
                this.loading = false;
            } 
            catch (err) {
                console.error(err);
                alert('Failed to submit quotation.');
            }
        },
        async updateQuotation() {
            try {
                this.loading = true;
                //const res = await axios.post('/api/sales-quotations', this.form);
                this.quotationData.put('/api/sales/quotations/'+this.quotation_id)
                .then(response =>{
                    this.loading = false;
                    this.$emit('quotationFormReload', response);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Order has been updated',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                    });
                });
                this.loading = false;
                  
                //alert('Order submitted successfully!');
                // optionally reset form
            } 
            catch (err) {
                console.error(err);
                alert('Failed to submit quotation.');
            }
        },
    },
    props:{
        quotation: Object,
        quotation_id: String,
        editMode: Boolean,
    },
    watch: {
        quotation_id(){
            this.loading = true;
            if (this.quotation_id != null){this.getDetails(this.quotation_id)}
            else{ this.quotationData.fill({}); this.loading = false;}
        }
    },
};
</script>