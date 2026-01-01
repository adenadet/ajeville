<template>
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
    <section class="row">
        <div class="col-lg-12 col-12 col-sm-6">
            <div class="card card-tabs">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Purchase Order Details</h3>
                </div>
                <form>
                    <div class="card-body p-0 overlay-wrapper">
                        <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
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
                                            <li class="nav-item">
                                                <a class="nav-link" id="review-tab" data-toggle="pill" href="#review" role="tab" aria-controls="review" aria-selected="false">Review</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content bg-grey p-0 m-0" id="custom-tabs-five-tabContent">
                                            <!--div class="tab-pane fade" id="approval" role="tabpanel" aria-labelledby="approval-tab" v-if="editMode">
                                                <ProcurementDetailPurchaseOrderApprovalList />
                                            </div-->
                                            <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
                                                <div class="row">
                                                    <div class="modal fade" id="itemModal">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-dark">
                                                                    <h4 class="modal-title" v-show="editMode">Edit Item: {{item.name}}</h4>
                                                                    <h4 class="modal-title" v-show="!editMode">New Item</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <InventoryFormItem :editMode="editMode" :item.sync="item" @itemReload="getInitials(current_page)"/> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-header bg-dark">
                                                                <h3 class="card-title">List of Items</h3>
                                                                <div class="card-tools">
                                                                    <button class="btn btn-tool" type="button" @click="addPurchaseOrderItem()"><i class="fa fa-plus" title="Add New Item"></i></button>
                                                                </div>                                                    
                                                            </div>
                                                            <div class="card-body table-responsive p-0" style="height: 500px;">                                  
                                                                <table class="table table-striped table-hovered">
                                        <thead>
                                            <tr>
                                                <th class="no-print" v-if="view != 'direct'">
                                                    <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                                                        <i class="fa fa-ellipsis-v text-dark"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <button class="dropdown-item btn btn-block btn-sm" @click="addPurchaseOrderItem()"><i class="fa fa-edit mr-1 text-primary"></i> Add Item</button>
                                                    </div>
                                                </th>
                                                <th v-else>&nbsp;</th>
                                                <th>S/N</th>
                                                <th>Product</th>
                                                <th>Package</th>
                                                <th class="float-right">Quantity</th>
                                                <th v-if="purchase_order.status == 3">Fulfilled</th>
                                                <th>Unit Price</th>
                                                <th class="float-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="purchase_order.order_items != null && purchase_order.order_items.length > 0">
                                            <tr v-for="(order_item, index) in purchase_order.order_items">
                                                <td class=" no-print">            
                                                    <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                                                        <i class="fa fa-ellipsis-v text-dark mt-2"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="view != 'direct'">
                                                        <button v-if="purchase_order.status == 3" class="dropdown-item btn btn-block btn-sm" @click="generateGRN(order_item)"><i class="fa fa-edit mr-1 text-success"></i> Receive Item</button>
                                                        <button v-if="purchase_order.status < 3 || purchase_order.status == 10" class="dropdown-item btn btn-block btn-sm" @click="updatePurchaseOrderItem(order_item)"><i class="fa fa-edit mr-1 text-success"></i> Update Item</button>
                                                        <button v-if="purchase_order.status < 3 || purchase_order.status == 10" class="dropdown-item btn btn-block btn-sm" @click="deletePurchaseOrderItem(order_item.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Item</button>
                                                    </div>
                                                </td>
                                                <td>{{ addOne(index) }}</td>
                                                <td>{{ order_item.item != null ? order_item.item.name :'Invalid Item' }}<br /><span>{{ order_item.item != null ? order_item.item.unique_id :'Invalid Item' }}</span></td>
                                                <td>{{ order_item.package != null ? order_item.package.name + ' of '+order_item.package_quantity+' units' : 'Units' }}</td>
                                                <td class="float-right">{{ order_item.quantity }}</td>
                                                <td v-if="purchase_order.status==3">0</td>
                                                <td>{{ currency(order_item.unit_price) }}</td>
                                                <td class="float-right">{{ currency(order_item.unit_price * order_item.quantity) }}</td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr >
                                                <td colspan="6">
                                                    No Items have been added to this request.
                                                </td>
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
                                                            <label for="po_number">PO Name</label>
                                                            <input type="text" class="form-control" name="name" id="name" v-model="purchaseOrderData.name" placeholder="PO Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Type</label>
                                                            <select class="form-control" name="type_id" id="type_id" v-model="purchaseOrderData.type_id">
                                                                <option value="">--Select Type--</option>
                                                                <option value="FPO">Foreign</option>
                                                                <option value="LPO">Local</option>
                                                                <option value="OPO">Ordinary</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">PO Unique ID</label>
                                                            <div>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ purchaseOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Store </label>
                                                            <select class="form-control" name="store_id" id="store_id" v-model="purchaseOrderData.store_id">
                                                                <option value="">--Select Store--</option>
                                                                <option v-for="store in stores" :value="store.id">{{ store.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Payment Terms</label>
                                                            <select class="form-control" name="store_id" id="payment_term_id" v-model="purchaseOrderData.payment_term_id">
                                                                <option value="">--Select Payment Term--</option>
                                                                <option v-for="payment_term in payment_terms" :value="payment_term.id">{{ payment_term.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Date</label>
                                                            <input type="date" class="form-control" name="date" id="date" v-model="purchaseOrderData.date" placeholder="Date">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Other Information</label>
                                                            <QuillEditor class="form-control" contentType="html" name="description" id="description" v-model.content="purchaseOrderData.description" placeholder="Description"></QuillEditor>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <a class="btn btn-dark" id="items-tab" data-toggle="pill" href="#items" role="tab" aria-controls="items" aria-selected="false">Items List</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                                <ProcurementDetailPurchaseOrder :purchase_order="purchaseOrderData" source="view" view="readOnly" />
                                            </div>
                                            <div class="tab-pane fade" id="vendor" role="tabpanel" aria-labelledby="vendor-tab">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="po_number">Vendor</label>
                                                            <select class="form-control" name="vendor_id" id="vendor_id" v-model="purchaseOrderData.vendor_id">
                                                                <option value="">--Select Vendor--</option>
                                                                <option v-for="vendor in vendors" :value="vendor.id">{{ vendor.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="purchaseOrderData.vendor_id != ''">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Taxes</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ purchaseOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Logistics</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ purchaseOrderData.unique_id }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Discount</label>
                                                            <input v-if="editMode" type="text" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="unique_id" id="unique_id" v-model="purchaseOrderData.unique_id" placeholder="PO Number">
                                                                <div class="form-control">{{ purchaseOrderData.unique_id }}</div>
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

export default {
    computed:{
        selected_store(){
            return this.stores.find(store => store.id === this.purchaseOrderData.store_id);
        },
        selected_vendor(){
            return this.vendors.find(vendor => vendor.id === this.purchaseOrderData.vendor_id);
        },
        verified(){
            if (this.purchaseOrderData.name == ''){return false;}
            if (this.purchaseOrderData.items.length == 0){return false;}
            else{return true;}
        },
        super_verified(){
            if (this.purchaseOrderData.type_id == ''){return false;}
            else if (this.purchaseOrderData.payment_term_id == ''){return false;}
            else if (this.purchaseOrderData.vendor_id == ''){return false;}
            else{return true;}
        }
    },
    data(){
        return  {
            additional_costs:[],
            categories: [],
            current_page: 1,
            form: new Form({}),
            item:{},
            loading: false,
            payment_terms: [],
            purchaseOrderData: new Form({
                id: '',
                additional_cost: '',
                date: '',  
                delivery_date: '',
                description: '', 
                items:[],
                logistics: '', 
                name: '',
                payment_term_id: '',
                status: '',
                store_id: 1,
                taxes: '',
                type_id: '',
                unique_id: '',
                vendor_id: '',
            }),
            purchase_order_item: {},
            stores: [],
            vendors: [],
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addPurchaseOrderItem(){
            this.loading = true;
            this.purchase_order_item = {};
            this.editMode = false;
            $('#purchaseOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        addItem(new_item){
            this.closeModals();
            var item = this.purchaseOrderData.items.find(item => item.id === new_item.id);
            var index = this.purchaseOrderData.items.map(function(o) { return o.id; }).indexOf(new_item.id);
            if (index < 0){
                console.log("Working")
                this.purchaseOrderData.items.push({
                    id: new_item.item_id, 
                    name: new_item.item.name, 
                    quantity: new_item.quantity, 
                    unique_id:new_item.item.unique_id,
                    unit_price: new_item.unit_price,
                    approved_quantity: new_item.approved_quantity,
                    total_quantity: new_item.total_quantity,
                    total_price: new_item.total_price,
                })
            }
            else{
                this.purchaseOrderData.items[index].quantity++;
            }
            //this.purchaseOrder.items.push(item);
        },
        addNewItem(){
            this.purchase_order_item = {};
            $('#purchaseOrderItemFormModal').modal('show');
        },
        closeModals(){
            $('#vendorFormModal').modal('hide');
            $('#vendorModal').modal('hide');
        },
        createPurchaseOrder(){
            this.loading = true;
            this.purchaseOrderData.post('/api/procurement/purchase_orders')
            .then(response =>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Purchase Order created successfully',
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
                    title: 'Purchase Order not created successfully',
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
                    this.form.delete('/api/inventory/stores/'+id)
                    .then(response=>{
                        Fire.$emit('storeReload', response);  
                        Swal.fire('Deleted!', 'Category has been deleted.', 'success');
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
            axios.get('/api/procurement/purchase_orders/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            this.payment_terms = response.data.payment_terms;
            this.stores = response.data.stores;
            this.vendors = response.data.vendors;
        },
        removeItem(index){
            this.purchaseOrder.items.splice(index, 1);
        },
        saveAsDraft(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Purchase Order will be treated as incomplete and nor processed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, keep in drafts!'
            })
            .then((result) => {
                if(result.value){
                    this.purchaseOrderData.status = 0;
                    this.editMode ? this.updatePurchaseOrder() : this.createPurchaseOrder();
                }
            });
        },
        saveAsQuickPurchase(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Purchase Order will not be sent for approval, and it will be processed directly.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, process it it!'
            })
            .then((result) => {
                if(result.value){
                    this.purchaseOrderData.status = 2;
                    this.editMode ? this.updatePurchaseOrder() : this.createPurchaseOrder();
                }
            });
        },
        sendForApproval(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Purchase Order will be sent to through the approval line before it can be processed further.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send for approval!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.purchaseOrderData.status = 1;
                    this.editMode ? this.updatePurchaseOrder() : this.createPurchaseOrder();
                }
            });
        },
        updatePurchaseOrder(){
            this.loading = true;
            this.purchaseOrderData.put('/api/procurement/purchase_orders/'+this.purchaseOrderData.id)
            .then(response =>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Purchase Order updated successfully',
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
                    title: 'Purchase Order not created successfully',
                })
            });
        },
    },
    props:{
        editMode: Boolean,
        purchase_order: Object,
        source: String,
    },
    watch:{
        purchase_order(){
            this.purchaseOrderData.fill(this.purchase_order);
            if (this.purchase_order.items == null){
                this.purchaseOrderData.items = [];
            }
            else{
                this.purchaseOrderData.items = this.purchase_order.items
            }   
            this.purchaseOrderData.unique_id = this.purchase_order.unique_id; 
        }
    },
}
</script>
