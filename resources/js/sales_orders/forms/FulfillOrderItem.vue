<template>
<section class="overlay-wrapper p-0">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-5">
            {{ order_item }}
        </div>
        <div class="col-md-7">
            <form @submit.prevent="confirmFulfillment">
                <div class="row">    
                    <button class="btn btn-sm btn-primary float-right" type="button" @click="addBatch(fulfillmentData.fulfillments?.length ?? 1)"> Add New Fulfillment</button>
                    <table class="table table-hover table-striped text-nowrap">
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
                            <tr v-for="(batch, queen) in fulfillmentData.fulfillments" :key="queen">
                                <td>{{ addOne(queen) }}</td>
                                <td>
                                    <select class="form-control" 
                                        v-model="fulfillmentData.fulfillments[queen].batch_id" 
                                        @change="updateDetails(queen)">
                                        <option value="">--Select Batch--</option>
                                        <option 
                                            v-for="batch in batches" 
                                            :key="batch.id" 
                                            :value="batch.id"
                                            :disabled="isBatchSelected(batch.id, queen)">
                                            {{ batch.batch ? batch.batch.batch_number : '' }} - [{{ batch.balance }} available]
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" v-model="fulfillmentData.fulfillments[queen].quantity" class="form-control" />
                                </td>
                                <td>
                                    <div v-html="ExcelDate(fulfillmentData.fulfillments[queen].expiry_date)" class="form-control"></div>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-danger" type="button" @click="deleteBatch(queen)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-sm btn-primary" type="button" @click="sendOut">Edit Sum</button>
                </div>
            </form>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            batches: [],
            loading: false,
            fulfillmentData: new Form({
                fulfillments: [],
                item_id: 0,
                store_id: 0,
                type: '',
            }),
            order_items: [],
        }
    },
    emits: ['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        addBatch(index) {
            this.fulfillmentData.fulfillments.push({
                batch_id: '',
                batch_number: '',
                quantity: 0,
                expiry_date: '',
            });
        },
        confirmFulfillment() {
            this.loading = true;
            this.fulfillmentData.ref_id = this.item.id;
            this.fulfillmentData.ref_type = this.item_type;
            this.fulfillmentData.put('/api/procurement/goods_received/' + this.item.id)
                .then(response => {
                    this.$emit('refreshPage');
                    this.loading = false;
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Fulfillment Created Has Been Confirmed',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(() => {
                    this.loading = false;
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                });
        },
        deleteBatch(index) {
            this.fulfillmentData.fulfillments.splice(index, 1);
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/inventory/store_items/batches/' + this.store_id + '/' + this.item.item_id)
                .then(response => {
                    this.batches = response.data.batches;
                    this.fulfillmentData.item_id = this.item_id;
                    this.fulfillmentData.store_id = this.store_id;
                    this.fulfillmentData.type = this.type;

                    if (this.item.fulfillments != null) {
                        this.fulfillmentData.fulfillments = this.item.fulfillments;
                    } else {
                        this.addBatch(1);
                    }
                })
                .catch(() => {
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Item is not available in the Store',
                    });
                    this.$emit('refreshPage', 'error');
                });
            this.loading = false;
        },
        newItem(item) {
            let order_item = {
                item: {
                    id: item.id,
                    name: item.name,
                },
                batches: item.batches,
            };
            this.fulfillmentData.items.push(order_item);
        },
        sendOut() {
            this.loading = true;
            this.$emit('refreshPage', this.fulfillmentData);
            this.loading = false;
            this.fulfillmentData.reset();
        },
        updateDetails(index) {
            const selectedBatchId = this.fulfillmentData.fulfillments[index].batch_id;

            // ✅ Double protection: check if batch already selected elsewhere
            const duplicateIndex = this.fulfillmentData.fulfillments.findIndex(
                (f, i) => f.batch_id === selectedBatchId && i !== index
            );

            if (duplicateIndex !== -1) {
                this.$swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Batch',
                    text: 'This batch has already been selected in another fulfillment!',
                });
                this.fulfillmentData.fulfillments[index].batch_id = '';
                return;
            }

            const selectedBatch = this.batches.find(b => b.id === selectedBatchId);
            if (selectedBatch) {
                this.fulfillmentData.fulfillments[index].batch_id = selectedBatch.id;
                this.fulfillmentData.fulfillments[index].batch_number = selectedBatch.batch.batch_number;
                this.fulfillmentData.fulfillments[index].quantity = 1;
                this.fulfillmentData.fulfillments[index].expiry_date = selectedBatch.batch.expiry_date;
            }
        },
        addOne(val) {
            return val + 1;
        },
        ExcelDate(dateStr) {
            if (!dateStr) return '';
            return new Date(dateStr).toLocaleDateString();
        },
        // ✅ Used to disable already-selected batches in dropdown
        isBatchSelected(batchId, currentIndex) {
            return this.fulfillmentData.fulfillments.some(
                (f, i) => f.batch_id === batchId && i !== currentIndex
            );
        }
    },
    props: {
        order_item: Object,
    },
    watch:{
        order_item(){
            this.getAllInitials(this.order_item.id);
        },
    }
}
</script>
