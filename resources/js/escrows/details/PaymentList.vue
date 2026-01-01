<template>
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog modal">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Payment Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowDetailPayment :payment.sync="payment"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleStandardModal" tabindex="-1" role="dialog" aria-labelledby="exampleStandardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleStandardModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EscrowDetailPayment :payment.sync="payment"/>
                </div>
            </div>
        </div>
    </div>
<div class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-hover table-striped table-head-fixed text-nowrap" >
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>S/N</th>
            <th>Date</th>
            <th>Transaction ID</th>
            <th colspan="2">Paid By</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody v-if="payments.length != 0">
            <tr v-for="(payment,index) in payments">
                <td>
                    <button class="nav-link btn btn-sm btn-tool mt-1" data-toggle="dropdown" type="button">
                        <i class="fa fa-bars text-dark"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewPayment(payment)"><i class="fa fa-eye mr-1 text-primary"></i> View Payment</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="disputePayment(payment)"><i class="fa fa-warning mr-1 text-danger"></i> Cancel payment</button>
                    </div>
                </td>
                <td class="text-dark">{{ addOne(index) }}</td>
                <td class="text-dark">{{ ExcelDate(payment.date) }}</td>
                <!--td class="text-dark">{{ payment.transaction.title }}</td-->
                <td class="text-dark">{{ payment.transaction_id }}</td>
                <td class="text-dark" v-if="source == 'admin'">{{ (payment.transaction != null && payment.transaction.seller != null ? FullName(payment.transaction.seller) : 'Old User') }}</td>
                <td class="text-dark" v-if="source == 'admin'">{{ (payment.transaction != null && payment.transaction.seller != null ? payment.transaction.seller.email : 'Old User') }}</td>
                <td class="text-dark" v-if="source == 'main' ">
                    {{ payment.buyer_id == owner_id ? 'Buyer' : 'Seller' }}
                </td>
                <td class="text-dark" v-if="source == 'payments'">
                    {{ payment.transaction.buyer_id == owner_id ? 
                    (payment.transaction.seller != null ? FullName(payment.transaction.seller) : 'Old User') : 
                    (payment.transaction.buyer != null ? FullName(payment.transaction.buyer) : 'Old User') }}
                </td>
                <td class="text-dark">{{ currency(payment.amount) }}</td>
                <td class="text-dark" v-if="source == 'payments'">{{ payment.status == 10 ? 'Paid' : 'Unknown Status'}}</td>
            </tr>
        </tbody>
        <tbody v-else style="height: 550px;">
            <tr>
                <td :colspan="source == 'admin' ? 9 : 8" class="text-center">No payment Found <br />
                    <button v-if="source != 'admin'" class="btn btn-sm btn-primary" type="button" @click="addTransaction()">Start a new payment</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            owner_id: 1,
            payment: {},
            style: 'grid',
        }
    },
    emits:['refreshPage'],
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.payment = {};
            $('#transactionModal').modal('show');
            this.loading = false; 
        },
        closeModal(){
            $('#acceptFormModal').modal('hide');            
            $('#paymentModal').modal('hide');
            $('#transactionFormModal').modal('hide');
        },
        confirmTransaction(payment){
            this.loading = true;
            this.payment = payment;
            $('#acceptFormModal').modal('show');
            this.loading = false;
        },
        deactivateTransaction(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This payment will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/escrows/payments/'+id)
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
        makePayment(payment){
            this.loading = true;
            this.payment = payment;

            $('#paymentFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
            this.$emit('refreshPage');
        },
        startTransaction(payment){
            this.loading = true;
            this.editMode = false;
            this.payment = payment;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        switchStyle(text){this.style = text;},
        viewPayment(payment){
            this.loading = true;
            this.payment = payment;
            $('#exampleStandardModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {},
    props:{
        payments: Array,
        source: String,
        user_id: Number,
    },
    watch:{}
}
</script>