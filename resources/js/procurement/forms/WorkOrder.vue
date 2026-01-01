<template>
    <section class="row">
        <div class="modal fade" id="workOrderItemFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">{{editMode ? 'Update' : 'Create'}} Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ProcurementFormWorkOrderItem :work_order.sync="work_order" :work_order_item.sync="work_order_item" :editMode.sync="editMode" @workOrderReload="addItem" @addItem="addItem" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-12 col-sm-6">
            <div class="card card-tabs">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Work Order Details</h3>
                </div>
                <form>
                    <div class="card-body p-0"> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-tabs">
                                    <div class="card-header p-0">
                                        <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="summary-tab" data-toggle="pill" href="#summary" role="tab" aria-controls="summary" aria-selected="true">Summary</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="items-tab" data-toggle="pill" href="#items" role="tab" aria-controls="items" aria-selected="false">Items List</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="review-tab" data-toggle="pill" href="#vendor" role="tab" aria-controls="vendor" aria-selected="false">Vendor</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content bg-grey p-0 m-0" id="custom-tabs-five-tabContent">
                                            <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-header bg-dark">
                                                                <h3 class="card-title">List of Items</h3>
                                                                <div class="card-tools">
                                                                    <button class="btn btn-tool" type="button" @click="addNewItem()"><i class="fa fa-plus" title="Add New Item"></i></button>
                                                                </div>                                                    
                                                            </div>
                                                            <div class="card-body table-responsive p-0" style="height: 500px;">
                                                                <table class="table table-head-fixed table-bordered table-hover table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>S/N</th>
                                                                            <th>Item Name</th>
                                                                            <th>Quantity</th>
                                                                            <th>Unit Price</th>
                                                                            <th>Sub Total Price</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody v-if="workOrderData.items.length != 0">
                                                                        <tr v-for="(item, index) in workOrderData.items" :key="index">
                                                                            <td>{{ addOne(index) }}</td>
                                                                            <td v-html="workOrderData.items[index].name"></td>
                                                                            <td><input type="number" v-model="workOrderData.items[index].quantity" /></td>
                                                                            <td v-html="currency(workOrderData.items[index].unit_price)"></td>
                                                                            <td v-html="currency(workOrderData.items[index].unit_price * workOrderData.items[index].quantity)"></td>
                                                                            <td>
                                                                                <button type="button" class="btn btn-danger" @click="removeItem(index)"><i class="fa fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <tbody v-else>
                                                                        <tr>
                                                                            <td colspan="6">No Item has been added yet</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="po_number">WO Name</label>
                                                            <input type="text" class="form-control" name="name" id="name" v-model="workOrderData.name" placeholder="PO Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Type</label>
                                                            <select class="form-control" name="type_id" id="type_id" v-model="workOrderData.type_id">
                                                                <option value="">--Select Type--</option>
                                                                <option value="FPO">Foreign</option>
                                                                <option value="LPO">Local</option>
                                                                <option value="OPO">Ordinary</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">WO Unique ID</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ workOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Branch </label>
                                                            <select class="form-control" name="branch_id" id="branch_id" v-model="workOrderData.branch_id">
                                                                <option value="">--Select Branch--</option>
                                                                <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Department </label>
                                                            <select class="form-control" name="department_id" id="department_id" v-model="workOrderData.department_id">
                                                                <option value="">--Select Department--</option>
                                                                <option v-for="department in departments" :value="department.id">{{ department.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Payment Terms</label>
                                                            <select class="form-control" name="store_id" id="payment_term_id" v-model="workOrderData.payment_term_id">
                                                                <option value="">--Select Payment Term--</option>
                                                                <option v-for="payment_term in payment_terms" :value="payment_term.id">{{ payment_term.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Date</label>
                                                            <input type="date" class="form-control" name="date" id="date" v-model="workOrderData.date" placeholder="Date">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Other Information</label>
                                                            <QuillEditor class="form-control" contentType="html" name="description" id="description" v-model.content="workOrderData.description" placeholder="Description"></QuillEditor>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <a class="btn btn-dark" id="items-tab" data-toggle="pill" href="#items" role="tab" aria-controls="items" aria-selected="false">Items List</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                                Put the first row, to be the summary 
                                                <br >Next the list of items to be purchase
                                                <br >Submit button at the bottom.
                                                <div class="invoice p-3 mb-3">
                                                    <div class="row invoice-info">
                                                        <div class="col-sm-4 invoice-col">
                                                        From
                                                        <address>
                                                            <strong>Admin, Inc.</strong><br>
                                                            795 Folsom Ave, Suite 600<br>
                                                            San Francisco, CA 94107<br>
                                                            Phone: (804) 123-5432<br>
                                                            Email: info@almasaeedstudio.com
                                                        </address>
                                                        </div>
                                                        <!-- /.col -->
                                                        <div class="col-sm-4 invoice-col">
                                                        To
                                                        <address>
                                                            <strong>John Doe</strong><br>
                                                            795 Folsom Ave, Suite 600<br>
                                                            San Francisco, CA 94107<br>
                                                            Phone: (555) 539-1037<br>
                                                            Email: john.doe@example.com
                                                        </address>
                                                        </div>
                                                        <!-- /.col -->
                                                        <div class="col-sm-4 invoice-col">
                                                        <b>Invoice #007612</b><br>
                                                        <br>
                                                        <b>Order ID:</b> 4F3S8J<br>
                                                        <b>Payment Due:</b> 2/22/2014<br>
                                                        <b>Account:</b> 968-34567
                                                        </div>
                                                        <!-- /.col -->
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 table-responsive">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Qty</th>
                                                                        <th>Product</th>
                                                                        <th>Serial #</th>
                                                                        <th>Description</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>Call of Duty</td>
                                                                        <td>455-981-221</td>
                                                                        <td>El snort testosterone trophy driving gloves handsome</td>
                                                                        <td>$64.50</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            </div>
                                                            <!-- /.col -->
                                                        </div>
                                                        <!-- /.row -->

                                                        <div class="row">
                                                            <div class="col-6">
                                                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem
                                                                plugg
                                                                dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                                                            </p>
                                                            </div>
                                                            <!-- /.col -->
                                                            <div class="col-6">
                                                                <p class="lead">Amount Due 2/22/2014</p>

                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style="width:50%">Subtotal:</td>
                                                                                <td>$250.30</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Tax (9.3%)</td>
                                                                                <td>$10.34</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Shipping:</td>
                                                                                <td>$5.80</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Total:</td>
                                                                                <td>$265.24</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row no-print">
                                                            <div class="col-12">
                                                            <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                                                            <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
                                                                Payment
                                                            </button>
                                                            <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                                                                <i class="fas fa-download"></i> Generate PDF
                                                            </button>
                                                            </div>
                                                        </div>
                                                        </div>
                                            </div>
                                            <div class="tab-pane fade" id="vendor" role="tabpanel" aria-labelledby="vendor-tab">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="po_number">Vendor</label>
                                                            <select class="form-control" name="vendor_id" id="vendor_id" v-model="workOrderData.vendor_id">
                                                                <option value="">--Select Vendor--</option>
                                                                <option v-for="vendor in vendors" :value="vendor.id">{{ vendor.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="workOrderData.vendor_id != ''">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Taxes</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ workOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Logistics</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ workOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Discount</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="workOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ workOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 table-responsive p-0">
                                                        <div class="card">
                                                            <div class="card-header bg-dark">
                                                                <h3 class="card-title">Additional Cost </h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" ><i class="fas fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-0">
                                                                <table class="table table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Name</th>
                                                                            <th>Cost</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody v-if="additional_costs.length != 0">
                                                                        <tr v-for="(additional_cost, index) in additional_costs" :key="index">
                                                                            <td>{{ addOne(index) }}</td>
                                                                            <td><input type="text" class="form-control" v-model="additional_cost[index].name" /></td>
                                                                            <td><input type="text" class="form-control" v-model="additional_cost[index].name" /></td>
                                                                            <td><button class="btn btn-xs btn-danger"><i class="fa fa-times"></i></button></td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <tbody v-else>
                                                                        <tr>
                                                                            <td colspan="4">No Additional Cost has been added yet</td>
                                                                        </tr>
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
                    <div class="card-footer">
                        <button type="button" class="btn btn-info float-right ml-1" @click="saveAsDraft" :disabled="!verified"><i class="fas fa-save"></i> Save as draft</button>
                        <button type="button" class="btn btn-primary float-right ml-1" @click="sendForApproval" :disabled="!verified"><i class="fas fa-share"></i> Send for Approval</button>
                        <button type="button" class="btn btn-success float-right ml-1" @click="saveAsQuickPurchase" :disabled="!verified"><i class="fas fa-cash-register"></i> Quick Purchase</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';
import ProcurementFormWorkOrderItem from './WorkOrderItem.vue'
export default {
    components:{
        ProcurementFormWorkOrderItem
    },
    computed:{
        verified(){
            if (this.workOrderData.name == ''){return false;}
            if (this.workOrderData.items.length == 0){return false;}
            else{return true;}
        },
        super_verified(){
            if (this.workOrderData.type_id == ''){return false;}
            else if (this.workOrderData.payment_term_id == ''){return false;}
            else if (this.workOrderData.vendor_id == ''){return false;}
            else{return true;}
        }
    },
    data(){
        return  {
            additional_costs:[],
            branches: [],
            categories: [],
            current_page: 1,
            departments: [],
            form: new Form({}),
            item:{},
            loading: false,
            payment_terms: [],
            workOrderData: new Form({
                id: '',
                additional_cost: '',
                date: '',  
                delivery_date: '',
                department_id: '',
                description: '', 
                items:[],
                logistics: '', 
                name: '',
                payment_term_id: '',
                status: '',
                taxes: '',
                type_id: '',
                unique_id: '',
                vendor_id: '',
            }),
            work_order_item: {},
            vendors: [],
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addNewItem(){
            this.work_order_item = {};
            $('#workOrderItemFormModal').modal('show');
        },
        addItem(new_item){
            //alert(new_item);
            //console.log(new_item);
            this.item = {
                name: new_item.item,
                quantity: new_item.quantity,
                unit_price: new_item.unit_price, 
            }
            this.workOrderData.items.push(this.item);
            console.log(this.workOrderData.items)
            this.closeModals();
            //this.purchaseOrder.items.push(item);
        },
        closeModals(){
            $('#vendorFormModal').modal('hide');
            $('#workOrderItemFormModal').modal('hide');
        },
        createWorkOrder(){
            this.loading = true;
            this.workOrderData.post('/api/procurement/work_orders')
            .then(response =>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Work Order created successfully',
                });
                this.workOrder = new Form({
                    items:[],
                    store_id: '',
                    unique_id: '',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Work Order not created successfully',
                })
            });
        },
        deleteVendor(id){
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
                if(result.value){
                    this.form.delete('/api/procurement/vendors/'+id)
                    .then(response=>{
                        Fire.$emit('storeReload', response);  
                        Swal.fire('Deleted!', 'Vendor has been deleted.', 'success');
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/work_orders/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Work Order Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            this.departments = response.data.departments;
            this.payment_terms = response.data.payment_terms;
            this.vendors = response.data.vendors;
        },
        removeItem(index){
            this.workOrderData.items.splice(index, 1);
        },
        saveAsDraft(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Work Order will be treated as incomplete and nor processed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, keep in drafts!'
            })
            .then((result) => {
                if(result.value){
                    this.workOrderData.status = 0;
                    this.editMode ? this.updateWorkOrder() : this.createWorkOrder();
                }
            });
        },
        saveAsQuickPurchase(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Work Order will not be sent for approval, and it will be processed directly.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, process it it!'
            })
            .then((result) => {
                if(result.value){
                    this.workOrderData.status = 2;
                    this.editMode ? this.updateWorkOrder() : this.createWorkOrder();
                }
            });
        },
        sendForApproval(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Work Order will be sent to through the approval line before it can be processed further.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send for approval!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.workOrderData.status = 1;
                    this.editMode ? this.updatePurchaseOrder() : this.createPurchaseOrder();
                }
            });
        },
        updateWorkOrder(){
            this.loading = true;
            this.workOrderData.put('/api/procurement/work_orders/'+this.workOrderData.id)
            .then(response =>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Work Order updated successfully',
                });
                this.purchaseOrder = new Form({
                    items:[],
                    store_id: '',
                    unique_id: '',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Work Order not created successfully',
                })
            });
        },
    },
    props:{
        editMode: Boolean,
        work_order: Object,
        source: String,
    },
    watch:{
        work_order(){
            if (this.work_order != null ){
                this.workOrderData.reset();
                this.workOrderData.fill(this.work_order);
            }
            else{
                this.workOrderData.reset();
            }

        }
    },
}
</script>
