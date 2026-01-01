<template>
<section class="card overlay-wrapper">
    <div class="overlay" v-if="loading">
        <div class="overlay-body overlay dark text-white">
            <i class="fa fa-spinner fa-spin"></i> Processing...
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 social-auth-links text-center">
            <AlatpayButton class="btn-danger btn-block btn" :apiKey="alatProd" :businessId="alatKey" :email="transaction != null && transaction.buyer != null ? transaction.buyer.email : 'adenadet01@gmail.com'" :phoneNumber="transaction != null && transaction.buyer != null ? transaction.buyer.phone : '08070402339'" :firstName="transaction != null && transaction.buyer != null ? transaction.buyer.first_name : 'Niyi'" :lastName="transaction != null && transaction.buyer != null ? transaction.buyer.last_name : 'Adetunji'" :transactionId="genRef()" :amount="transaction != null && transaction.buyer != null ? parseInt(transaction.amount * 1.03) : 5000000" :onTransaction="processPayment('paystack')" :onFailure="failedPayment('alatpay')"/>
            
            <paystack class="btn-primary btn-block btn" buttonClass="'btn-block btn btn-primary'" buttonText="Pay with Paystack" :publicKey="publicKey" :email="transaction != null && transaction.buyer != null ? transaction.buyer.email : 'adenadet01@gmail.com'" :amount="transaction != null ? parseFloat(transaction.amount) : 5000000" :reference="genRef()" :onSuccess="processPayment('paystack')" :onCanel="failedPayment('paystack')" @click="upStat(1)"></paystack>
            <p><br />- OR -<br /></p>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-success"><h3 class="card-title">Paid with Transfer</h3></div>
                <div class="card-body">
                    <p>To pay with transfer, please make a transfer to the following account:</p>
                    <ul class="list-unstyled">
                        <li><strong>Account Name:</strong> Nairafy Escrows Limited</li>
                        <li><strong>Account Number:</strong> 0126228418</li>
                        <li><strong>Bank:</strong> Wema Bank</li>
                    </ul>
                    <p>Please send a screenshot of the transfer to our support team.</p>
                    <form role="form" @submit.prevent="processPayment('transfer')">
                        <div class="input-group mb-3">
                            <input type="text" id="depositor_name" name="depositor_name" class="form-control" placeholder="Depositor's name" v-model="paymentData.transfer.depositor_name" required>
                            <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
                        </div>
                        <div class="input-group mb-3">
                            <input  v-model="paymentData.transfer.date" type="date" class="form-control" placeholder="Payment Date" id="date" name="date" required>
                            <div class="input-group-append"><div class="input-group-text"><span class="fas fa-calendar"></span></div></div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" placeholder="Proof of Payment" required @change="uploadProof">
                            <div class="input-group-append"><div class="input-group-text"><span class="fas fa-file"></span></div></div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree" v-model="paymentData.transfer.terms" required>
                                    <label class="ml-2" for="agreeTerms">I agree to the <a href="#">terms</a></label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-success btn-block">Complete Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import paystack from 'vue3-paystack';
export default {
    components: {
        paystack
    },
    computed:{
        user(){
            return this.$store.state.user;
        },
    },
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            paymentData: new Form({
                amount: null,
                channel: null,
                description: null,
                method: null,
                transfer: {
                    bank_id: 1,
                    depositor_name: '',
                    date: '',
                    proof: '',
                    proof_type: '',
                },
                transaction_id: null,
            }),
            publicKey: "pk_live_c2fded4469321ca5e78eeb29437b0e0be724daf4", 
            nairafyKey: "",
            alatKey: "f5352372-fc3d-4971-8e15-08dd8ee905c1",
            alatProd: "f230b3d136b24599a8db7c01e8afd51b",
            stats: null,
            transaction: {},
        }
    },
    emits:['refreshPage'],
    methods:{
        failedPayment(method){
            /*this.$toast.error('Payment failed. Please try again.', {
                position: 'top-right',
                duration: 5000,
                theme: 'bubble',
            });*/
        },
        genRef(){
            return "NFY_"+ new Date().valueOf();
        },
        getInitials(){
            //this.loading = true;
            axios.get('/api/escrows/transactions/'+this.transaction_id+'?type=unique_code&status='+this.status+'&query='+this.query)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Transactions loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transactions not loaded successfully',
                })
            });
        },
        processPayment(channel){
            this.loading = true;
            if (channel == 'transfer'){
                this.stats = true;
            }
            if (this.transaction == null || this.transaction.id == null || this.stats == null){return;}
            //alert('Processing payment with '+channel);
            this.paymentData.channel = channel;
            this.paymentData.transaction_id = this.transaction.id;
            this.paymentData.put('/api/escrows/transactions/payment/'+this.transaction.id).then(response =>{
                this.loading = false
                this.$emit('reload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Transaction "'+ this.transaction.unique_code+'" has been received.',
                    footer: this.paymentData.channel == 'transfer' ? "Please confirmation might take up to 24 hours" : "Please check your email for the payment receipt.",
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
        refreshPage(response){
            this.paymentData.transaction_id = response.data.transaction.id;
            this.paymentData.amount = response.data.transaction.amount;
            this.transaction = response.data.transaction;
        },
        uploadProof(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['type'] == 'image/png' || file['type'] == 'image/jpeg' || file['type'] == 'image/jpg' || file['type'] == 'image/gif' || 'application/pdf'){
                if (file['type'] == 'image/png' || file['type'] == 'image/jpeg' || file['type'] == 'image/jpg' || file['type'] == 'image/gif'){
                this.paymentData.transfer.proof_type = 'image';
                }
                else if(file['type'] == 'application/pdf'){
                    this.paymentData.transfer.proof_type = 'pdf';
                }
            }
            else{this.$swal.fire({type: 'error', title: 'File type not supported'}); return;}
            if (file['size'] < 2000000){
                console.log(file['type' ])
                reader.onloadend = (e) => {
                    this.paymentData.proof = reader.result;
                    }
                reader.readAsDataURL(file)
            }
            else{this.$swal.fire({type: 'error', title: 'File is too large'}); return }
        },
        upStat(){
            this.stats = true;
        }
    },
    mounted() {},
    props:{
        transaction_id: String,
    },
    watch:{
        transaction_id(){
            this.getInitials();
        }
    }
}
</script>