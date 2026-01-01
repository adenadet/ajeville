<template>
<div class="container-fluid">
    <div class="modal fade" id="approvalFormModal" tabindex="-1" role="dialog" aria-labelledby="quotationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="quotationModalLabel">Approve Quotation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ApprovalFormSalesQuotation @approvalOrderReload="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="quotationFormModal" tabindex="-1" role="dialog" aria-labelledby="quotationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="quotationModalLabel">Update Quotation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesFormQuotation :quotation_id.sync="quotation.uuid" @quotationFormReload="getInitials" :editMode="true" />
                </div>
            </div>
        </div>
    </div>
    <div class="row overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="col-12 p-0">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Quotation Details</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-white mt-2"></i></button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button class="dropdown-item btn btn-block btn-sm" @click="downloadPdf()"><i class="fa fa-file-pdf mr-1 text-dark"></i> Download PDF</button>
                            <button v-if="quotation.customer_id == 0" class="dropdown-item btn btn-block btn-sm" @click="addCustomer(quotation.uuid)"><i class="fa fa-user-plus mr-1 text-primary"></i> Assign To Customer</button>
                            <button v-if="quotation.status == 'draft'" class="dropdown-item btn btn-block btn-sm" @click="updateQuote(quotation.uuid)"><i class="fa fa-edit mr-1 text-warning"></i> Update Order</button>
                            <button v-if="quotation.status == 'sent'" class="dropdown-item btn btn-block btn-sm" @click="agreeQuote(quotation)"><i class="fa fa-credit-card mr-1 text-success"></i> Confirm Quote</button>
                            <button v-if="quotation.status != 'cancelled'" class="dropdown-item btn btn-block btn-sm" @click="resendQuote(quotation.id)"><i class="fa fa-envelope mr-1 text-purple"></i> Send Quote</button>
                            <button v-if="quotation.status != 'agreed' && quotation.status != 'cancelled'" class="dropdown-item btn btn-block btn-sm" @click="deleteQuote(quotation.unique_id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Quote</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">        
                    <div class="invoice p-3 mb-3" ref="invoiceRef">
                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fas fa-globe"></i> Smart Distributors Nigeria Limited.
                                    <small class="float-right">Date: {{ quotation.quote_date != null ? ExcelDate(quotation.quote_date) : ExcelDate('1970-01-01') }}</small>
                                </h4>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                From
                                <address>
                                    <strong>Smart Distributors Nigeria Limited</strong><br>
                                    Lekki Phase I,<br>
                                    Lekki, Lagos.<br>
                                    Phone: +234-803-123-5432<br>
                                    Email: sales@smartdistributors.net
                                </address>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                To
                                <address v-if="quotation.customer != null">
                                    <strong>{{ quotation.customer.name }}</strong><br>
                                    Address: <span v-html="quotation.customer.address"></span><br>
                                    Phone: {{ quotation.customer.phone }}<br>
                                    Email: {{ quotation.customer.email }}
                                </address>
                                <address v-else>
                                    <strong>Walk In Customer</strong><br>
                                </address>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                <b>Invoice #</b><br>
                                <br>
                                <b>Quotation ID:</b> {{ quotation.uuid }}<br>
                                <b>Account:</b> 968-34567<br />
                                <b>Created By:</b> {{ quotation.created_by != null ? FullName(quotation.creator) : 'N/A' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Qty</th>
                                            <th>Product</th>
                                            <th>Description</th>
                                            <th>Unit Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="quotation.quotation_items.length > 0">
                                        <tr v-for="(quotation_item, index) in quotation.quotation_items" :key="index">
                                            <td>{{quotation_item.quantity}}</td>
                                            <td>{{quotation_item.item_name != null ? quotation_item.item_name : quotation_item.item.name}} </td>
                                            <td>{{ quotation_item.package.name }} of {{ quotation_item.package_quantity }}</td>
                                            <td>{{ currency(quotation_item.unit_price) }}</td>
                                            <td>{{ currency(quotation_item.quantity *  quotation_item.unit_price)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-6">
                                To confirm send a mail to 'sales@smartdistributors.net', state the quotation id as well as any other details you require.
                                <!--p class="lead">Payment Methods:</p>
                                <p v-if="quotation.payment_terms != null" class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                    {{ quotation.payment_terms.name }}<br>{{ quotation.payment_terms.description }}
                                </p-->
                            </div>
                            <div class="col-6">
                                <p class="lead">Amount Due 2/22/2014</p>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td style="width:50%">Subtotal:</td>
                                                <td>{{currency(sub_total)}}</td>
                                            </tr>
                                            <tr>
                                                <td>Tax (7.5%)</td>
                                                <td>{{currency(sub_total * 0.075)}}</td>
                                            </tr>
                                            <tr>
                                                <td>Logistics:</td>
                                                <td>{{ currency(quotation.logistics) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Discount:</td>
                                                <td>{{ currency(quotation.discount) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total:</td>
                                                <td>{{ currency( (sub_total * 1.075) + quotation.logistics - quotation.discount) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
import useInvoiceTools from '@/globalMethods/useInvoiceTools';
import {ref} from 'vue';
export default {
    computed:{
        sub_total(){
            if (!this.quotation.quotation_items?.length) return 0;
            return this.quotation.quotation_items.reduce(
                (sum, it) => sum + it.unit_price * it.quantity,
                0
            );
        },
    },
    data() {
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            form: new Form({}),
            quotation: {
                quotation_items: [],
            },
            query: null,
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addUpdate(){
            $('#quotationModal').modal('show');
        },
        approveUpdate(){
            $('#approvalFormModal').modal('show');
        },
        closeModals() {
            $('#approvalFormModal').modal('hide');
            $('#quotationFormModal').modal('hide');
        },
        getInitials(page = 1) {
            this.loading = true 
            axios.get('/api/sales/quotations/'+ this.$route.params.id)
                .then(response => {
                    this.refreshPage(response);
                    this.loading = false; 
                    this.$toast.fire({
                        icon: 'success',
                        title: 'Quotation was loaded successfully',
                    });
                })
                .catch(() => {
                    this.loading = false;
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Quotation was not loaded successfully',
                    })
                });
        },
        refreshPage(response) {
            this.quotation = response.data.quotation;
            this.closeModals();
        },
        updateQuote(){
            this.loading = true;
            $('#quotationFormModal').modal('show');
            this.loading = false;
        }
    },
    setup () {
        const invoiceRef = ref(null);
        const { downloadPdf, printInvoice } = useInvoiceTools(invoiceRef);
        return { invoiceRef, downloadPdf, printInvoice };
    },
}
</script>
<style scoped>
@media print {
    body *        { visibility: hidden !important; }
    #invoice, #invoice *    { visibility: visible !important; }
    #invoice      { position: absolute; top: 0; left: 0; width: 100%; }
    .no-print     { visibility: hidden !important; }
}
</style>