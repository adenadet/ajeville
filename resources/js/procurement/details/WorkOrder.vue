<template>
    <section class="content">
        <div class="modal fade" id="assignVendorFormModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Assign Vendor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormAssignVendor item_type="work_order" :item.sync="work_order" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="otherCostFormModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Other Cost</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormOtherCost item_type="work_order" :item.sync="work_order" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="workOrderItemFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">{{editMode ? 'Update' : 'Create'}} Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormWorkOrderItem :work_order.sync="work_order" :work_order_unique_id.sync="work_order != null ? work_order.unique_id : 'Error'" :work_order_item.sync="work_order_item" :editMode.sync="editMode" @workOrderReload="getAllInitials()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="callout callout-info no-print">
                        <h5><i class="fas fa-info"></i> Note:</h5>This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
                    </div>
                        
                    <div class="card">
                    <div class="card-header bg-dark no-print">
                        Work Order Detail
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu">
                                <button class="btn btn-block dropdown-item" @click="addWorkOrderItem()"><i class="fa fa-cart-plus mr-1"></i> Add New Item </button>
                                <button class="btn btn-block dropdown-item" @click="assignVendor()"><i class="fa fa-user-tag text-primary mr-1"></i> Assign Vendor </button>
                                <button class="btn btn-block dropdown-item" @click="changeReceiver()"><i class="fa fa-home text-warning mr-1"></i> Change Receiver </button>
                                <button class="btn btn-block dropdown-item" @click="updateOtherCost()"><i class="fa fa-edit text-success mr-1"></i> Update Other Cost </button>
                                <button class="btn btn-block dropdown-item" @click="cancelWorkOrder()"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Work Order </button>
                                <button class="btn btn-block dropdown-item" @click="submitForApproval()" v-if="work_order.status == 0"><i class="fa fa-check mr-1 text-warning"></i> Submit for Approval</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 overlay-wrapper" style="min-height: 600px;">
                        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                        <div class="row" v-else>
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row invoice-info">
                                        <div class="col-sm-4 invoice-col">
                                            Vendor<br />
                                            <address v-if="work_order.vendor != null">
                                                <strong>{{ work_order.vendor.name }}</strong><br>
                                                {{work_order.address}}<br>
                                                Phone: {{ work_order.vendor.phone }}<br>
                                                Email: {{ work_order.vendor.email }}
                                            </address>
                                            <button v-else class="btn btn-primary no-print" type="button" @click="assignVendor()">
                                                Add Vendor
                                            </button>
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-4 invoice-col">
                                            <b>Name: {{ work_order.name }}</b><br>
                                            
                                            <b>Unique ID:</b> {{work_order.unique_id}}<br>
                                            <b>Payment Due:</b> {{ work_order.delivery_date }}<br>
                                            <b>Created By: {{ work_order.creator != null ? FullName(work_order.creator) : ''  }}</b><br />
                                            <b>Approved At: {{ work_order.creator != null ? FullName(work_order.creator) : ''  }}</b> 
                                        </div>
                                        <div class="col-sm-4 invoice-col">
                                            To <br />
                                            <address v-if="work_order.department != null">
                                                <strong>{{ work_order.department.name }}</strong><br>
                                                {{ work_order.department.branch != null ? work_order.department.branch.address : '57, Campbell Street, Lagos Island, Lagos' }}<br>
                                            </address>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <div class="row">
                                        <div class="col-12 table-responsive">
                                            <table class="table table-striped table-hovered" v-if="work_order.order_items != null">
                                                <thead>
                                                    <tr>
                                                        <th class="no-print">
                                                            <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                                <button class="dropdown-item btn btn-block btn-sm" @click="addWorkOrderItem()"><i class="fa fa-edit mr-1 text-primary"></i> Add Item</button>
                                                            </div>
                                                        </th>
                                                        <th>S/N</th>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Unit Price</th>
                                                        <th class="float-right">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="work_order.order_items.length > 0">
                                                    <tr v-for="(order_item, index) in work_order.order_items">
                                                        <td class=" no-print">            
                                                            <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                                <button class="dropdown-item btn btn-block btn-sm" @click="updateWorkOrderItem(order_item)"><i class="fa fa-edit mr-1 text-success"></i> Update Item</button>
                                                                <button class="dropdown-item btn btn-block btn-sm" @click="deleteWorkOrderItem(order_item.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Item</button>
                                                            </div>
                                                        </td>
                                                        <td>{{ addOne(index) }}</td>
                                                        <td v-html="order_item.item"></td>
                                                        <td>{{ order_item.quantity }}</td>
                                                        <td>{{ currency(order_item.unit_price) }}</td>
                                                        <td class="float-right">{{ currency(order_item.unit_price * order_item.quantity) }}</td>
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <tr><td colspan="6">No Items have been added to this request.</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="lead">Payment Methods:</p>
                                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">{{ work_order.payment_term != null ? work_order.payment_term.name : 'None Specified' }}</p>
                                            <p v-if="work_order.details != null" class="lead">Additional Details:</p>
                                            <div v-html="work_order.details"></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="table-responsive p-0">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr><td style="width:50%">Subtotal:</td><td class="float-right">{{ currency(total_sub_total) }}</td></tr>
                                                        <tr><td>Tax </td><td class="float-right">{{ currency(work_order.taxes) }}</td></tr>
                                                        <tr><td>Logistics:</td><td class="float-right">{{ currency(work_order.logistics) }}</td></tr>
                                                        <tr><td>Discount:</td><td class="float-right">{{ currency(work_order.discount) }}</td></tr>
                                                        <tr><td>Total:</td><td class="float-right">{{currency(total_price)}}</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import ProcurementDetailStatusRibbon from '../details/StatusRibbon.vue' 
import ProcurementFormAdditionalCost from '../forms/AdditionalCost.vue' 
import ProcurementFormAssignStore from '../forms/AssignStore.vue' 
import ProcurementFormAssignVendor from '../forms/AssignVendor.vue' 
import ProcurementFormOtherCost from '../forms/OtherCost.vue' 
import ProcurementFormWorkOrderItem from '../forms/WorkOrderItem.vue'
export default {
    components:{
        ProcurementFormAssignVendor, ProcurementFormOtherCost, ProcurementFormWorkOrderItem, 
    },
    data() {
        return {
            editMode: false,
            form: new Form({
                status: 1,
            }),
            loading: false,
            work_order_item: {},
            total_sub_total: 0,
            total_price: 0,
        };
    },
    methods: {
        addWorkOrderItem(){
            this.loading = true;
            this.work_order_item = {};
            this.editMode = false;
            $('#workOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        assignVendor(){
            this.loading = true;
            this.editMode = false;
            $('#assignVendorFormModal').modal('show');  
            this.loading = false;
        },
        closeModal(){
            $('#assignVendorFormModal').modal('hide');  
            $('#orderItemFormModal').modal('hide');
            $('#otherCostFormModal').modal('hide');
            $('#workOrderItemFormModal').modal('hide');
        },
        deleteWorkOrderItem(id) {
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
                    this.form.delete('/api/procurement/work_order_items/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Item has been deleted.', 'success');
                        this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials() {
            this.loading = true;
            this.closeModal();
            axios.get('/api/procurement/work_orders/'+this.$route.params.id)
            .then(response =>{
                this.work_order = response.data.work_order;
                let order_items = response.data.work_order.order_items;
                let sum = 0;
                for (let i = 0; i < order_items.length; i++) {
                    if (order_items[i].total_price == null){
                        sum = order_items[i].unit_price * (order_items[i].approved_quantity != 0 ? order_items[i].approved_quantity : order_items[i].quantity)
                    }
                    else{
                        sum = order_items[i].total_price
                    }
                    this.total_sub_total += sum;
                }
                this.total_price = this.total_sub_total + (this.work_order.taxes != null ? this.work_order.taxes : 0) + (this.work_order.logistics != null ? this.work_order.logistics : 0) - (this.work_order.discount != null ? this.work_order.discount : 0);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Tickets were not loaded successfully',
                })
            });
        },
        submitForApproval(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send for approval!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.put('/api/procurement/work_orders/'+this.work_order.id)
                    .then(response=>{
                        this.$swal.fire('Done!', 'Work Order has been sent for approval.', 'success');
                        this.getAllInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href="#">Why do I have this issue?</a>'});
                    });
                }
            });
        },
        updateOtherCost(){
            this.loading = true;
            this.editMode = true;
            $('#otherCostFormModal').modal('show');  
            this.loading = false;
        },
        updateWorkOrderItem(work_order_item){
            this.loading = true;
            this.work_order_item = work_order_item;
            this.editMode = true;
            $('#workOrderItemFormModal').modal('show');  
            this.loading = false;
        }
        
    },
    mounted(){
        //this.getAllInitials();
    },
    props:{
        work_order: Object,
    }
};
</script>