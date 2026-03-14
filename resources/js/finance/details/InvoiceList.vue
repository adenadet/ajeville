<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="approveInvoiceModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Approve Invoices</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="editMode = false"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <ApprovalFormInvoice :invoice.sync="invoice" @approvalInvoiceReload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="invoiceDetailModal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Invoice Detail</h4>
                        <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body table-responsive">
                        <FinanceDetailInvoice :invoice.sync="invoice" :editMode.sync="editMode" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="invoiceFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h3 class="modal-title">Invoice Form</h3>
                        <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body table-responsive">
                        <FinanceFormInvoice :invoice.sync="invoice" :editMode.sync="editMode" @refreshInvoiceForm="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-head-fixed text-nowrap table-striped table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Classification</th>
                    <th>Due Date</th>
                    <th>Unique ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="invoices.length > 0">
                <tr v-for="invoice in invoices" :key="invoice.id">
                    <td>{{ ExcelDate(invoice.date)}}</td>
                    <td>{{ invoice.vendor != null ? invoice.vendor.name : 'Not yet assigned' }}</td>
                    <td>{{ invoice.classification != null ? invoice.classification.name : 'Not yet assigned' }}</td>
                    <td>{{ ExcelDate(invoice.due_date) }}</td>
                    <td>
                        <router-link v-if="invoice.expense != null" :to="'/finance/expenses/'+invoice.expense.unique_id">{{invoice.expense.unique_id}}</router-link>
                        <span v-else>{{ invoice.unique_id }}</span>
                    </td>
                    <td>{{ currency(invoice.amount) }}</td>
                    <td>
                        <span v-if="invoice.status == 1" class="badge badge-warning">Pending Confirmation</span>
                        <span v-else-if="invoice.status == 2" class="badge badge-primary">Confirmed</span>
                        <span v-else-if="invoice.status == 5" class="badge badge-dark">Ongoing</span>
                        <span v-else-if="invoice.status == 10" class="badge badge-success">Completed</span>
                        <span v-else class="badge badge-danger">Queried</span> 
                    </td>
                    <td>
                        <button class="nav-link btn btn-sm btn-tool mt-1" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v text-dark"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'approval'">
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewInvoice(invoice)"><i class="fa fa-eye mr-1"></i> View Invoice</button>
                            <button v-if="invoice.status == 1" class="dropdown-item btn btn-block btn-sm" @click="confirmInvoice(invoice)"><i class="fa fa-check mr-1 text-info"></i> Approve Invoice</button>
                         </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else>
                            <router-link class="dropdown-item btn btn-block btn-sm" :to="'/finance/invoices/'+invoice.id"><i class="fa fa-eye mr-1"></i> View Invoice</router-link>
                            <button class="dropdown-item btn btn-block btn-sm" @click="viewInvoice(invoice)"><i class="fa fa-eye mr-1"></i> View Invoice</button>
                            <button v-if="invoice.status >= 2 && invoice.expense == null" class="dropdown-item btn btn-block btn-sm" @click="createExpense(invoice)"><i class="fa fa-money-bill mr-1 text-purple"></i> Create Expense</button>
                            <button v-if="invoice.status >= 2" class="dropdown-item btn btn-block btn-sm" @click="createQuery(invoice)"><i class="fa fa-exclamation-circle mr-1 text-warning"></i> Create Dispute</button>
                            <button v-if="invoice.status < 10 && source == 'approval'" class="dropdown-item btn btn-block btn-sm" @click="confirmInvoice(invoice)"><i class="fa fa-handshake mr-1 text-info"></i> Confirm Invoice</button>
                            <button v-if="invoice.status < 2" class="dropdown-item btn btn-block btn-sm" @click="updateInvoice(invoice)"><i class="fa fa-edit mr-1 text-success"></i> Update Invoice</button>
                            <button v-if="invoice.status < 2" class="dropdown-item btn btn-block btn-sm" @click="cancelInvoice(invoice)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Invoice</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="7" class="text-center">No Invoices Found</td>
                </tr>
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
            invoice: {},
        }
    },
    emits: ['refreshInvoiceList'],
    mounted() {},
    methods: {
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#invoiceDetailModal').modal('hide');
            $('#invoiceFormModal').modal('hide');
        },
        confirmInvoice(invoice){
            this.invoice = invoice;
            $('#approveInvoiceModal').modal('show');
        },
        createExpense(invoice){
            this.loading = true;
            axios.get('/api/finance/invoices/'+invoice.id+'/expense')
            .then(response => {
                this.refreshPage(response);
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Finance Expense created successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Finance Expense was not created successfully',
                })
            });
            this.loading = false;
        },
        makePayment(appointment){
            this.paySpecific = true;
            $('#paymentModal').modal('show');
        },
        refreshPage() {
            this.loading = true;
            this.closeModals();
            this.$emit('refreshInvoiceList');
            this.loading = false;
        },
        updateInvoice(invoice){
            this.loading = true;
            this.invoice = invoice;
            this.editMode = true;
            $('#invoiceFormModal').modal('show');
            this.loading = false; 
        },
        viewInvoice(invoice){
            this.loading = true;
            this.invoice = invoice;
            $('#invoiceDetailModal').modal('show');
            this.loading = false;
        },
    },
    props: {
        invoices: Array,
        source: String,
    }
}
</script>