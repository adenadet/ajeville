<template>
<section class="row overlay-wrapper p-0">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Invoice</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 350px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            <select class="form-control form-control-sm ml-1" id="status" name="status" v-model="status">
                                <option value="">--Sort By Type--</option>
                                <option value="completed">Completed</option>
                                <option value="overdue">Overdue</option>
                                <option value="pending">Pending</option>
                            </select>
                            <button class="nav-link btn btn-sm btn-tool mt-1" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'./transactions/'+invoice.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Transaction</button></router-link>
                            <button v-if="invoice.status == 4" class="dropdown-item btn btn-block btn-sm" @click="createDispute(invoice)"><i class="fa fa-exclamation-circle mr-1 text-warning"></i> Create Dispute</button>
                            <button v-if="invoice.status == 1" class="dropdown-item btn btn-block btn-sm" @click="confirmTransaction(invoice)"><i class="fa fa-handshake mr-1 text-info"></i> Agree to Contract</button>
                            <button v-if="invoice.status == 2" class="dropdown-item btn btn-block btn-sm" @click="makePayment(invoice)"><i class="fa fa-hand-holding-usd mr-1 text-warning"></i> Make Payment</button>
                            <button v-if="invoice.status == 0" class="dropdown-item btn btn-block btn-sm" @click="updateTransaction(invoice)"><i class="fa fa-edit mr-1 text-success"></i> Update Transaction</button>
                            <button v-if="invoice.status <= 2" class="dropdown-item btn btn-block btn-sm" @click="deactivateTransaction(invoice)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Transaction</button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0 overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><br /><div class="text-bold pt-2">Loading...</div></div>
                <FinanceDetailInvoice :invoice="invoice" />
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            active_visits: 0,
            current_page: 1,
            editMode: false,
            invoice: {},
            invoices: {data: [], total: 0,},
            patients: [],
            query: '',
            status: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addInvoice(){
            this.editMode = false;
            this.invoice = {};
            $('#invoiceModal').modal('show');
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/finance/invoices/'+this.$route.params.id).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Invoices did not load successfully',
                })
            });
            this.loading = false;
        },
        makePayment(invoice){
            this.paySpecific = true;
            this.transaction = invoice;
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshPage(response) {
            this.invoice = response.data.invoice;
        }
    },
    props: {}
}
</script>