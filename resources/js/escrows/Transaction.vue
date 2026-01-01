<template>
<section class="content">
    <div class="row p-0">
        <div class="col-md-4">
            <div class="card" v-if="(transaction.status == 1101 && user.id == transaction.buyer.id) || (transaction.status == 1110 && user.id == transaction.seller.id)">
                <div class="card-header">Accept Transaction</div>
                <div class="card-body"><EscrowFormAccept :transaction.sync="transaction"  :user_id="user.id" /></div>
            </div>
            <div class="card" v-else-if="(transaction.status == 1101 && user.id != transaction.buyer.id) || (transaction.status == 1110 && user.id != transaction.seller.id)">
                <div class="card-header">
                    <h3 class="card-title">Awaiting Partner Confirmation</h3>
                </div>
                <div class="card-body info-box">
                    <span class="info-box-icon"><i class="fas fa-handshake-slash"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Your Partner has not yet confirmed this request. 
                            <br />He has a few days more to look over the contract request.
                        </span>
                        <span class="info-box-number"></span>

                        <!--span class="progress-description">Send a Reminder</span -->
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-block btn-success" type="button" @click="sendReminder()">Send a Reminder </button>
                    <button class="btn btn-block btn-outline-dark" type="button" @click="cancelTransaction()">Cancel Transaction </button>
                </div>
            </div>
            <div class="card" v-else-if="(transaction.status == 1111 && user.id == transaction.buyer.id)">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-money-bill mr-1"></i>Your Agreement is Confirmed – Proceed with Payment </h3>
                </div>
                <div class="card-body">
                    <p>Great news! All parties have successfully agreed to the contract terms. </p>
                    <p>Now, it's time to make your payment to secure the transaction. Choose any of the available payment methods below to proceed.</p>
                    <p>Your funds will be held securely until the transaction is completed.</p>
                </div>
                <div class="card-footer row text-center">
                    <p class="text-right">Make payment using one of the following options:</p>
                    <button class="col-md-5 btn btn-block bg-navy" type="button">Paystack </button>
                    <button class="col-md-5 offset-md-1 btn btn-block bg-purple" type="button">Alat </button>
                    <button class="col-md-5 btn btn-block btn-outline-danger" type="button">Quickteller </button>
                    <button class="col-md-5 offset-md-1 btn btn-block btn-outline-secondary" type="button">Bank Transfer</button>
                </div>
            </div>
            <div class="card" v-else-if="(transaction.status == 1111 && user.id != transaction.buyer.id)">
                <div class="card-header">
                    <h3 class="card-title">Awaiting Payment</h3>
                </div>
                <div class="card-body info-box">
                    <span class="info-box-icon"><i class="fas fa-handshake-slash"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text text-wrap">Your Partner has not yet made a payment. <br />He has a few days more to look over the contract request.</span>
                        <span class="info-box-number"></span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-block btn-success" type="button" @click="sendPaymentReminder()">Send A Payment Reminder </button>
                    <button class="btn btn-block btn-outline-dark" type="button" @click="cancelTransaction()">Cancel Transaction </button>
                </div>
            </div>
            <div class="card" v-else-if="(transaction.status == 2000 && user.id != transaction.seller.id)">
                <div class="card-header"><h3 class="card-title">Awaiting Delivery</h3></div>
                <div class="card-body info-box">
                    <span class="info-box-icon"><i class="fas fa-handshake-slash"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text text-wrap">Your Partner has not yet made a payment. <br />He has a few days more to look over the contract request.</span>
                        <span class="info-box-number"></span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-block btn-success" type="button">Send A Payment Reminder </button>
                    <button class="btn btn-block btn-outline-dark" type="button">Cancel Transaction </button>
                </div>
            </div>
            <div class="card" v-else-if="(transaction.status == 2000 && user.id == transaction.seller.id)">
                <div class="card-header"><h3 class="card-title">Awaiting Delivery</h3></div>
                <div class="card-body"><EscrowFormDelivery :transaction.sync="transaction" /></div>
            </div>
            <div class="card" v-else-if="(transaction.status == 3000 && user.id == transaction.buyer.id)">
                <div class="card-header"><h3 class="card-title">Awaiting Delivery</h3></div>
                <div class="card-body"><EscrowFormDeliveryAccept :transaction.sync="transaction" /></div>
            </div>
            <div class="card p-0" v-else>
                <div class="card-header">
                    <h3 class="card-title">Awaiting Partner Confirmation</h3>
                </div>
                <div class="card-body info-box">
                    <span class="info-box-icon"><i class="fas fa-handshake-slash"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Your Partner has not yet confirmed this request. <br />He has a few days more to look over the contract request.</span>
                        <span class="info-box-number"></span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-block btn-success" type="button" @click="sendReminder()">Send a Reminder </button>
                    <button class="btn btn-block btn-outline-dark" type="button"  @click="cancelTransaction()">Cancel Transaction </button>
                </div>
            </div>
        </div>
        <div class="col-md-8"><EscrowDetailTransaction :transaction.sync="transaction" :user_id="user.id" /></div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            acceptanceData: new Form({
                'transaction_id': '',
                'decision': '',
                'details': '', 
            }),
            form: new Form({}),
            loading: false,
            transaction: {},
            user: {},
        }
    },
    emits:['reload'],
    methods:{
        acceptEscrow(){
            this.loading = true
            this.acceptanceData.post('/api/escrows/transactions/accept')
            .then(response =>{
                this.loading = false
                this.$emit('reload', response);
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
        cancelTransaction(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You are about to cancel this transaction",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/escrows/transactions/'+this.transaction.id+'/cancel')
                    .then(response=>{
                        this.$swal.fire(response.data.status, response.data.message, response.data.status);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/escrows/transactions/'+this.$route.params.id+'?status='+this.status+'&query='+this.query)
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
                    title: 'Items not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.transaction = response.data.transaction;
            this.user = response.data.user;
        },
        reloadData(policy){
            this.acceptanceData.policy_id   = policy.id;
            this.acceptanceData.policy_name = policy.name;
            this.acceptanceData.departments = [];
            for (let i = 0; i < policy.depts.length; i++) {this.assignData.departments.push(policy.depts[i].department.id);}    
        },
        sendPaymentReminder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Your partner will get a mail with the reminder to make payment.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send a reminder!'
                })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/escrows/transactions/remind/'+this.transaction.id)
                    .then(response=>{
                        this.$swal.fire('success', 'Your partner will get a reminder on this transaction', 'success');   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        sendReminder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Your partner will get a mail with the reminder mail",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reminder confirmation!'
                })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/escrows/transactions/remind/'+this.transaction.id)
                    .then(response=>{
                        this.$swal.fire(response.data.status, response.data.message, response.data.status);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        }
    },
    mounted(){ 
        this.getAllInitials();
    },
    props:{
        'departments': Array,
    },
}
</script>