<template>
<section>
    <form class="overlay-wrapper" role="form" @submit.prevent="editMode ? (item_type == 'product' ? updateProduct() : updateTransaction())  :(item_type == 'product' ? createProduct() : createTransaction())">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card">
            <div class="card-header bg-dark"><h3 class="card-title">Product Details</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" v-model="transactionProductData.product.description" name="description" id="description" required/>
                            <input type="hidden" name="id" id="id" v-model="transactionProductData.product.id" />
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" id="role" v-model="transactionProductData.product.role" required>
                                <option value="">--Select Role--</option>
                                <option value="buyer">Buyer</option>
                                <option value="seller">Seller</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" v-model="transactionProductData.product.category_id" name="category_id" id="category_id" @change="updateCategory" required>
                                <option value="">--Category--</option>
                                <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" class="form-control" v-model="transactionProductData.product.unit_price" name="unit_price" id="unit_price" required/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="text" class="form-control" v-model="transactionProductData.product.quantity" name="quantity" id="quantity" required />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Description</label>
                            <QuillEditor theme="snow" v-model:content="transactionProductData.product.details" content-type="html" placeholder="Put a Detailed description of the product to ensure that there will be no issue determining the product" name="details" id="details"></QuillEditor>
                        </div>
                    </div>
                </div>        
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-success"><h3 class="card-title">Partner Detail</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Partner</label>
                            <select class="form-control" v-model="transactionProductData.partner_type">
                                <option value="">--Select Partner Type--</option>
                                <option value="existing">I have his unique code</option>
                                <option value="new">I don't have his unique code</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="transactionProductData.partner_type == 'existing'">
                        <div class="form-group">
                            <label>Partner Code</label>
                            <input class="form-control" type="text" name="code" id="code" v-model="transactionProductData.partner.unique_id" @change="getPartner()">
                        </div>
                    </div>
                </div>
                <div class="row" v-if="transactionProductData.partner_type == 'new'">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Partner Name</label>
                            <input type="text" class="form-control" v-model="transactionProductData.partner.name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Partner Email</label>
                            <input type="email" class="form-control" name="partner_email" id="partner_email"v-model="transactionProductData.partner.email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Partner Number</label>
                            <input type="number" class="form-control" name="partner_phone" id="partner_phone"v-model="transactionProductData.partner.phone">
                        </div>
                    </div>
                </div>
                
                <div class="row overlay-wrapper" v-else-if="transactionProductData.partner_type == 'existing' && partner != null">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Partner Name</label>
                            <div type="text" class="form-control disabled" v-html="partner.name != null ? partner.name : FullName(partner)"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Partner Email</label>
                            <div class="form-control disabled" v-html="partner.email"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Partner Number</label>
                            <div class="form-control disabled" v-html="partner.phone"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Transaction Details</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Inspection Period (max. of {{ category.max_hold_period }} days) </label>
                            <input class="form-control" type="number" name="inspection_period" id="inspection_period" v-model="transactionProductData.inspection_period" :max="category.max_hold_period" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Total Price</label>
                            <div class="form-control" v-html="transactionProductData.product.unit_price * transactionProductData.product.quantity" disabled></div>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="category.requires_delivery">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Shipping method</label>
                            <select class="form-control" name="taxes" id="taxes" v-model="transactionProductData.shipping_method">
                                <option value=""></option>
                                <option value="cargo">Cargo Shipping</option>
                                <option value="none">No Shipping</option> 
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Shipping fee paid by:</label>
                            <select class="form-control" name="taxes" id="taxes"  v-model="transactionProductData.shipping_payment">
                                <option value=""></option>
                                <option value="buyer">Buyer</option>
                                <option value="seller">Seller</option> 
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <QuillEditor v-model:content="transactionProductData.details" name="details" id="details" contentType="html"></QuillEditor>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4"><input type="submit" name="submit" class="submit btn btn-success" value="Submit" /></div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            categories: [],
            category: {},
            loading: false,
            partner: {},
            transactionProductData: new Form({
                broker:{
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    unique_id: '',
                },
                buyer:{
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    unique_id: '',
                },
                product:{
                    id: '',
                    owner_id: '',
                    item_code: '',
                    category_id: '',
                    description: '',
                    details: '',
                    detailed: '',
                    quantity: '',
                    role: '',
                    status: '',
                    unit_price: '',
                },
                partner:{
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    unique_id: '',
                },
                seller:{
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    unique_id: '',
                },
                transaction:{
                    amount: '',
                    broker_id: '',
                    buyer_id: '',
                    completed_by: '',
                    confirmation_code: '',
                    date: '',
                    id: '',
                    invoice_id: '',
                    item_details: '',
                    details: '',
                    inspection_period: '',
                    price: 0,
                    seller_id: '',
                    status: '',
                    title: '',
                    unique_code: '',
                }
            }),
        }
    },
    emits:['reloadProducts'],
    mounted(){
        this.getAllInitials();
    },
    methods:{
        createProduct(){
            this.loading = true
            this.transactionProductData.post('/api/escrows/products')
            .then(response =>{
                this.loading = false
                this.$emit('reloadProducts');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Transaction "'+ this.transaction.unique_code+'" has been '+(this.acceptanceData.action == 'accept' ? 'accepted' : 'rejected'),
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        },
        createTransaction(){
            this.loading = true
            this.transactionProductData.post('/api/escrows/transactions')
            .then(response =>{
                this.loading = false
                this.$emit('refreshPage', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Transaction has been created and sent to your Partner for confirmation',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/escrows/transactions/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        getPartner(){
            this.loading = true;
            axios.get('/api/escrows/partners/'+this.transactionProductData.partner.unique_id+'?type=unique_id&detailed=0')
            .then(response => {
                this.partner = response.data.partner;
                this.transactionProductData.partner.id = response.data.partner.id;
                this.transactionProductData.partner.name = response.data.partner.first_name != null ? response.data.partner.first_name+' '+response.data.partner.last_name: response.data.partner.name;
                this.transactionProductData.partner.email = response.data.partner.email;
                this.transactionProductData.partner.phone = response.data.partner.phone;
                this.transactionProductData.partner.unique_id = response.data.partner.unique_id;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Does not exist',})
                this.loading = false;
            });
        },
        updateCategory(){
            this.category = this.categories.find(obj => obj.id === this.transactionProductData.product.category_id);
            console.log(this.category)
        },
        updateProduct(){
            this.loading = true
            this.transactionProductData.put('/api/escrows/products/'+this.product.id)
            .then(response =>{
                this.loading = false
                this.$emit('reloadProducts');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Product : '+ this.product.item_code+' has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        updateTransaction(){
            this.loading = true
            this.transactionProductData.put('/api/escrows/transactions/'+this.transactionProductData.id)
            .then(response =>{
                this.loading = false
                this.$emit('refreshPage', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Transaction has been updated and sent to your Partner for confirmation',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        },
    },
    props:{
        editMode: Boolean,
        product: Object,
        item_type: String,
        transaction: Object,
    },
    watch:{
        item_type(){
            if (this.item_type == 'product'){
                this.transactionProductData.product.fill(this.product);
            }
            else if (this.item_type == 'purchase'){
                this.transactionProductData.product.fill(this.product);
            }
            else if (this.item_type == 'purchase'){
                if (this.transaction.id != null){
                    this.transactionProductData.transaction.amount = this.transaction.amount;
                    this.transactionProductData.category_id = this.transaction.category_id;
                    this.transactionProductData.completed_by = this.transaction.completed_by;
                    this.transactionProductData.confirmation_code = this.transaction.confirmation_code;
                    this.transactionProductData.created_by = this.transaction.created_by;
                    this.transactionProductData.date = this.transaction.date;
                    this.transactionProductData.id = this.transaction.id;
                    this.transactionProductData.invoice_id = this.transaction.invoice_id;
                    this.transactionProductData.item_details = this.transaction.item_details;
                    this.transactionProductData.details = this.transaction.details;
                    this.transactionProductData.inspection_period = this.transaction.inspection_period;
                    if (this.user.id == this.transaction.seller.id){
                        if(this.transaction.buyer != null){
                            this.transactionProductData.partner.id = this.transaction.buyer.unique_id;
                            this.transactionProductData.partner.name = this.transaction.buyer.name != null ? this.transaction.buyer.name : this.FullName(this.transaction.buyer)  ;
                            this.transactionProductData.partner.email = this.transaction.buyer.email;
                            this.transactionProductData.partner.phone = this.transaction.buyer.phone;
                            this.transactionProductData.partner.type = 'existing'
                        }
                        else{
                            this.transactionProductData.partner.id = '';
                            this.transactionProductData.partner.name = '';
                            this.transactionProductData.partner_email = '';
                            this.transactionProductData.partner_phone = '';
                            this.transactionProductData.partner_type = 'new'
                        }
                    }
                    else if(this.user.id == this.transaction.buyer.id){
                        if(this.transaction.seller != null){
                            this.transactionProductData.partner.id = this.transaction.seller.unique_id;
                            this.transactionProductData.partner.name = this.transaction.seller.name != null ? this.transaction.seller.name : this.FullName(this.transaction.seller)  ;
                            this.transactionProductData.partner.email = this.transaction.seller.email;
                            this.transactionProductData.partner.phone = this.transaction.seller.phone;
                            this.transactionProductData.partner.type = 'existing'
                        }
                        else{
                            this.transactionProductData.partner_id = '';
                            this.transactionProductData.partner_name = '';
                            this.transactionProductData.partner_email = '';
                            this.transactionProductData.partner_phone = '';
                            this.transactionProductData.partner_type = 'new'
                        }
                    }
                    //this.transactionProductData.product_id = this.transaction.updated_by;
                    //this.transactionProductData.request_id = this.transaction.updated_by;
                    this.transactionProductData.role = (this.user.id == this.transaction.buyer.id) ? 'buyer' : 'seller'
                    this.transactionProductData.seller_id = this.transaction.seller_id;
                    this.transactionProductData.status = this.transaction.status;
                    this.transactionProductData.title = this.transaction.title;
                    this.transactionProductData.unique_code = this.transaction.unique_code;
                    this.transactionProductData.updated_by = this.transaction.updated_by;
                }
            }
        },
    }
}
</script>