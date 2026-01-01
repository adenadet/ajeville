<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="createBatch">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Purchase Order</label>
                    <select v-if="purchase_order == null || purchase_order.id == null" class="form-control" v-model="fulfillData.po_id">
                        <option value="">--Select Purchase Order </option>
                        <option v-for="purchase_order in purchase_orders" :value="purchase_order.id">{{ purchase_order.name }} [{{ purchase_order.unique_id }}]</option>
                    </select>
                    <div class="form-control" v-if="purchase_order != null">
                        {{ purchase_order.name }} [{{ purchase_order.unique_id }}]
                        <input type="hidden" v-model="fulfillData.po_id">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12"  v-if="purchase_order != null">
                <div class="card" v-for="(order_item, index) in purchase_order.order_items">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">{{fulfillData.items[index].item.item.name}} - {{fulfillData.items[index].item.quantity}} {{fulfillData.items[index].item.package.name}}s</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" @click="addBatch(index)"><i class="fas fa-plus mt-2 fa-sm"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Batch Number</th>
                                    <th>Quantity</th>
                                    <th>Expiry Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(batch, queen) in fulfillData.items[index].batches">
                                    <td>{{ addOne(queen) }}</td>
                                    <td><input class="form-control" type="text" v-model="fulfillData.items[index].batches[queen].batch_number" required/></td>
                                    <td><input class="form-control" type="number" v-model="fulfillData.items[index].batches[queen].quantity" required :max="fulfillData.items[index].item.quantity"/></td>
                                    <td><input class="form-control" type="date" v-model="fulfillData.items[index].batches[queen].expiry_date" required :min="to_day"/></td>
                                    <td><button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm btn-primary" type="submit">Save</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    computed:{
        to_day(){
            return new Date().toJSON().slice(0, 10);
        },
    },
    data() {
        return {
            fulfillData: new Form({
                po_id: '',
                items: [],
            }),
            loading: false,
            purchase_orders: [],
        }
    },
    emits:['refreshBatchForm'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        addBatch(index){
            this.fulfillData.items[index].batches.push({batch_number: '', quantity: 0, expiry_date: '',});
        },
        createBatch(){
            this.loading = true;
            this.fulfillData.po_id = this.purchase_order.id;
            this.fulfillData.post('/api/procurement/batches')
            .then(response =>{
                this.$emit('refreshBatchForm');
                this.$swal.fire({icon: 'success', title: 'The Fulfillment Created Awaiting confirmation', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;    
        },
        deleteBatch(index, queen){},
        newItem(item){
            let order_item = {
                item: item,
                batches: [
                    {
                        batch_number: '',
                        expiry_date: '',
                        quantity: 0,
                    }
                ]
            };
            this.fulfillData.items.push(order_item);
        }
    },
    props: {
        //editMode: Boolean,
        //item_type: String,
        //item: Object,
        purchase_order: Object,
    },
    watch:{
        purchase_order(){
            var order_items = this.purchase_order.order_items;
            if (this.purchase_order.order_items == null){
                alert("No Items to Fulfill");
            }
            else{
                order_items.forEach(this.newItem)
            }
        },
    }
}
</script>