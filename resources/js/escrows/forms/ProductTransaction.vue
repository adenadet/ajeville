<template>
    <section class="container overlay-wrapper">
        <div class="modal fade" id="paymentFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Make Payment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <EscrowFormQuickPayment :transaction.sync="transaction" @transactionPaid="completeTransaction" @transactionFailed="cancelTransaction()"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="card p-3" v-if="step == 1">
                        <h3 class="my-3">{{ product?.description || 'Trail Product' }}</h3>
                        <p>{{ company?.name || vendor.first_name+' '+vendor.last_name }}</p>
                        <hr>    
                        <div v-html="product.details"></div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input v-model="quickTransactionData.quantity" type="text" class="form-control" id="quantity" name="quantity" placeholder="Quantity">
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <div v-html="currency(quickTransactionData.price)" step="0.01" class="form-control"></div>
                        </div>
                        <button class="btn btn-success" type="button" @click="validateData('product')" :disabled="quickTransactionData.quantity < 1">Continue</button>
                    </div>
                    <div class="card" v-if="step == 2">
                        <div class="card-header">
                            <h3 class="card-title">{{ product?.description || 'Trail Product' }}</h3>
                            <div class="card-tools">
                                <button class="btn btn-danger btn-sm" type="button" @click="returnPage('product')">Back</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>First Name</label>
                                <input v-model="quickTransactionData.first_name" type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name">
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input v-model="quickTransactionData.last_name" type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Email</label>
                                <input v-model="quickTransactionData.email" type="email" class="form-control" id="email" placeholder="Enter Email Address">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Phone Number</label>
                                <input v-model="quickTransactionData.phone" type="number" class="form-control" id="phone" placeholder="Enter Phone Number">
                            </div>
                            <button class="btn btn-success col-md-12" type="button" @click="validateData('buyer')">Continue</button>
                        </div>
                    </div>
                    <div class="card" v-if="step == 3">
                        <div class="card-header">
                            <h3 class="card-title">Confirm Transaction</h3>
                            <div class="card-tools">
                                <button class="btn btn-danger btn-sm" type="button" @click="returnPage('buyer')">Back</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <div v-html="quickTransactionData.first_name" class="form-control"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <div v-html="quickTransactionData.last_name" class="form-control"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <div v-html="quickTransactionData.email" class="form-control"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <div v-html="quickTransactionData.phone" class="form-control"></div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-8">
                                    Product: <h3>{{ product.description }}</h3>
                                </div>
                                <div class="col-md-4">
                                    Quantity: x {{ quickTransactionData.quantity }} <br />
                                    Price: {{ currency(product.unit_price) }}
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <button class="btn btn-success col-md-12" type="button" @click="validateData('confirm')">Make Payment of {{ currency(this.quickTransactionData.price * this.quickTransactionData.quantity) }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card text-center" v-if="step == 4">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Success Icon" class="img-fluid" style="width: 80px; height: 80px;">
                            <h5 class="card-title mt-3">Payment Successful</h5>
                            <p class="card-text">Your payment has been processed successfully.</p>
                        </div>
                    </div>    
                </form>
            </div>
            <div class="col-12 col-md-6" >
                <h3 class="d-inline-block d-sm-none">{{ product.name }}</h3>
                <span></span>
                <div class="col-12">
                    <img :src="product.images.length != 0 ? '/img/products/'+product.images[0].source : '/img/products/default.png'" class="product-image" alt="Product Image">
                </div>
                <div class="col-12 product-image-thumbs" v-if="product.images.length != 1">
                    <div v-for="image in product.images" class="product-image-thumb"><img :src="'/img/products/'+image.source" alt="Product Image"></div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            company: {},
            loading: false,
            product: {images: [],},
            quickTransactionData: new Form({
                id: '',
                product_id: '',
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                amount: 0.00,
                seller: {},
                buyer: {},
                quantity: 1,
            }),
            step: 1,
            transaction: {},
            vendor: {},
        }
    },
    emits:['reloadProducts'],
    mounted(){
        this.getAllInitials();
    },
    methods:{
        closeModal() {
            $('#paymentFormModal').modal('hide');
            //this.$emit('transactionFailed', this.transaction);
        },
        cancelTransaction(){
            this.form.delete('/api/payments/transactions/'+this.transaction.id)
            .then(response => {})
            .catch(() => {});
        },
        completeTransaction(){
            $('#paymentFormModal').modal('hide');
            this.transaction = {};
            this.stage = 4;
        },
        createTransaction(){
            this.loading = true;
            if (this.transaction != null && this.transaction.id != null && this.transaction.id != ''){  
                this.quickTransactionData.post('/api/payments/transactions')
                .then(response =>{
                    this.transaction = response.data.transaction;
                })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                });
            }
            $('#paymentFormModal').modal('show');
            this.loading = false;
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/payments/quick_payments/product/'+this.$route.params.product_id)
            .then(response => {
                this.company = response.data.product.owner.company;
                this.product = response.data.product;
                this.vendor = response.data.product.owner;
                this.quickTransactionData.price         = response.data.product.unit_price;
                this.quickTransactionData.product_id    = response.data.product.id; 
                this.quickTransactionData.seller        = response.data.product.owner;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        returnPage(game){
            if(game == 'buyer'){
                this.step = 2
            }
            else if(game == 'product'){
                this.step = 1
            }
        },
        validateData(game){
            if (game == 'product'){
                this.quickTransactionData.amount = this.quickTransactionData.price * this.quickTransactionData.quantity;
                this.step = 2;
            }
            else if (game == 'buyer'){
                if (this.quickTransactionData.first_name.length == 0){
                    this.$swal.fire({
                        icon: 'error',
                        title: 'The First Name field is empty',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    return;
                }
                if (this.quickTransactionData.last_name.length == 0){
                    this.$swal.fire({
                        icon: 'error',
                        title: 'The Last Name field is empty',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                if (!(this.validateEmail(this.quickTransactionData.email))){
                    this.$swal.fire({
                        icon: 'error',
                        title: 'The email address is invalid',
                        footer: "An email is something like you@company.net",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                if (this.quickTransactionData.last_name.length == 0){
                    this.$swal.fire({
                        icon: 'error',
                        title: 'The Last Name field is empty',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                this.step = 3;
            }
            else if (game == 'confirm'){
                this.loading = true;
                this.createTransaction();
            }
        },
        validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }
    },
}
</script>