<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="fulfillmentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fulfill Item: {{current_item.item != null ? current_item.item.name : ''}} - {{current_item.total_quantity}} units </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <InventoryFormFulfill type="sold" :store_id.sync="order.store_id" :item.sync="current_item" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><strong>{{ order.unique_id }}</strong></h3>
                </div>                    
                <div class="card-body">
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item" v-for="item in order.order_items" >
                            <b>{{ item.item != null ? item.item.name : item.item_name }}</b> <a class="float-right">{{ item.quantity }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card pb-0" v-for="(item,index) in order.order_items">
                <div class="card-header" id="headingOne">
                    <h3 class="card-title text-small">{{ item.item != null ? item.item.name : item.item_name }} - {{ item.total_quantity }} units [{{item.fulfilled_quantity}} fulfilled]</h3>
                    <div class="card-tools"><button type="button" class="btn btn-xs btn-primary" @click="addFulfillment(item)" :disabled="item.fulfilled_quantity >= item.total_quantity"><i class="fa fa-plus mr-1"></i> Fulfill</button></div> 
                </div>
                <div class="card-body p-0">
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Batch ID</th>
                                        <th>Quantity</th>
                                        <th>By</th>
                                    </tr>
                                </thead>
                                <tbody v-if="orderFulfillmentData.items[index].fulfillments != null">
                                    <tr v-for="(fulfillment, quest) in orderFulfillmentData.items[index].fulfillments">
                                        <td>{{addOne(quest)}}</td>
                                        <td>{{ fulfillment.store_item_batch != null && fulfillment.store_item_batch.batch != null ? fulfillment.store_item_batch.batch.batch_number+' ['+fulfillment.store_item_batch.batch.expiry_date+']' : '' }}</td>
                                        <td>{{ fulfillment.quantity }}</td>
                                        <td>{{ FullName(fulfillment.creater) }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr><td colspan="4">Make Fulfillment</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import ApprovalFormSalesOrder from '@/approvals/forms/SalesOrder.vue';
import InventoryFormFulfill from '@/inventory/forms/Fulfill.vue';
import SalesFormOrder from '@/sales_orders/forms/Order.vue';
export default {
    components: {ApprovalFormSalesOrder, InventoryFormFulfill, SalesFormOrder},
    data() {
        return {
            current_item: {},
            order: {},
            items: [],
            loading: false,
            orderFulfillmentData: new Form({
                ref_id: '',
                ref_type: '',
                items: [
                    {},
                ],
            }),
            order_items: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addFulfillment(item){
            this.loading = true;
            this.current_item = item;
            $('#fulfillmentModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#fulfillmentModal').modal('hide');
        },
        confirmFulfillment(){
            this.loading = true;
            this.orderFulfillmentData.ref_id = this.item.id;
            this.orderFulfillmentData.ref_type = this.item_type;
            this.orderFulfillmentData.put('/api/sales/order_fulfillment/'+this.order.id)
            .then(response =>{
                //this.$emits('refreshPage');
                this.$swal.fire({icon: 'success', title: 'The Fulfillment Created Has Been Confirmed', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;
                
        },
        deleteBatch(index, queen){},
        getAllInitials() {
            this.loading = true 
            this.closeModals();
            axios.get('/api/sales/orders/'+ this.$route.params.id)
            .then(response => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Sales Order loaded successfully',
                });
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Sales Order was not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response){
            this.order = response.data.order;
            this.orderFulfillmentData.items = response.data.order.order_items;
            //this.closeModals();
        },
        updatePage(reply){
            this.closeModals();
            if (typeof reply === 'string') {
                return;
            }

            // Case 2: reply contains fulfillmentData
            if (reply && reply.fulfillments) {
                // Find the index of the item currently being fulfilled
                const index = this.order.order_items.findIndex(
                    (itm) => itm.item_id === this.current_item.item_id
                );

                if (index !== -1) {
                    const itemQuantity = this.current_item.quantity;

                    // Ensure orderFulfillmentData.items[index] exists
                    if (!this.orderFulfillmentData.items[index]) {
                        this.orderFulfillmentData.items[index] = {
                            item_id: this.current_item.item_id,
                            quantity: itemQuantity,
                            fulfillments: []
                        };
                    }

                    const targetItem = this.orderFulfillmentData.items[index];

                    // Merge fulfillments
                    reply.fulfillments.forEach(newFulfillment => {
                        const existing = targetItem.fulfillments.find(
                            f => f.batch_id === newFulfillment.batch_id
                        );

                        if (existing) {
                            existing.quantity += newFulfillment.quantity;
                        } else {
                            targetItem.fulfillments.push({ ...newFulfillment });
                        }
                    });

                    //Cap total fulfilled quantity at item.quantity
                    let totalAllocated = targetItem.fulfillments.reduce((sum, f) => sum + f.quantity, 0);

                    if (totalAllocated > itemQuantity) {
                        let excess = totalAllocated - itemQuantity;

                        // Walk backwards reducing until within limit
                        for (let i = targetItem.fulfillments.length - 1; i >= 0 && excess > 0; i--) {
                            const alloc = targetItem.fulfillments[i];
                            const reducible = Math.min(alloc.quantity, excess);
                            alloc.quantity -= reducible;
                            excess -= reducible;
                        }

                        // Remove any zero allocations
                        targetItem.fulfillments = targetItem.fulfillments.filter(f => f.quantity > 0);

                        if (this.$toast) {
                            this.$toast.warning(
                                `Only ${itemQuantity} units accepted for item ${this.current_item.item_id}. Extra quantity was trimmed.`
                            );
                        } else {
                            alert(
                                `Only ${itemQuantity} units accepted for item ${this.current_item.item_id}. Extra quantity was trimmed.`
                            );
                        }
                    }
                }
            }
        }
    },
}
</script>