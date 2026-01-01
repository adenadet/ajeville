<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateTransaction() : createTransaction()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Title</label>
                    <input class="form-control" type="text" name="title" id="title" v-model="transactionData.product.title">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Item Category</label>
                    <select class="form-control" v-model="transactionData.product.category_id" required @change="updateCategory">
                        <option value="">--Select Item Type--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>My Role</label>
                    <select class="form-control" name="role" id="role" v-model="transactionData.product.role">
                        <option value="">--Select Role--</option>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Inspection Period (max. of {{ category.max_hold_period }} days) </label>
                    <input class="form-control" type="number" name="inspection_period" id="inspection_period" v-model="transactionData.product.inspection_period" :max="category.max_hold_period" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Item Name</label>
                    <input class="form-control" type="text" name="item_details" id="item_details" v-model="transactionData.product.item_details">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Price</label>
                    <input class="form-control" type="number" name="amount" id="amount" v-model="transactionData.product.amount">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Partner</label>
                    <select class="form-control" v-model="transactionData.partner_type">
                        <option value="">--Select Partner Type--</option>
                        <option value="existing">I have his unique code</option>
                        <option value="new">I don't have his unique code</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6" v-if="transactionData.partner_type == 'existing'">
                <div class="form-group">
                    <label>Partner Code</label>
                    <input class="form-control" type="text" name="code" id="code" v-model="transactionData.partner_unique_id" @change="getPartner()">
                </div>
            </div>
        </div>
        <div class="row" v-if="transactionData.partner_type == 'new'">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner Name</label>
                    <input type="text" class="form-control" v-model="transactionData.partner.name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner Email</label>
                    <input type="email" class="form-control" name="partner_email" id="partner_email"v-model="transactionData.partner.email">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner Number</label>
                    <input type="number" class="form-control" name="partner_phone" id="partner_phone"v-model="transactionData.partner.phone">
                </div>
            </div>
        </div>
        <div class="row overlay-wrapper" v-else-if="transactionData.partner_type == 'existing' && partner != null">
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
        <div class="row" v-if="category.requires_delivery">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Shipping method</label>
                    <select class="form-control" name="taxes" id="taxes" v-model="transactionData.shipping_method">
                        <option value=""></option>
                        <option value="cargo">Cargo Shipping</option>
                        <option value="none">No Shipping</option> 
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Shipping fee paid by:</label>
                    <select class="form-control" name="taxes" id="taxes"  v-model="transactionData.shipping_payment">
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
                    <QuillEditor v-model:content="transactionData.details" name="details" id="details" contentType="html"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" type="button" v-if="product == null" @click="editMode ? updateTransaction() : createTransaction()">Begin</button>
                <button class="btn btn-primary" type="button" v-if="product != null" @click="purchase_product">Begin</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    computed:{
        all_vendors(){
            if (this.assignVendorData.category_id != '') {
                return this.vendors.filter(vendor => vendor.category_id === this.assignVendorData.category_id);
            }
            else{ return this.vendors}
        }
    },
    data() {
        return {
            categories: [],
            category: {},
            loading: false,
            partner: {},
            transactionData: new Form({
                amount: '',
                seller_id: '',
                broker_id:'',
                buyer_id: '',
                completed_by: '',
                confirmation_code: '',
                date: '',
                details: '',
                id: '',
                inspection_period: '',
                invoice_id: '',
                item_details: '',
                item_type_id: '',
                price: 0,
                request_id: '',
                status: '',
                unique_code: '',
                created_by: '',
                updated_by: '',    
                product: {
                    category_id: '',
                    details: '',
                    detailed: '',
                    description: '',
                    id: '',
                    item_code: '',
                    owner_id: '',
                    quantity: '',
                    role: '',
                    status: '',
                    unit_price: '',
                },
                partner: {
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    unique_id: '',
                },
            }),
            vendors: [],
            work_orders: [],
            user: {},
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createTransaction(){
            this.loading = true;
            this.transactionData.amount = 
            this.transactionData.post('/api/escrows/transactions')
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
                this.user = response.data.user;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        getPartner(){
            this.loading = true;
            axios.get('/api/escrows/partners/'+this.transactionData.partner_unique_id+'?type=unique_id&detailed=0')
            .then(response => {
                this.partner = response.data.partner;
                this.transactionData.partner_id = response.data.partner.id;
                this.transactionData.partner_name = response.data.partner.first_name != null ? response.data.partner.first_name+' '+response.data.partner.last_name: response.data.partner.name;
                this.transactionData.partner_email = response.data.partner.email;
                this.transactionData.partner_phone = response.data.partner.phone;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Does not exist',})
                this.
                this.loading = false;
            });
        },
        purchase_product(){
            this.loading = true;
            this.transactionData.post('/api/escrows/transactions')
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
        updateCategory(){
            console.log(this.transactionData.product.category_id);
            this.category = this.categories.find(obj => obj.id === this.transactionData.product.category_id);
            console.log(this.category);
        },
        updateTransaction(){
            this.loading = true
            this.transactionData.put('/api/escrows/transactions/'+this.transactionData.id)
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
    props: {
        editMode: Boolean,
        product: Object,
        transaction: Object,
    },
    watch:{
        product(){
            if (this.product != null && this.product.id != null){
                this.transactionData.category_id = this.product.category_id;
                this.transactionData.item_details = this.product.description;
                this.transactionData.amount = this.product.unit_price;
                this.transactionData.title = 'Direct Purchase of '+this.product.description;
                //this.transactionData.partner_id = this.product.owner.unique_id;
                this.transactionData.product_id = this.product.id;
                this.transactionData.role = this.product.role;
                this.partner = this.product.owner;
                this.transactionData.partner_type = 'existing'
            }``
        },
        transaction(){
            if (this.transaction.id != null){
                this.transactionData.amount = this.transaction.amount;
                this.transactionData.category_id = this.transaction.category_id;
                this.transactionData.completed_by = this.transaction.completed_by;
                this.transactionData.confirmation_code = this.transaction.confirmation_code;
                this.transactionData.created_by = this.transaction.created_by;
                this.transactionData.date = this.transaction.date;
                this.transactionData.id = this.transaction.id;
                this.transactionData.invoice_id = this.transaction.invoice_id;
                this.transactionData.item_details = this.transaction.item_details;
                this.transactionData.details = this.transaction.details;
                this.transactionData.inspection_period = this.transaction.inspection_period;
                if (this.user.id == this.transaction.seller.id){
                    if(this.transaction.buyer != null){
                        this.transactionData.partner_id = this.transaction.buyer.unique_id;
                        this.transactionData.partner_name = this.transaction.buyer.name != null ? this.transaction.buyer.name : this.FullName(this.transaction.buyer)  ;
                        this.transactionData.partner_email = this.transaction.buyer.email;
                        this.transactionData.partner_phone = this.transaction.buyer.phone;
                        this.transactionData.partner_type = 'existing'
                    }
                    else{
                        this.transactionData.partner_id = '';
                        this.transactionData.partner_name = '';
                        this.transactionData.partner_email = '';
                        this.transactionData.partner_phone = '';
                        this.transactionData.partner_type = 'new'
                    }
                }
                else if(this.user.id == this.transaction.buyer.id){
                    if(this.transaction.seller != null){
                        this.transactionData.partner_id = this.transaction.seller.unique_id;
                        this.transactionData.partner_name = this.transaction.seller.name != null ? this.transaction.seller.name : this.FullName(this.transaction.seller)  ;
                        this.transactionData.partner_email = this.transaction.seller.email;
                        this.transactionData.partner_phone = this.transaction.seller.phone;
                        this.transactionData.partner_type = 'existing'
                    }
                    else{
                        this.transactionData.partner_id = '';
                        this.transactionData.partner_name = '';
                        this.transactionData.partner_email = '';
                        this.transactionData.partner_phone = '';
                        this.transactionData.partner_type = 'new'
                    }
                }
                //this.transactionData.product_id = this.transaction.updated_by;
                //this.transactionData.request_id = this.transaction.updated_by;
                this.transactionData.role = (this.user.id == this.transaction.buyer.id) ? 'buyer' : 'seller'
                this.transactionData.seller_id = this.transaction.seller_id;
                this.transactionData.status = this.transaction.status;
                this.transactionData.title = this.transaction.title;
                this.transactionData.unique_code = this.transaction.unique_code;
                this.transactionData.updated_by = this.transaction.updated_by;
            }
        },
    }
}
</script>