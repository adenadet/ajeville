<template>
    <section class="overlay-wrapper p-0">
        <div class="modal fade" id="additionalCostFormModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Additional Cost</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormAdditionalCost :purchase_order.sync="purchase_order" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="assignStoreFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Assign Store</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormAssignStore :purchase_order_id.sync="purchase_order.unique_id" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="assignVendorFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Assign Vendor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormAssignVendor item_type="purchase_order" :item.sync="purchase_order" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="purchaseOrderFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">{{editMode ? 'Update' : 'Add'}} Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormPurchaseOrder :purchase_order.sync="purchase_order" :editMode.sync="editMode" @purchaseOrderReload="getInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="purchaseOrderItemFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">{{editMode ? 'Update' : 'Add'}} Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormPurchaseOrderItem :po_id.sync="purchase_order.unique_id" :purchase_order_item.sync="purchase_order_item" :editMode.sync="editMode" @purchaseOrderReload="getInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <ProcurementDetailStatusRibbon :order.sync="purchase_order" />
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            Vendor<br />
                            <address v-if="purchase_order.vendor != null">
                                <strong>{{ purchase_order.vendor.name }}</strong><br>
                                {{purchase_order.address}}<br>
                                Phone: {{ purchase_order.vendor.phone }}<br>
                                Email: {{ purchase_order.vendor.email }}
                            </address>
                            <button v-else class="btn btn-primary no-print" type="button" @click="assignVendor()">
                                Add Vendor
                            </button>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            To <br />
                            <address v-if="purchase_order.store != null">
                                <strong>{{ purchase_order.store.name }}</strong><br>
                                {{ purchase_order.store.branch != null ? purchase_order.store.branch.address : '57, Campbell Street, Lagos Island, Lagos' }}<br>
                            </address>
                            <button v-else class="btn btn-sm mt-1 btn-primary no-print" type="button" @click="assignStore()">
                                Add Receiving Store
                            </button>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            <b>Name: {{ purchase_order.name }}</b><br>
                            
                            <b>Unique ID:</b> {{purchase_order.unique_id}}<br>
                            <b>Payment Due:</b> {{ purchase_order.delivery_date }}<br>
                            <b>Account:</b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped table-hovered table-bordered">
                                <thead>
                                    <tr>
                                        <th class="no-print" v-if="source != 'approvals' && purchase_order.status <= 1">
                                            <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                                                <i class="fa fa-ellipsis-v text-dark"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                <button class="dropdown-item btn btn-block btn-sm" @click="addPurchaseOrderItem()"><i class="fa fa-edit mr-1 text-primary"></i> Add Item</button>
                                            </div>
                                        </th>
                                        <th>S/N</th>
                                        <th>Product</th>
                                        <th>Package</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody v-if="purchase_order.order_items != null && purchase_order.order_items.length > 0">
                                    <tr v-for="(order_item, index) in purchase_order.order_items">
                                        <td class=" no-print" v-if="source != 'approvals' && purchase_order.status <= 1">            
                                            <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                                                <i class="fa fa-ellipsis-v text-dark mt-2"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="view != 'direct'">
                                                <button v-if="purchase_order.status >= 2" class="dropdown-item btn btn-block btn-sm" @click="generateGRN(order_item)"><i class="fa fa-edit mr-1 text-success"></i> Receive Item</button>
                                                <button v-if="purchase_order.status < 3 || purchase_order.status == 10" class="dropdown-item btn btn-block btn-sm" @click="updatePurchaseOrderItem(order_item)"><i class="fa fa-edit mr-1 text-success"></i> Update Item</button>
                                                <button v-if="purchase_order.status < 3 || purchase_order.status == 10" class="dropdown-item btn btn-block btn-sm" @click="deletePurchaseOrderItem(order_item.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Item</button>
                                            </div>
                                        </td>
                                        <td>{{ addOne(index) }}</td>
                                        <td>{{ order_item.item != null ? order_item.item.name :'Invalid Item' }}<br /><span>{{ order_item.item != null ? order_item.item.unique_id :'Invalid Item' }}</span></td>
                                        <td>{{ order_item.package != null ? order_item.package.name + ' of '+order_item.package_quantity+' units' : 'Units' }}</td>
                                        <td class="text-right">{{ order_item.quantity }}</td>
                                        <td>{{ currency(order_item.unit_price) }}</td>
                                        <td class="text-right">{{ currency(order_item.unit_price * order_item.quantity) }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr >
                                        <td :colspan="source != 'approvals' && purchase_order.status <= 1 ? 7 : 6">
                                            No Items have been added to this request.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <p class="lead">Payment Methods:</p>
                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">{{ purchase_order.payment_term != null ? purchase_order.payment_term.name : 'None Specified' }}
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="table-responsive p-0">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr><td style="width:50%">Subtotal:</td><td class="float-right">{{ currency(total_sub_total) }}</td></tr>
                                        <tr><td>Tax </td><td class="float-right">{{ currency(purchase_order.taxes) }}</td></tr>
                                        <tr><td>Logistics:</td><td class="float-right">{{ currency(purchase_order.logistics) }}</td></tr>
                                        <tr><td>Discount:</td><td class="float-right">{{ currency(purchase_order.discount) }}</td></tr>
                                        <tr><td>Total:</td><td class="float-right">{{currency(total+total_sub_total)}}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row no-print" v-if="source != 'approvals'">
                        <div class="col-12">
                            <button type="button" class="btn btn-default" v-if="purchase_order.status < 2" @click="updatePurchaseOrder()"><i class="fas fa-edit"></i> Update</button>
                            <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;"><i class="fas fa-download"></i> Generate PDF</button>
                            <button type="button" class="btn btn-success float-right" v-if="purchase_order.status < 2" @click="updateAdditionalCost()"><i class="far fa-edit"></i>Update Other Cost</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import ProcurementDetailStatusRibbon from '@/procurement/details/StatusRibbon.vue';
import ProcurementFormAdditionalCost from '@/procurement/forms/AdditionalCost.vue';
import ProcurementFormAssignStore from '@/procurement/forms/AssignStore.vue';
import ProcurementFormAssignVendor from '@/procurement/forms/AssignVendor.vue';
import ProcurementFormOtherCost from '@/procurement/forms/OtherCost.vue';
import ProcurementFormPurchaseOrder from '@/procurement/forms/PurchaseOrder.vue';
import ProcurementFormPurchaseOrderItem from '@/procurement/forms/PurchaseOrderItem.vue';
export default {
    components:{ProcurementFormAdditionalCost, ProcurementFormAssignVendor, ProcurementFormOtherCost,  ProcurementDetailStatusRibbon, ProcurementFormAssignStore, ProcurementFormPurchaseOrder, ProcurementFormPurchaseOrderItem},
    computed:{
        sub_total(){
            if (this.purchase_order.items == null){
                return 0.00
            }
            else{
                if(this.purchase_order.items.length == 0){
                    return 0.00;
                }
                else{
                    let sum = 0;
                    let i = 0;
                    while (i < this.purchase_order.items.length) {
                        sum = sum + (this.purchase_order.items[i].unit_price * this.purchase_order.items[i].quantity);
                        
                        i++;
                    }
                    return sum;
                }
            }
        },
        total(){
            let sum = 0.00;
            sum = (this.purchase_order.taxes != null ? this.purchase_order.taxes: 0.00) + (this.purchase_order.logistics != null ? this.purchase_order.logistics: 0.00) - (this.purchase_order.discount != null ? this.purchase_order.discount: 0.00) 
            return sum;
        }
    },
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: true,
            purchase_order_item: {},
            total_sub_total: 0,
            total_price: 0,
        };
    },
    emits:['purchaseOrderReload'],
    methods: {
        addPurchaseOrderItem(){
            this.loading = true;
            this.purchase_order_item = {};
            this.editMode = false;
            $('#purchaseOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        approvePurchaseOrder(){
            this.loading = true;
            $('#approvalFormModal').modal('show');  
            this.loading = false;
        },
        assignStore(){
            this.loading = true;
            this.editMode = false;
            $('#assignStoreFormModal').modal('show');  
            this.loading = false;
        },
        assignVendor(){
            this.loading = true;
            this.editMode = false;
            $('#assignVendorFormModal').modal('show');  
            this.loading = false;
        },
        cancelPurchaseOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel Purchase Order!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_orders/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Purchase Order has been cancelled.', 'success');
                        this.getInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        closeModals(){
            $('#additionalCostFormModal').modal('hide');
            $('#approvalFormModal').modal('hide');  
            $('#assignVendorFormModal').modal('hide');  
            $('#assignStoreFormModal').modal('hide');
            $('#confirmFulfillFormModal').modal('hide');  
            $('#orderItemFormModal').modal('hide');
            $('#purchaseOrderFormModal').modal('hide');
            $('#purchaseOrderItemFormModal').modal('hide');
            $('#viewFulfillModal').modal('hide');
        },
        confirmFulfillment(){
            this.loading = true;
            $('#confirmFulfillFormModal').modal('show');  
            this.loading = false;
        },
        deletePurchaseOrder() {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_orders/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Purchase Order has been rejected.', 'success');
                        this.getInitials();  
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        deletePurchaseOrderItem(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_order_items/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Item has been deleted.', 'success');
                        this.getAllInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        fulfillPurchaseOrder(){
            this.loading = true;
            this.editMode = false;
            $('#fulfillFormModal').modal('show');  
            this.loading = false;
        },
        generateGRN(order_item){
            alert("Working")
        },
        getInitials(){
            this.loading = true;
            this.closeModals();
            this.$emit('purchaseOrderReload');
            this.loading = false; 
        },
        rejectPurchaseOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The purchase order will have to be restarted all over!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_orders/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Rejected!', 'The Purchase Order has been rejected.', 'success');
                        this.getInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });

        },
        submitPurchaseOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Submit Purchase Order!'
            })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/procurement/purchase_orders/submit/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Submitted!', 'Purchase Order has been submitted for approval.', 'success');
                        this.getInitials()
                    })
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});});
                }
            });
        },
        updatePurchaseOrder(){
            this.loading = true;
            this.editMode = true;
            $('#purchaseOrderFormModal').modal('show');  
            this.loading = false;
        },
        updatePurchaseOrderItem(purchase_order_item){
            this.loading = true;
            this.purchase_order_item = purchase_order_item;
            this.editMode = true;
            $('#purchaseOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        updateAdditionalCost(){
            this.loading = true;
            $('#additionalCostFormModal').modal('show');  
            this.loading = false;
        },
        viewFulfillment(){
            this.loading = true;
            $('#viewFulfillModal').modal('show');  
            this.loading = false;
        },
    },
    mounted(){
        //this.getAllInitials();
    },
    props:{
        purchase_order: Object,
        source: String,
        view: String,
    },
    watch:{
        purchase_order() {
            this.loading = true;
            this.closeModals();
            if (this.purchase_order.order_items != null){
                let order_items = this.purchase_order.order_items;
                let sum = 0;
                this.total_sub_total = 0;
                for (let i = 0; i < order_items.length; i++) {
                    if (order_items[i].total_price == null || order_items[i].total_price == 0){
                        sum = order_items[i].unit_price * (order_items[i].approved_quantity != 0 ? order_items[i].approved_quantity : order_items[i].quantity)
                    }
                    else{
                        sum = order_items[i].total_price
                    }
                    this.total_sub_total = this.total_sub_total + sum;
                }
            }
            this.loading = false;
        },
    },
};
</script>