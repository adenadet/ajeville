<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="paymentFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Price List Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormTransaction :editMode.sync="editMode" :transaction.sync="transaction" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped table-hover text-nowrap">
        <thead>
            <tr>
                <th>Date</th>
                <th>Unique ID</th>
                <th>Partner</th>
                <th>Classification</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Payment Due Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="transactions.length > 0">
            <tr v-for="transaction in transactions">
                <td>{{ ExcelDate(transaction.date) }}</td>
                <td>{{ transaction.unique_id }}</td>
                <td>
                    <span v-if="transaction.customer_id != null">{{ transaction.customer != null ? transaction.customer.name : 'Walk In Customer' }}</span>
                    <span v-else-if="transaction.reference_type == 'purchase_order'">{{ transaction.vendor != null ? transaction.vendor.name : 'Open Market' }}</span>
                </td>
                <td>
                    {{ transaction.classification != null ? transaction.classification.name : 'N/A' }}
                </td>
                <td>{{ transaction.trans_type == 'debit' ? currency(transaction.amount) : 0.00 }}</td>
                <td>{{ transaction.trans_type == 'credit' ?currency(transaction.amount) : 0.00}}</td>
                <td>{{ ExcelDate(transaction.payment_due_date) }}</td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="btn btn-block dropdown-item" :to="'/finance/transactions/'+transaction.unique_id"><i class="fas fa-eye mr-1"></i> View Transaction</router-link>
                        <button class="btn btn-block dropdown-item" @click="editPricelist(transaction)"><i class="fas fa-edit mr-1 text-primary"></i> Update Transaction</button>
                        <button class="btn btn-block dropdown-item" v-if="transaction.status == 0" @click="deactivateTransaction(transaction)"><i class="fas fa-recycle mr-1 text-success"></i> Reactivate Transaction</button>
                        <button class="btn btn-block dropdown-item" v-if="transaction.status == 1" @click="deactivateTransaction(transaction)"><i class="fas fa-trash mr-1 text-danger"></i> Deactivate Transaction</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan=9>No Transaction meets your criteria</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            transaction: {},
        }
    },
    mounted() {},
    methods: {
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        getInitials(page=1) {
            axios.get('/api/finance/transactions?page='+page).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        makePayment(appointment){
            this.$Progress.start();
            this.paySpecific = true;
            Fire.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshPage(response) {
            this.transactions = response.data.transactions;
        }
    },
    props: {
        transactions: Array,
    }
}
</script>