<template>
    <section class="content overlay-wrapper p-0">
        <div class="modal fade" id="approvalFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Approve Purchase Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormPurchaseOrderApproval :purchase_order.sync="purchase_order" @purchaseOrderReload="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmFulfillFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-purple">
                        <h4 class="modal-title">Confirm Goods Received</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <InventoryFormFulfillment item_type="purchase_order" :item.sync="purchase_order" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="fulfillFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Create GRN</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormBatch :purchase_order.sync="purchase_order" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="modal fade" id="purchaseOrderFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">{{editMode ? 'Update' : 'Add'}} Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormPurchaseOrder :purchase_order.sync="purchase_order" :editMode.sync="editMode" @purchaseOrderReload="getAllInitials()"/>
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
                        <ProcurementFormPurchaseOrderItem :po_id.sync="purchase_order.unique_id" :purchase_order_item.sync="purchase_order_item" :editMode.sync="editMode" @purchaseOrderItemFormReload="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewFulfillModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-purple">
                        <h4 class="modal-title">Fulfill Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormBatch :batch.sync="batch" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div-->
        
        <div class="col-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3"><h3 class="card-title">Purchase Order Details</h3></li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Details</a>
                        </li>
                        <li class="nav-item" v-if="purchase_order.status >= 1">
                            <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">Approval Trail</a>
                        </li>
                        <li class="nav-item" v-if="purchase_order.status >= 3">
                            <a class="nav-link" id="custom-tabs-two-messages-tab" data-toggle="pill" href="#custom-tabs-two-messages" role="tab" aria-controls="custom-tabs-two-messages" aria-selected="false">Goods Received</a>
                        </li>
                        <li class="nav-item" v-if="purchase_order.status >= 2">
                            <a class="nav-link" id="custom-tabs-two-settings-tab" data-toggle="pill" href="#custom-tabs-two-settings" role="tab" aria-controls="custom-tabs-two-settings" aria-selected="false">Invoice Payments</a>
                        </li>
                        <li class="card-tools" v-if="purchase_order != null">
                            <button class="btn btn-xs btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-white mt-2"></i></button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button v-if="purchase_order.status == 1" class="dropdown-item btn btn-block btn-sm" @click="approvePurchaseOrder()"><i class="fa fa-edit mr-1 text-success"></i> Approve Purchase Order</button>
                                <button v-if="purchase_order.status == 0 || purchase_order.status == 10" class="dropdown-item btn btn-block btn-sm" @click="submitPurchaseOrder()"><i class="fa fa-save mr-1 text-warning"></i> Submit Purchase Order</button>
                                <button v-if="purchase_order.status >= 2 && purchase_order.status < 10" class="dropdown-item btn btn-block btn-sm" @click="fulfillPurchaseOrder()"><i class="fa fa-shopping-cart mr-1 text-success"></i> Fulfill Purchase Order</button>
                                <button v-if="purchase_order.status >= 1 && purchase_order.status < 10 " class="dropdown-item btn btn-block btn-sm" @click="rejectPurchaseOrder()"><i class="fa fa-times mr-1 text-danger"></i> Reject Purchase Order</button>
                                <button v-if="purchase_order.status == 0 || purchase_order.status == 10 " class="dropdown-item btn btn-block btn-sm" @click="cancelPurchaseOrder()"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Purchase Order</button>
                                <button v-if="purchase_order.status == 3" class="dropdown-item btn btn-block btn-sm" @click="confirmFulfillment()"><i class="fa fa-edit mr-1 text-success"></i> Confirm Fulfillment</button>
                                <button v-if="purchase_order.status == 4" class="dropdown-item btn btn-block btn-sm" @click="viewFulfillment()"><i class="fa fa-edit mr-1 text-success"></i> View Fulfillments</button>
                            </div>
                        </li>
                    </ul>

                    
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                            <div class="card">
                                <div class="card-header bg-navy">
                                    <h3 class="card-title">Purchase Order Details</h3>
                                    
                                </div>
                                <div class="card-body p-0 container-fluid overlay-wrapper">
                                    <ProcurementDetailPurchaseOrder v-if="purchase_order != null" :purchase_order.sync="purchase_order" view="initials" @purchaseOrderReload="getAllInitials"/>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                            <ProcurementDetailApprovalTrail :approvals.sync="purchase_order.approvals" /> 
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel" aria-labelledby="custom-tabs-two-messages-tab">
                            <div class="card">
                                <div class="card-header bg-purple">
                                    <h3 class="card-title">Goods Received Details</h3>
                                </div>
                                <div class="card-body p-0 container-fluid overlay-wrapper" style="min-height: 500px">
                                    <ProcurementDetailBatchList :batches.sync="purchase_order.batches" source="admin" />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel" aria-labelledby="custom-tabs-two-settings-tab">
                            <!--ProcurementDetailPaymentList /-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
</template>
<script>
import ProcurementDetailPaymentList from '@/procurement/details/PaymentList.vue';
import ProcurementFormPaymentTerm from '@/procurement/forms/PaymentTerm.vue';
import ProcurementFormPurchaseOrder from '@/procurement/forms/PurchaseOrder.vue';
export default {
    components:{ProcurementDetailPaymentList, ProcurementFormPaymentTerm, ProcurementFormPurchaseOrder},
    data() {
        return {
            batch:{},
            editMode: false,
            form: new Form({}),
            loading: false,
            purchase_order: {approvals: [], batches: [],},
            purchase_order_item: {},
            total_sub_total: 0,
            total_price: 0,
        };
    },
    methods: {
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
                        this.getAllInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        closeModal(){
            $('#additionalCostFormModal').modal('hide');
            $('#approvalFormModal').modal('hide');  
            $('#assignVendorFormModal').modal('hide');  
            $('#orderItemFormModal').modal('hide');
            $('#purchaseOrderItemFormModal').modal('hide');
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
                        this.getAllInitials();  
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
        getAllInitials() {
            this.loading = true;
            this.closeModal();
            axios.get('/api/procurement/purchase_orders/'+this.$route.params.id)
            .then(response =>{
                this.purchase_order = response.data.purchase_order;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order was not loaded',
                })
            });
            this.loading = false;
            
        },
        rejectPurchaseOrder(){},
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
                //Send Delete request
                if(result.value){
                    this.form.get('/api/procurement/purchase_orders/submit/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Submitted!', 'Purchase Order has been submitted for approval.', 'success');
                        //this.$route.push('')  
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
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
    },
    mounted(){
        this.getAllInitials();
    }
};
</script>