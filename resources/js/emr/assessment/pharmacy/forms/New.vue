<template>
<div class="container-fluid">
    <div class="modal fade" id="itemModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Add Item '+product.name :'Add Item'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InvmFormItemSearch />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Make Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <PaymentForm :assignMode="assignMode" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Find Vendor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <VendorFormSearch />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="vendorModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Vendor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <VendorFormBasic />
                </div>
            </div>
        </div>
    </div>
    <form action="#" @submit.prevent="editMode ? editSale() : addSale()">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div><h4 class="mb-3">New Purchase Order</h4></div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="form-group row">
                <label class="col-md-2">Vendor Name *</label>
                <div class="col-md-6 input-group">
                    <select class="form-control" required id="vendor_id" name="vendor_id" v-model="purchaseForm.vendor_id">
                        <option value="">--Select Vendor --</option>
                        <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">{{vendor.name}}</option>
                    </select>
                    <span class="input-group-append">
                        <button type="button" title="Add New" class="btn bg-teal" @click="addVendor()"><i class="fas fa-user-plus"></i></button>
                    </span>
                    <span class="input-group-append">
                        <button type="button" title="Search" class="btn btn-success" @click="searchVendor()"><i class="fas fa-user-circle"></i></button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="form-group row">
                <label class="col-md-2">Purchase Order *</label>
                <div class="col-md-4">
                    <input disabled type="text" class="form-control" id="Purchase_order_id" name="purchase_order_id" placeholder="Purchase Order ID *" required v-model="purchaseForm.Purchase_order_id" :class="{'is-invalid' : purchaseForm.errors.has('Purchase_order_id') }">
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group row">
                <label class="col-md-2">Reference#</label>
                <div class="col-md-4">
                    <input type="number" class="form-control" id="Purchase_order_id" name="purchase_ref_id" placeholder="Enter Reference Number *" required v-model="purchaseForm.Purchase_order_id" :class="{'is-invalid' : purchaseForm.errors.has('Purchase_order_id') }">
                </div>
            </div>
             <div class="form-group row">
                <label class="col-md-2">Purchase Order Date*</label>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="po_date" name="po_date" placeholder="Purchase Order Date *" required v-model="purchaseForm.po_date" :class="{'is-invalid' : purchaseForm.errors.has('po_date') }">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2">Expected Receipt Date</label>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="por_date" name="por_date" placeholder="Enter Phone Number *" required v-model="purchaseForm.por_date" :class="{'is-invalid' : purchaseForm.errors.has('por_date') }">
                </div>
                <label class="col-md-2">Payment Terms</label>
                <div class="col-md-4">
                    <select class="form-control" data-errors="Please Enter Name." required id="payment_terms" name="payment_terms" v-model="purchaseForm.payment_terms" :class="{'is-invalid' : purchaseForm.errors.has('payment_terms') }">
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="form-group row">
                <label class="col-md-2">Delivery Method</label>
                <div class="col-md-4">
                    <select class="form-control" data-errors="Please Enter Name." required id="delivery_id" name="delivery_id" v-model="purchaseForm.delivery_id">
                    <option value="">--Select Delivery Type--</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2">Purchase Person</label>
                <div class="col-md-4">
                    <select class="form-control" data-errors="Please Enter Name." required id="Purchase_person_id" name="Purchase_person_id" v-model="purchaseForm.Purchase_person_id">
                    <option value="">--Select Purchase Person--</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="btn-group">
                <button type="button" class="btn btn-success btn-sm" @click.prevent="addItem"><i class="fas fa-plus"></i>Add Item</button>
                
            </div>
            <div class="table-responsive rounded mb-3">
                <alert-error :form="purchaseForm"></alert-error> 
                <table class="table mb-0 tbl-server-info">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th></th>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body"> 
                        <tr v-for="(item, index) in purchaseForm.items" :key="index">
                            <td>{{index | addOne}}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/odoo/images/table/product/01.jpg" class="img-fluid rounded avatar-50 mr-3" alt="image">
                                    <div>{{item.name}}<p class="mb-0"></p></div>
                                </div>
                            </td>
                            <td>{{item.code}}</td><td>{{item.category.name}}</td>
                            <td><input class="form-control" type="number" name="item_qty" id="item_qty" v-model="item.quantity" /></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-danger" title="Delete" v-on:click="deleteItem(item)"><i class="ri-delete-bin-line mr-0"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-warning btn-sm" @click.prevent="addPayment"><i class="fas fa-plus"></i>Add Payment</button>
            </div>
        </div>
        <div class="col-lg-6">
        </div>
    </div>
    </form>
</div>
</template>
<script>
export default {
    data(){
        return {
            assignMode: true,
            categories: {},
            vendors: [],
            date: '',
            discountForm: new Form({
                'discount_type': 'money',
                'amount': '',
            }),
            editMode: false,
            error: {},
            product: {},
            products: {},
            item_count: 1,
            items: {
                order_id: '',
                product: {},
                quantity: 0,
            },
            purchaseForm: new Form({
                'vendor_id': '',
                'code': '',
                'date_due': '',
                'delivery_id': '',
                'discount': 0.00,
                'issue_date': '',
                'items': [],
                'payment_id': '',
                'phone': '',
                'Purchase_person_id': '',
                'shipping_charge': 0.00,
                'subject': '',
                'sub_total': 0.00,
                'total': 0.00,
            }),   
        }
    },
    methods:{
        addVendor(){
            $('#vendorModal').modal('show');
        },
        addItem(){
            $('#itemModal').modal('show');
        },
        addPayment(){
            $('#paymentModal').modal('show');
        },
        addSale(){
            this.$Progress.start();
            this.purchaseForm.post('/api/inventory/purchases')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success', title: 'The Sale '+ this.purchaseForm.code+' has been created',
                    showConfirmButton: false, timer: 1500
                });
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
            this.$Progress.finish();
        },
        deleteItem(item){
            this.purchaseForm.items.pop(item);
            this.calculateCost();
        },
        editSale(){
            this.$Progress.start();
            this.purchaseForm.post('/api/inventory/purchases')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Sale '+ this.purchaseForm.code+' has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
            this.$Progress.finish();
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/inventory/purchases').then(response =>{
                this.$Progress.finish();
                this.purchaseRefresh(response);
                toast.fire({icon: 'success', title: 'Purchase Form was loaded successfully',});
            })
            .catch(()=>{this.$Progress.fail(); toast.fire({icon: 'error', title: 'Purchase Form was not loaded successfully',})});
        },
        newProduct(){

        },
        purchaseRefresh(response){
            this.vendors = response.data.vendors;
            this.products = response.data.products;    
        },
        searchvendor(){
            $('#searchModal').modal('show');
        },
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('foundVendor', vendor =>{
            this.purchaseForm.vendor_id = vendor.id;
            $('#searchModal').modal('hide');
        });
        Fire.$on('updatePurchaseList', item =>{
            this.purchaseForm.items.push(item);
            this.calculateCost();
            this.calculateTotal();
            $('#itemModal').modal('hide');
        });
    }
}
</script>