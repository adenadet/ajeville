<template>
<section>
    <div class="modal fade" id="invoiceModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h3 class="modal-title">Invoice Form</h3>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormInvoice :invoice.sync="invoice" :editMode.sync="editMode" @refreshInvoiceForm="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Invoices</h3>
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
                        <button class="btn btn-sm btn-primary ml-1" type="button" @click="addInvoice"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-sm btn-success ml-1" type="button" @click="downloadInvoices"><i class="fa fa-download"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0 overlay-wrapper table-responsive" style="height: 500px;">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><br /><div class="text-bold pt-2">Loading...</div></div>
            <FinanceDetailInvoiceList :invoices="invoices.data" @refreshInvoiceList="getInitials"/>
        </div>
        <div class="card-footer">
            <div class="col-12">
                <pagination v-model="current_page" @paginate="getInitials" :per-page="invoices.per_page != null ? invoices.per_page : 52" :records="invoices.total != null ? invoices.total : 550" ></pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import FinanceDetailInvoiceList from '@/finance/details/InvoiceList.vue';
import FinanceFormInvoice from '@/finance/forms/Invoice.vue';
export default {
    components:{
        FinanceDetailInvoiceList, FinanceFormInvoice
    },
    data() {
        return {
            active_visits: 0,
            current_page: 1,
            editMode: false,
            invoice: {},
            invoices: {data: [], total: 0,},
            loading: false,
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
        closeModals(){
            $('#invoiceModal').modal('hide');
        },
        getInitials() {
            this.loading = true;
            this.closeModals();
            axios.get('/api/finance/invoices?page='+this.current_page+'&query='+this.query+'&status='+this.status).then(response => {
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
            this.invoices = response.data.invoices;
        }
    },
    props: {}
}
</script>