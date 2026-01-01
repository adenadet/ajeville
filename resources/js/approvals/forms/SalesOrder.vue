<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Sales Order</label>
                    <div class="form-control">
                        {{ order.unique_id }}
                    </div>
                </div>
            </div> 
            <div class="col-md-12">
                <div class="form-group">
                    <label>Decision</label>
                    <select class="form-control" id="decision" name="decision" v-model="approvalData.decision">
                        <option value="">--Select Decision--</option>
                        <option value="confirm">Confirm</option>
                        <option value="reject">Reject</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Remark</label>
                    <QuillEditor class="form-control" contentType="html" name="remarks" id="remarks" v-model.content="approvalData.remarks" placeholder="Description"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="invoice p-0 mb-3" v-if="approvalData.decision == 'confirm'">
            <div class="row">
                <div class="col-12 table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Unit Price</th>
                                <th>Requested Qty</th>
                                <th>Approved Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody v-if="order.order_items != null && order.order_items.length > 0">
                            <tr v-for="(order_item, index) in order.order_items" :key="index">
                                <td>{{ order_item.item_name != null ? order_item.item_name : order_item.item.name}}</td>
                                <td>{{ order_item.package.name }} of {{ order_item.package_quantity }}</td>
                                <td>{{ currency(order_item.unit_price) }}</td>
                                <td>{{ order_item.requested_quantity}}</td>
                                <td><input type="number" v-model="approvalData.order.order_items[index].approved_quantity" class="form-control"/></td>
                                <td>{{ currency(order_item.quantity *  order_item.unit_price * order_item.package_quantity)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 float-right">
                <button class="btn btn-primary" @click="approveRequest">Submit</button>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            approvalData: new Form({
                decision: '',
                remarks: '',
                order: {},
            }),
            editMode: false,
            form_type: '',
            loading: false, 
        }
    },
    mounted() {},
    emits: ['approvalSalesReload'],
    methods:{
        approveRequest(){
            this.loading = true;
            this.approvalData.put('/api/approvals/sales_orders/'+this.approvalData.order.id)
            .then(response =>{
                this.loading = false;
                this.$emit('approvalSalesReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Order has been approved',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
            this.loading = false;
        },
    },
    props: {
        order: Object,
    },
    watch:{
        order(){
            this.loading = true;
            this.approvalData.order = this.order;
            this.loading = false;
        }
    }
}
</script>