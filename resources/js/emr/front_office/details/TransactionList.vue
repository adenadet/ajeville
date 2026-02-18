<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="serviceModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Services</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRFrontOfficeFormPatientService :patient.sync="patient" :visit_id.sync="visit?.id" @refreshPatientServiceForm="closeModal"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="transactionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transaction Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRFinanceDetailTransaction :transaction_id.sync="transaction.id"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Payment</h4>
                    <h4 class="modal-title" v-show="!editMode">New Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRFinanceFormDeposit :editMode="editMode" :transactions.sync="trans" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Transactions</h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" title="New Transactions" @click="addService"><i class="fa fa-plus"></i></button>
                    <router-link :to="source == 'visit' ? '/hims/visits/bills/'+this.$route.params.id : '/hims/patients/bills/'+this.$route.params.id" class="btn btn-sm btn-default" title="Print Transactions"><i class="fa fa-print"></i></router-link>
                    <button class="btn btn-sm btn-warning" title="Export Transactions"><i class="fa fa-file-pdf"></i></button>
                    <button class="btn btn-sm btn-info" title=" Make Payment" @click="makePayment"><i class="fas fa-cash-register mr-2"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover text-nowrap">
                <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Service Name</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Completion Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="!(loading) && transactions != null && transactions.data.length != 0">
                    <tr v-for="(transaction, index) in transactions.data" :key="transaction.id" :class="transaction.status == 0 ? 'text-danger' : ''">
                        <td>{{ addOne(index)  }}</td>
                        <td>{{ ExcelDate(transaction.date) }}</td>
                        <td>{{ transaction.service_type?.name}}</td>
                        <td>{{ transaction.item_name }}</td>
                        <td>{{ currency(transaction.item_total) }}</td>
                        <td>
                            <span v-if="transaction.status == 400" class="badge badge-danger">Cancelled</span>
                            <span v-else-if="transaction.status == 100" class="badge badge-success">Paid</span>
                            <span v-else-if="transaction.status == 1" class="badge badge-dark">Unpaid</span>
                        </td>
                        <td>
                            <span v-if="transaction.status == 400" class="badge badge-danger">Cancelled</span>
                            <span v-else-if="transaction.status == 1000" class="badge badge-info">Transferred</span>
                            <span v-else-if="transaction.service_status == 1" class="badge badge-success">Done</span>
                            <span v-else class="badge badge-warning">Pending</span>
                        </td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button class="btn btn-block dropdown-item" @click="viewTransaction(transaction)"><i class="fas fa-eye mr-2"></i> View Transaction</button>
                                <button class="btn btn-block dropdown-item" v-if="transaction.status == 1 && (transaction.paid_by == 1 || transaction.paid_by == 3)" @click="viaWallet(transaction)"><i class="fas fa-wallet mr-2"></i> Pay via Wallet</button>
                                <button class="btn btn-block dropdown-item" v-if="transaction.service_status == 0" @click="cancelTransaction(transaction)"><i class="fas fa-times mr-2"></i> Cancel</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else-if="loading">
                    <tr>
                        <td colspan="8">
                            <div class="card">
                                <div class="overlay-wrapper">
                                    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="8">No Transaction Created</td>
                    </tr>
                </tbody>
            </table>    
        </div>
    </div>
</section>
</template>
<script>
export default {
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data() {
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            source: 'visit',
            trans: [],
            transaction: {},
        };
    },
    emits:['refreshTransactionList'],
    methods: {
        addService(){
            $('#serviceModal').modal('show'); 
        },
        cancelTransaction(transaction){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This transaction would be deleted and payment reversed",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/emr/hims/visit_transactions/'+transaction.id)
                    .then(response=>{
                        this.$emit('refreshTransactionList');
                        this.$swal.fire('Deleted!', 'Successfully Cancelled', 'success');
                        this.loading = false; 
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        closeModal(){
            this.$emit('refreshTransactionList');
            $('#paymentModal').modal('hide');  
            $('#serviceModal').modal('hide');  
            $('#transactionModal').modal('hide');  
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/finance/transactions/patients/'+this.patient.id+'/all?page='+this.current_page).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your Transactions did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        makePayment(){
            this.editMode = false;
            $('#paymentModal').modal('show');
        },
        refreshPage(response) {
            this.transactions = response.data.transactions;
        },
        viaWallet(transaction){
            var force = 0;
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The patient's wallet would be debited for this transaction",
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "Pay Wallet",
                denyButtonText: "Force Debit",
                confirmButtonColor: '#3035d6',
                cancelButtonColor: '#d33',
                denyButtonColor: '#0D0',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if((result.isConfirmed) || (result.isDenied)){
                    force = result.isDenied ? 1 : 0;
                    this.form.get('/api/emr/finance/transactions/'+transaction.id+'/payment?forced='+force)
                    .then(response=>{
                        this.$swal.fire('Paid', 'Transaction paid', 'success');
                        this.$emit('refreshTransactionList');
                    })
                    .catch(error => {
                        let message = 'Payment failed.';
                        if (error.response && error.response.data) {
                            message = error.response.data.transaction || error.response.data.message || message;
                        }
                        this.$swal.fire({ icon: 'error', title: 'Payment Failed', text: message});
                    });
                }
            });
        },
        viewTransaction(transaction){
            this.loading = true;
            this.transaction = transaction;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
    },
    props:{
        transactions: Object,
    },
    watch: {
        
    },
    
};
</script>