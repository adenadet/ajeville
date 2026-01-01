<template>
<section class="overlay-wrapper">
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Transaction Details</h3>
            <!--div class="card-tools">
                <button type="button" class="btn btn-tool"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool"><i class="fas fa-times"></i></button>
            </div-->
        </div>
        <div class="card-body" style="height: 600px;">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1" v-if="transaction != null">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h3 class="card-title">{{className(transaction.transactionable_type)}}</h3>
                        </div>
                        <div class="card-body" v-if="className(transaction.transactionable_type) == 'Income'">
                            <strong><i class="fas fa-calendar mr-1"></i> ID:</strong>
                            <a :href="`/finance/incomes/${transaction.transactionable.unique_id}`"><p>{{ transaction.transactionable.unique_id }}</p></a>
                            <hr>
                            <strong><i class="fas fa-calendar mr-1"></i> Date | Due Date</strong>
                            <p class="text-muted">{{ transaction.transactionable.date }} | {{ transaction.transactionable.due_date }}</p>
                            <hr>
                            <strong><i class="fas fa-money-bill mr-1"></i> Total Amount</strong>
                            <p class="text-muted">{{ transaction.transactionable.amount != null ? currency(transaction.transactionable.amount) : '0.00' }}</p>
                            <hr>
                            <strong><i class="fas fa-calendar-check mr-1"></i> Status</strong><br />
                            <span v-if="transaction.transactionable.status==1" class="badge badge-warning">Unconfirmed</span>
                            <span v-else-if="transaction.transactionable.status==5" class="badge bg-orange">Queried</span>
                            <span v-else-if="transaction.transactionable.status==10" class="badge badge-info">Confirmed</span>
                            <span v-else-if="transaction.transactionable.status==40" class="badge badge-danger">Rejected</span>
                            <span v-else-if="transaction.transactionable.status==100" class="badge badge-danger">Deleted</span>
                            <span v-else-if="transaction.transactionable.status==10" class="badge bg-success">Paid</span>
                            <span v-else-if="transaction.transactionable.status==300" class="badge bg-warning">Part Paid</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 order-1 order-md-1 table-responsive overflow-none">
                    <FinanceDetailTransaction :transaction.sync="transaction" />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            counterparty: {},
            loading: false,
            transaction: { reference_type: null, transactionable: {},}
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page=1) {
            axios.get('/api/finance/transactions/'+this.$route.params.id+'?page='+page).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transaction did not loaded successfully',
                })
            });
        },
        makePayment(appointment){
            this.paySpecific = true;
            $('#paymentModal').modal('show');
        },
        refreshPage(response) {
            this.transaction = response.data.transaction;
        }
    },
    /*
    props: {
        transaction: Object,
    },
    watch:{
        transaction(){
            this.loading = true;
            this.counterparty = this.transaction.trans_type == 'debit' ? this.transaction.customer : this.transaction.vendor;

            this.loading = true;
        }
    }*/
}
</script>