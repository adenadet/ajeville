<template>
<div class="modal fade" id="acceptFormModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h4 class="modal-title">Accept Transaction</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true" class="text-white">&times;</span></button>
            </div>
            <div class="modal-body">
                <EscrowFormAccept :transaction.sync="transaction" @reload="refreshPage"/>
            </div>
        </div>
    </div>
</div>
<!--div class="modal fade" id="paymentFormModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h4 class="modal-title">Make Payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true" class="text-white">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6"><EscrowDetailTransactionSummary :transaction.sync="transaction"/></div>
                    <div class="col-6 p-0"><EscrowFormPayment :transaction_id.sync="transaction.unique_code" @reload="refreshPage"/></div>
                </div>
            </div>
        </div>
    </div>
</div-->
<div class="modal fade" id="transactionFormModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h4 class="modal-title">{{editMode ? 'Update' : 'Start'}} Transaction</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true" class="text-white">&times;</span></button>
            </div>
            <div class="modal-body">
                <EscrowFormTransaction :editMode="editMode" :product.sync="transaction.product" :transaction.sync="transaction" @refreshPage="refreshPage"/>
            </div>
        </div>
    </div>
</div>
<section class="overlay-wrapper">
    <div class="overlay" v-if="loading">
        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
    </div>
    <table class="table table-hover table-striped table-head-fixed text-nowrap">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>S/N</th>
            <th>Date</th>
            <th>Title</th>
            <th>Unique ID</th>
            <th v-if="source == 'admin'">Buyer</th>
            <th v-if="source == 'admin'">Seller</th>
            <th v-if="source == 'main'">My Role</th>
            <th v-if="source == 'main'">Partner</th>
            <th v-if="source == 'payments'">Partner</th>
            <th>Amount</th>
            <th>Status</th>
            <th v-if="source != 'payments'">Inspection Period</th>
        </tr>
        </thead>
        <tbody v-if="transactions.length != 0">
            <tr v-for="(transaction,index) in transactions">
                <td>
                    <button  class="text-dark nav-link btn btn-sm btn-tool p-1" data-toggle="dropdown" type="button">
                        <i class="fa fa-bars text-dark mt-1"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link v-if="source != 'payments'" :to="'/escrows/transactions/'+transaction.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Transaction</button></router-link>
                        <!--router-link v-if="source != 'payments'" :to="'/escrows/payments/'+transaction.unique_id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Transaction</button></router-link-->
                        <router-link v-if="source == 'admin'" :to="'/escrows/partners/'+transaction.buyer_id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-user mr-1"></i> View Buyer</button></router-link>
                        <router-link v-if="source == 'admin'" :to="'/escrows/partners/'+transaction.seller_id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-user mr-1"></i> View Seller</router-link>
                        <router-link v-if="source == 'main' || source == 'payments'" :to="'/escrows/partners/'+(transaction.buyer_id == owner_id ? transaction.seller_id : transaction.buyer_id)" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-user mr-1"></i> View Partner</router-link>
                        <button v-if="transaction.status >= 4000 && transaction.status <=4999" class="dropdown-item btn btn-block btn-sm" @click="createDispute(transaction)"><i class="fa fa-exclamation-circle mr-1 text-warning"></i> Create Dispute</button>
                        <button v-if="transaction.status >= 4000 && transaction.status <=4999" class="dropdown-item btn btn-block btn-sm" @click="completeTransaction(transaction)"><i class="fa fa-battery-full mr-1 text-info"></i> Complete Transaction</button>
                        <button v-if="transaction.status >= 1000 && transaction.status <=1110" class="dropdown-item btn btn-block btn-sm" @click="confirmTransaction(transaction)"><i class="fa fa-handshake mr-1 text-info"></i> Agree to Contract</button>
                        <button v-if="(transaction.status >= 1111 && transaction.status <= 1999) && (transaction.buyer_id == owner_id)" class="dropdown-item btn btn-block btn-sm" @click="makePayment(transaction)"><i class="fa fa-hand-holding-usd mr-1 text-warning"></i> Make Payment</button>
                        <button v-if="transaction.status <= 1111" class="dropdown-item btn btn-block btn-sm" @click="updateTransaction(transaction)"><i class="fa fa-edit mr-1 text-success"></i> Update Transaction</button>
                        <button v-if="transaction.status <= 1999" class="dropdown-item btn btn-block btn-sm" @click="deactivateTransaction(transaction)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Transaction</button>
                    </div>
                </td>
                <td class="text-dark">{{ addOne(index) }}</td>
                <td class="text-dark">{{ ExcelDate(transaction.date) }}</td>
                <td class="text-dark">{{ transaction.title }}</td>
                <td class="text-dark">{{ transaction.unique_code }}</td>
                <td class="text-dark" v-if="source == 'admin'">{{ (transaction.buyer != null ? (transaction.buyer.name != null ? transaction.buyer.name : FullName(transaction.buyer)) : 'Old User') }}</td>
                <td class="text-dark" v-if="source == 'admin'">{{(transaction.seller != null ? (transaction.seller.name != null ? transaction.seller.name : FullName(transaction.seller)) : 'Old User')}}</td>
                <td class="text-dark" v-if="source == 'main' ">
                    {{ transaction.buyer_id == owner_id ? 'Buyer' : 'Seller' }}
                </td>
                <td class="text-dark" v-if="source == 'main' || source == 'payments'">
                    {{ transaction.buyer_id == owner_id ? 
                    (transaction.seller != null ? (transaction.seller.name != null ? transaction.seller.name : FullName(transaction.seller)) : 'Old User') : 
                    (transaction.buyer != null ? (transaction.buyer.name != null ? transaction.buyer.name : FullName(transaction.buyer)) : 'Old User')  }}
                </td>
                <td class="text-dark">{{ currency(transaction.amount) }}</td>
                <td class="text-dark" v-if="source != 'payments'">{{ transaction.status >= 9000 && transaction.status <= 9999 ? 'Rejected' : 
                    (transaction.status >= 1000 && transaction.status <= 1110 ? 'Pending Partner Confirmation' : 
                    (transaction.status >= 1111 && transaction.status <= 1999 ? 'Pending Payment' : 
                    (transaction.status >= 2000 && transaction.status <= 3000 ? 'Ongoing' : 
                    (transaction.status >= 4000 && transaction.status <= 4999 ? 'Completed (Inspection Period)' : 
                    (transaction.status >= 5000 && transaction.status <= 5999 ? 'Completed' :
                    (transaction.status >= 6000 && transaction.status <= 6999 ? 'Disputed' : 
                    (transaction.status >= 7000 && transaction.status <= 8999 ? 'Disputed Resolved' : 
                    (transaction.status >= 9000 && transaction.status <= 9999 ? 'Rejected' : 
                    'Unknown Status'))))))))}}</td>
                <td class="text-dark" v-if="source == 'payments'">{{ transaction.status >= 9000 && transaction.status <= 9999 ? 'Refunded' : 
                    (transaction.status >= 1000 && transaction.status <= 1999 ? 'Awaiting Payment' : 
                    (transaction.status >= 2000 && transaction.status <= 9000 ? 'Paid' : 'Unknown Status'))}}</td>
                <td v-if="source != 'payments'" >{{ transaction.inspection_period }} days</td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td :colspan="source == 'admin' ? 11 : 10" class="text-center">No Transaction Found <br />
                <button v-if="source != 'admin'" class="btn btn-sm btn-primary" type="button" @click="addTransaction()">Start a new Transaction</button>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            owner_id: 1,
            transaction: {},
            style: 'grid',
        }
    },
    emits:['refreshPage'],
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false; 
        },
        closeModal(){
            $('#acceptFormModal').modal('hide');            
            $('#paymentFormModal').modal('hide');
            $('#transactionFormModal').modal('hide');
        },
        confirmTransaction(transaction){
            this.loading = true;
            this.transaction = transaction;
            $('#acceptFormModal').modal('show');
            this.loading = false;
        },
        deactivateTransaction(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This transaction will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/escrows/transactions/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        makePayment(transaction){
            this.loading = true;
            this.transaction = transaction;

            $('#paymentFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
            this.$emit('refreshPage');
        },
        startTransaction(transaction){
            this.loading = true;
            this.editMode = false;
            this.transaction = transaction;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        switchStyle(text){this.style = text;},
        updateTransaction(transaction){
            this.loading = true;
            this.editMode = true;
            this.transaction = transaction;
            $('#transactionFormModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        transactions: Array,
        source: String,
        user_id: Number,
    },
    watch:{
        transactions(){
            this.loading = true;
            this.loading = false;
        },
        user_id(){
            if (this.user_id != null){
                this.owner_id = this.user_id;
            }
        }
    }
}
</script>