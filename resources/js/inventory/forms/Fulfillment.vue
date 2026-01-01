<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="confirmFulfillment">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{item_type == 'purchase_order' ? 'Purchase Order' : (item_type == 'transfer_order' ? 'Transfer Request' : '') }}</label>
                    <div class="form-control">
                        {{ item.name }} [{{ item.unique_id }}]
                        <input type="hidden" v-model="fulfillmentData.ref_id">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card" v-for="(order_item, index) in fulfillmentData.items">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">{{order_item.item.name}} - {{ order_item.approved_quantity }}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="25%">Batch Number</th>
                                    <th width="25%">Quantity</th>
                                    <th width="25%">Expiry Date</th>
                                    <th width="15%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(batch, queen) in order_items[index].batches">
                                    <td>{{ addOne(queen) }}</td>
                                    <td><p class="form-control" v-html="order_items[index].batches[queen].batch_number"></p></td>
                                    <td><p class="form-control" v-html="order_items[index].batches[queen].quantity"></p></td>
                                    <td><p class="form-control" v-html="ExcelDate(order_items[index].batches[queen].expiry_date)"></p></td>
                                    <td>
                                        {{ order_items[index].batches[queen].status}} - {{ fulfillmentData.items[index].batches[queen].status }}
                                        <div class="form-control" v-if="order_items[index].batches[queen].status != 0 && order_items[index].batches[queen].status != null">
                                            {{order_items[index].batches[queen].status == 1 ? 'Confirmed' : 'Rejected'}}
                                        </div>
                                        <select v-else class="form-control" v-model="fulfillmentData.items[index].batches[queen].status" required>
                                            <option value="0">--Confirm Receiving--</option>
                                            <option value="1">Confirm</option>
                                            <option value="10">Reject</option>
                                        </select>
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
    data() {
        return {
            items: [],
            loading: false,
            fulfillmentData: new Form({
                ref_id: '',
                ref_type: '',
                items: [],
            }),
            order_items: [],
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
        confirmFulfillment(){
            this.loading = true;
            this.fulfillmentData.ref_id = this.item.id;
            this.fulfillmentData.ref_type = this.item_type;
            this.fulfillmentData.put('/api/procurement/batches/'+this.item.id)
            .then(response =>{
                this.$emits('refreshPage');
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Fulfillment Created Has Been Confirmed', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        deleteBatch(index, queen){},
        getAllInitials(id) {
            this.loading = true;
            axios.get('/api/procurement/batches/'+id+'?t='+this.item_type)
            .then(response => {
                this.loading = true;
                this.order_items = response.data.items;
                response.data.items.forEach(this.newItem)
                this.fulfillmentData.items = response.data.items; 
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        newItem(item){
            let order_item = {
                item: {
                    id: item.id,
                    name: item.name,
                }, 
                batches: item.batches,
            };
            this.fulfillmentData.items.push(order_item);
        },
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
    },
    props: {
        item_type: String,
        item: Object,
    },
    watch:{
        item(){
            this.getAllInitials(this.item.id);
        },
    }
}
</script>