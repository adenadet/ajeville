<template>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="payOutFormModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Pay Out Form</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <FinanceFormPayOut :editMode.sync="editMode" :pay_out.sync="pay_out" @refreshPayOutForm="getInitials" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="pay_outViewModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Pay Out Modal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <FinanceDetailPayOut :pay_out.sync="pay_out" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pay Out Mode</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Paying Account</th>
                        <th>Status</th>
                        <th><button type="button" class="btn btn-primary btn-xs ml-1" @click="addPayOut"><i class="fas fa-plus"></i></button></th>
                    </tr>
                </thead>
                <tbody v-if="pay_outs.length != 0">
                    <tr v-for="(pay_out, index) in pay_outs" :key="pay_out.id" :class="pay_out.status == 0 ? 'text-danger' : ''">
                        <td>{{ ExcelDate(pay_out.date) }}</td>
                        <td>{{ pay_out.mode != null ? pay_out.mode.name : 'Unverified'}}</td>
                        <td>
                            <p v-if="pay_out.customer != null">{{ pay_out.customer.name }}</p>
                            <p v-if="pay_out.staff != null">{{ pay_out.customer != null ? pay_out.customer.name : 'Walk In Customer' }}</p>
                            <p v-if="pay_out.vendor != null">{{ pay_out.vendor.name}}</p>
                        </td>
                        <td>{{ currency(pay_out.amount) }}</td>
                        <td>{{ pay_out.account != null ? (pay_out.account.bank != null ? pay_out.account.bank.bank_name : 'Deactivated Bank') +' ['+pay_out.account.account_number+']'  : 'Cash'  }}</td>
                        <td>{{ pay_out.status == 1 ? 'Unconfirmed' : (pay_out.status == 10 ? 'Confirmed' : 'Reversed') }}</td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button class="btn btn-block dropdown-item" @click="viewPayOut(pay_out)"><i class="fas fa-eye mr-2"></i> View PayOut</button>
                                <button class="btn btn-block dropdown-item" @click="updatePayOut(pay_out)"><i class="fas fa-edit mr-2 text-primary"></i> Edit PayOut</button>
                                <button class="btn btn-block dropdown-item" v-if="pay_out.status == 1" @click="confirmPayOut(pay_out)"><i class="fas fa-check text-success mr-2"></i> Confirm PayOut</button>
                                <button class="btn btn-block dropdown-item" v-if="pay_out.status == 1" @click="voidPayOut(pay_out.id)"><i class="fas fa-trash text-danger mr-2"></i> Void PayOut</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="8">No PayOuts Created</td>
                    </tr>
                </tbody>
            </table>    
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            pay_out: {},
        }
    },
    emits:['refreshPayOutList'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        addPayOut(){
            this.loading = true;
            this.editMode = false;
            this.expense = {};
            $('#payOutFormModal').modal('show');
            this.loading = false;
        },
        confirmPayOut(pay_out){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This pay_out would be confirmed and the customer's balance will be increased",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.get('/api/finance/pay_outs/'+pay_out.id+'/confirm')
                    .then(response=>{
                        this.$emit('refreshPayOutList');
                        this.$swal.fire('Confirmed!', 'PayOut has been confirmed', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false; 
                }
            });
        },
        closeModal(){
            $('#pay_outFormModal').modal('hide');
            $('#pay_outViewModal').modal('hide');
        },
        getInitials() {
            this.closeModal();
            this.$emit('refreshPayOutList');
        },
        makePayOut(transaction){
            this.loading = true;
            this.editMode = false;
            var transactions = []; 
            var trans = {id: transaction.id, amount:transaction.item_total};
            transactions.push(trans);
            $('#pay_outModal').modal('show');
            this.loading = false;
        },
        refreshPage(response) {
            this.transactions = response.data.transactions;
        },
        updatePayOut(pay_out){
            this.loading = true;
            this.editMode = true;
            this.pay_out = pay_out;
            $('#payOutFormModal').modal('show');
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
                    this.form.post('/api/finance/pay_outs')
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
        viewPayOut(pay_out){
            this.loading = true;
            this.pay_out = pay_out;
            $('#pay_outViewModal').modal('show');
            this.loading = false;
        },
        voidPayOut(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This pay_out would be deleted and pay_out reversed",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/pay_outs/'+id)
                    .then(response=>{
                        this.$emit('refreshPayOutList');
                        this.$swal.fire('Deleted!', 'This pay_out has been deleted', 'success');
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
        pay_outs: Array,
        source: String,
    }
}
</script>