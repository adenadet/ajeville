<template>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="paymentFormModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Payment Form</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <FinanceFormPayment :editMode.sync="editMode" :payment.sync="payment" @refreshPaymentForm="getInitials" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="paymentViewModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Payment Modal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <FinanceDetailPayment :payment.sync="payment" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment Mode</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="payments.length != 0">
                    <tr v-for="(payment, index) in payments" :key="payment.id" :class="payment.status == 0 ? 'text-danger' : ''">
                        <td>{{ payment.date }}</td>
                        <td>{{ payment.mode != null ? payment.mode.name : 'Unverified'}}</td>
                        <td>{{ payment.customer != null ? payment.customer.name : 'Walk In Customer' }}</td>
                        <td>{{ currency(payment.amount) }}</td>
                        <td>{{ payment.account != null ? (payment.account.bank != null ? payment.account.bank.bank_name : 'Deactivated Bank') +' ['+payment.account.account_number+']'  : 'Cash'  }}</td>
                        <td>{{ payment.status == 0 ? 'Unconfirmed' : 'Confirmed' }}</td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button class="btn btn-block dropdown-item" @click="viewPayment(payment)"><i class="fas fa-eye mr-2"></i> View Payment</button>
                                <button class="btn btn-block dropdown-item" @click="updatePayment(payment)"><i class="fas fa-edit mr-2 text-primary"></i> Edit Payment</button>
                                <button class="btn btn-block dropdown-item" v-if="payment.status == 0" @click="confirmPayment(payment)"><i class="fas fa-check text-success mr-2"></i> Confirm Payment</button>
                                <button class="btn btn-block dropdown-item" v-if="payment.status == 0" @click="voidPayment(payment.id)"><i class="fas fa-trash text-danger mr-2"></i> Void Payment</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="8">No Payments Created</td>
                    </tr>
                </tbody>
            </table>    
        </div>
    </section>
</template>
<script>
import FinanceDetailPayment from '@/finance/details/Payment.vue';
import FinanceFormPayment from '@/finance/forms/Payment.vue';
export default {
    components:{FinanceFormPayment, FinanceDetailPayment},
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            payment: {},
        }
    },
    emits:['refreshPaymentList'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        confirmPayment(payment){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This payment would be confirmed and the customer's balance will be increased",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.get('/api/finance/payments/'+payment.id+'/confirm')
                    .then(response=>{
                        this.$emit('refreshPaymentList');
                        this.$swal.fire('Confirmed!', 'Payment has been confirmed', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false; 
                }
            });
        },
        closeModal(){
            $('#paymentFormModal').modal('hide');
            $('#paymentViewModal').modal('hide');
        },
        getInitials() {
            this.closeModal();
            this.$emit('refreshPaymentList');
        },
        makePayment(transaction){
            this.loading = true;
            this.editMode = false;
            var transactions = []; 
            var trans = {id: transaction.id, amount:transaction.item_total};
            transactions.push(trans);
            $('#paymentModal').modal('show');
            this.loading = false;
        },
        refreshPage(response) {
            this.transactions = response.data.transactions;
        },
        updatePayment(payment){
            this.loading = true;
            this.editMode = true;
            this.payment = payment;
            $('#paymentFormModal').modal('show');
            this.loading = false;
        },
        viaWallet(transaction){
            Swal.fire({
                title: 'Are you sure?',
                text: "The patient's wallet would be debited for this transaction",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.form.transaction_id = transaction.id;
                    this.form.post('/api/finance/payments')
                    .then(response=>{
                        Swal.fire('Update!', response.data.message, response.data.icon);
                        //this.getInitials();  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewPayment(payment){
            this.loading = true;
            this.payment = payment;
            $('#paymentViewModal').modal('show');
            this.loading = false;
        },
        voidPayment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This payment would be deleted and payment reversed",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/payments/'+id)
                    .then(response=>{
                        this.$emit('refreshPaymentList');
                        this.$swal.fire('Deleted!', 'This payment has been deleted', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false; 
                    
                }
            });
        },
        
    },
    props:{
        payments: Array,
        source: String,
    }
}
</script>