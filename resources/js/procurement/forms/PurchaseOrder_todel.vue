<template>
    <section class="row">
        <div class="col-lg-12 col-12 col-sm-6">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Purchase Order Details</h3>
                </div>
                <form>
                    <div class="card-body p-0 overlay-wrapper">
                        <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                        <div class="row p-0">
                            <div class="col-md-12">
                                <div class="card card-tabs">
                                    <div class="card-header">
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
                                            <!-- Summary Details of the Purchase Order-->
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
                                            <!-- Item List of the Purchase Order-->
                                            <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
                                                <div class="row">
                                                    <ProcurementFormPurchaseOrderItemList :purchase_order_id.sync="purchase_order.unique_id" @purchaseOrderReload=""/>
                                                </div>
                                            </div>

                                            <!-- Vendor of the Purchase Order-->
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
                                                            <input v-if="editMode" type="number" class="form-control" name="taxes" id="taxes" v-model="purchaseOrderData.taxes" placeholder="Taxes">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="taxes" id="taxes" v-model="purchaseOrderData.taxes" placeholder="Taxes">
                                                                <div class="form-control">{{ purchaseOrderData.taxes }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Logistics</label>
                                                            <input v-if="editMode" type="number" class="form-control" name="logistics" id="logistics" v-model="purchaseOrderData.logistics" placeholder="Logistics">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="logistics" id="logistics" v-model="purchaseOrderData.logistics" placeholder="Logistics">
                                                                <div class="form-control">{{ purchaseOrderData.logistics }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label for="po_number">Discount</label>
                                                            <input v-if="editMode" type="number" class="form-control" name="discount" id="discount" v-model="purchaseOrderData.discount" placeholder="Discount">
                                                            <div v-else>
                                                                <input type="hidden" class="form-control" name="discount" id="discount" v-model="purchaseOrderData.discount" placeholder="Discount">
                                                                <div class="form-control">{{ purchaseOrderData.discount }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--div class="row">
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
                                                </div-->
                                            </div>
                                            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                                <ProcurementDetailPurchaseOrderSummary :purchase_order.sync="purchase_order" />
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>
<script>
export default {
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
            additional_costs: [],
            form: new Form({}),
            loading: true,
            payment_terms: [],
            purchaseOrderData: new Form({
                id: '',
                additional_cost: '',
                date: '',  
                delivery_date: '',
                description: '', 
                logistics: '', 
                name: '',
                order_items:[],
                payment_term_id: '',
                status: '',
                store_id: 1,
                taxes: '',
                type_id: '',
                unique_id: '',
                vendor_id: '',
            }),
            purchase_order_item: {},
            purchase_order_item_editMode: false,
            stores: [],
            total_sub_total: 0,
            total_price: 0,
            vendors: [],
        };
    },
    emits:['purchaseOrderReload'],
    methods: {
        addPurchaseOrderItem(){
            this.loading = true;
            this.purchase_order_item_editMode = false
            this.purchase_order_item = {};
            $('#purchaseOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        approvePurchaseOrder(){
            this.loading = true;
            $('#approvalFormModal').modal('show');  
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
            $('#confirmFulfillFormModal').modal('hide');  
            $('#orderItemFormModal').modal('hide');
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
                        this.getInitials();   
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
        getAllInitials(page=1){
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
        getInitials(response){
            this.loading = true;
            axios.get('/api/procurement/purchase_orders/'+purchase_order.unique_id)
            .then(response =>{
                this.purchaseOrderData.fill(response.data.purchase_order);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Form not loaded successfully',
                })
            });
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
        refreshPage(response){
            this.categories = response.data.categories;
            this.payment_terms = response.data.payment_terms;
            this.stores = response.data.stores;
            this.vendors = response.data.vendors;
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
        this.getAllInitials();
    },
    props:{
        editMode: Boolean,
        purchase_order: Object,
        source: String,
        view: String,
    },
    watch:{
        purchase_order() {
            this.loading = true;
            this.closeModals();
            this.purchaseOrderData.fill(this.purchase_order);
            if (this.purchase_order.order_items == null){
                this.purchaseOrderData.order_items = [];
            }
            else{
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