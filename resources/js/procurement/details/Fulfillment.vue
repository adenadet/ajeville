<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="confirmFulfillFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Vendor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormGoodsReceivedNote :purchase_order.sync="purchase_order" @refreshPage="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <form @submit.prevent="createGRN">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Purchase Order</label>
                    <select v-if="purchase_order == null || purchase_order.id == null" class="form-control" v-model="fulfillData.po_id">
                        <option value="">--Select Purchase Order </option>
                        <option v-for="purchase_order in purchase_orders" :value="purchase_order.id">{{ purchase_order.name }} [{{ purchase_order.unique_id }}]</option>
                    </select>
                    <div class="form-control">
                        {{ purchase_order.name }} [{{ purchase_order.unique_id }}]
                        <input type="hidden" v-model="fulfillData.po_id">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card" v-for="(order_item, index) in items">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">{{order_item.item.name}}</h3>
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
                                <tr v-for="(batch, queen) in order_item.batches">
                                    <td>{{ addOne(queen) }}</td>
                                    <td><div class="form-control" v-html="batch.batch_number"></div></td>
                                    <td><div class="form-control" v-html="batch.quantity"></div></td>
                                    <td><div class="form-control" v-html="batch.expiry_date"></div></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" type="button" @click="confirmReceipt(batch)">View</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm btn-primary" type="submit">Edit</button>
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
            items: [],
            loading: false,
            purchase_order: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        addBatch(index){
            this.fulfillData.items[index].batches.push({batch_number: '', quantity: 0, expiry_date: '',});
        },
        createGRN(){
            this.loading = true;
            this.fulfillData.po_id = this.purchase_order.id;
            this.fulfillData.post('/api/procurement/goods_received')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Fulfillment Created Awaiting confirmation', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        deleteBatch(index, queen){},
        updateAdditionalCost(){
            this.loading = true;
            this.additionalCostData.put('/api/procurement/purchase_orders/update/'+this.purchase_order.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Vendor has been assigned', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/procurement/vendors/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.vendors = response.data.vendors;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
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