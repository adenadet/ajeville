<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateReturn() : createReturn()">
        <div class="row">
            <div class="col-sm-8">
                <div class="form-group mb-3">
                    <label class="form-label">Customer {{ returnOrderData.customer_id }} {{ editMode ? 'Working' : 'Not Working' }}</label>
                    <select class="form-control" v-model="returnOrderData.customer_id">
                        <option value="">Walk In</option>
                        <option v-for="cust in customers" :key="cust.id" :value="cust.id">{{ cust.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group mb-3">
                    <label class="form-label">Store</label>
                    <select class="form-control" v-model="returnOrderData.store_id">
                        <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" v-model="returnOrderData.date" class="form-control" required />
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group mb-3">
                    <label class="form-label">Sales Order ID</label>
                    <select v-model="returnOrderData.sales_order_id" class="form-control" @change="loadInitials">
                        <option value="">Select Sales Order</option>
                        <option v-for="so in orders" :value="so.unique_id" :key="so.id">{{ so.unique_id }}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group mb-3">
                    <label class="form-label">Price List</label>
                    <select v-model="returnOrderData.price_list_id" class="form-control"  v-if="!returnOrderData.sales_order_id">
                        <option value="">Select Price List</option>
                        <option v-for="pl in price_lists" :value="pl.price_list_id" :key="pl.price_list_id">{{pl.price_list != null ? pl.price_list.name : 'Deleted' }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Return Items</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" @click="addItem">Add Item</button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                            <th>Reason</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in returnOrderData.return_items" :key="index">
                            <td>
                                <input type="text" class="form-control" v-model="item.item_name" @input="searchItems(index, item.item_name)" />
                                <div class="list-group position-absolute" v-if="searchedItems[index] && searchedItems[index].length && item.item_name.length >= 2">
                                    <button class="list-group-item list-group-item-action" v-for="it in searchedItems[index]" @click.prevent="selectItem(index, it)">{{ it.name }}</button>
                                </div>
                            </td>
                            <td><input type="number" class="form-control" v-model.number="item.quantity" min="1" /></td>
                            <td><input type="number" class="form-control" v-model.number="item.unit_price" step="0.01" /></td>
                            <td><input type="number" class="form-control" v-model.number="item.discount" step="0.01" /></td>
                            <td class="text-bold">{{ currency(itemSubtotal(item)) }}</td>
                            <td><textarea class="form-control" v-model="item.reason" required rows="1"></textarea></td>
                            <td><button class="btn btn-danger btn-sm" @click="removeItem(index)">X</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6"></div>
            <div class="col-6">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2"><strong>Subtotal:</strong> <span>{{ currency(subtotal) }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><strong>VAT (7.5%):</strong> <span>{{ currency(vat) }}</span></div>
                        <div class="d-flex justify-content-between border-top pt-2"><strong>Total Return:</strong> <span>{{ currency(total) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-success mt-3" type="submit">Submit Return</button>
    </form>
</section>
</template>
<script>
export default {
    computed:{
        subtotal(){ 
            if (this.returnOrderData.return_items == null || this.returnOrderData.return_items.length == 0) {
                return 0;
            }
            return this.returnOrderData.return_items.reduce((s,i)=>s+this.itemSubtotal(i),0); 
        },
        vat(){ return this.subtotal * 0.075; },
        total(){ return this.subtotal + this.vat; }
    },
    data(){
        return{
            all_orders: [],
            customers: [],
            items:[],
            loading:false,
            orders: [],
            price_list_id:"",
            price_lists:[],
            returnOrderData: new Form({
                customer_id: '',
                date: '',
                price_list_id: '',
                return_items:[{ item_name:"", item_id:"", search:"", quantity:1, unit_price:0, discount:0, reason: '', }],
                sales_order_id: '',
            }),
            sales_order_id:"",
            searchedItems:{},
            stores: [],
        }
    },
    emits:['returnFormRefresh'],
    methods:{
        createReturn(){
            try {
                this.loading = true;
                this.returnOrderData.sales_order_id = this.sales_order_id;
                this.returnOrderData.amount = this.total;
                this.returnOrderData
                this.returnOrderData.post('/api/sales/returns')
                .then(response =>{
                    this.$emit('returnFormRefresh', response);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Return Order has been created',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.returnOrderData.reset();
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
        async loadInitials(){
            this.loading = true;
            const res = await axios.get(`/api/sales/returns/initials`);
            this.customers = res.data.customers;
            this.items = res.data.items;
            this.all_orders = res.data.orders;
            this.orders = this.returnOrderData.customer_id
                ? this.all_orders.filter(o => o.customer_id == this.returnOrderData.customer_id)
                : this.all_orders;
            this.price_lists = res.data.price_lists;
            this.stores = res.data.stores;
            this.loading = false;
        },
        async searchItems(index, query) {
            if (query.length < 2) return;
            var price_list_id = this.returnOrderData.price_list_id == "" ||   this.returnOrderData.price_list_id == null ? 0 : this.returnOrderData.price_list_id;
            var sales_order_id = this.returnOrderData.sales_order_id == "" || this.returnOrderData.sales_order_id == null ? 0 : this.returnOrderData.sales_order_id;
            const res = await axios.get(`/api/inventory/items/quick_search?q=${query}&plan_id=${price_list_id}&so_id=${sales_order_id}`);
            this.searchedItems[index] = res.data.items;
            const selected = res.data.items.find(i => i.name === query);
            if (selected) {
                this.returnOrderData.return_items[index].item_id = selected.id;
                this.returnOrderData.return_items[index].item_name = selected.name;
                this.returnOrderData.return_items[index].unit_price = selected.price ?? selected.unit_price;
            }
        },
        selectItem(index, item){
            const row = this.returnOrderData.return_items[index];
            row.item_id = item.id;
            row.item_name = item.name;
            row.name = item.name;
            row.unit_price = item.unit_price ?? item.price ?? 0;
            this.searchedItems = [];
        },
        addItem(){ 
            this.returnOrderData.return_items.push({ item_id:"", item_name:"", search:"", quantity:1, unit_price:0, discount:0 }); 
        },
        removeItem(i){ 
            this.returnOrderData.return_items.splice(i,1);
        },
        itemSubtotal(i){ 
            return (i.unit_price * i.quantity) - (i.discount || 0);
        },
        updateReturn(){
            this.loading = true;
            this.returnOrderData.sales_order_id = this.sales_order_id;
            this.returnOrderData.amount = this.total;
            this.returnOrderData.put('/api/sales/returns/'+this.return_order.id)
            .then(response =>{
                this.$emit('returnFormRefresh', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Return Order has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
                this.returnOrderData.reset();
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            });
            this.loading = false;
        },
    },
    mounted(){
        this.loadInitials();
    },
    props:{
        editMode: Boolean,
        return_order: Object,
    },
    watch:{
        "returnOrderData.customer_id"(customerId){
            if (!customerId) {
                this.orders = this.all_orders;
                return;
            }
            this.orders = this.all_orders.filter(o => o.customer_id == customerId);
        },
        return_order(){
            if (this.return_order != null){
                this.returnOrderData.fill(this.return_order);
            }
        }
    },
}
</script>