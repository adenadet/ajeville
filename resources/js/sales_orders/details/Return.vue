<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="invoice p-3 mb-3" ref="invoiceRef" style="border: none;">
        <div class="row">
            <div class="col-12">
                <h4>
                    <img :src="'/img/clients/smart_distributors_nigeria_limited.png'" alt="Smart Distributors Nigeria Limited" class="img-fluid"/>
                    <small class="float-right">
                        <span class="text-right"><strong>Date: {{ return_order.date != null ? ExcelDate(return_order.date) : '1970-01-01' }}</strong></span><br />
                        <span class="text-right"><strong>RC: 1435092</strong></span><br />
                        <span class="text-right"><strong>TIN: 20541261-0001</strong></span>
                    </small>
                </h4>  
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                To:
                <address>
                    <strong>Smart Distributors Nigeria Limited</strong><br>
                    22, Providence Street,<br /> Lekki Phase I,<br>
                    Lekki, Lagos.<br>
                    Phone: +234-802-735-3553<br>
                    Email: sales@smartdistributors.net <br />
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                From:
                <address v-if="return_order.customer != null">
                    <strong>{{ return_order.customer.name }}</strong><br>
                    Address: <span v-html="return_order.customer.address"></span><br>
                    Phone: {{ return_order.customer.phone }}<br>
                    Email: {{ return_order.customer.email }}
                </address>
                <address v-else>
                    <strong>Walk In Customer</strong><br>
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <h3>Return</h3>
                <b>Return ID:</b> {{ return_order.unique_id }}<br>
                <b>Payment Due:</b> {{ ExcelDate(return_order.date) }}<br>
                <b>Created By:</b> {{ FullName(return_order.creator) }}
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                            <th>Reason</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="return_order.return_items.length > 0">
                        <tr v-for="return_item in return_order.return_items">
                            <td>{{ return_item.item_name }}</td>
                            <td>{{ return_item.quantity }}</td>
                            <td>{{ return_item.unit_price }}</td>
                            <td>{{ return_item.discount }}</td>
                            <td>{{ currency((return_item.unit_price * return_item.quantity) - return_item.discount) }}</td>
                            <td>{{ return_item.reason }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <p class="lead">Description:</p>
                <div class="text-muted well well-sm shadow-none" style="margin-top: 10px;" v-html="return_order.description"></div>
            </div>
            <div class="col-6">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="width:50%">Subtotal:</td>
                                <td>{{currency(sub_total)}}</td>
                            </tr>
                            <tr>
                                <td>Tax (7.5%)</td>
                                <td>{{ currency(sub_total * 0.075) }}</td>
                            </tr>
                            <tr>
                                <td>Logistics:</td>
                                <td>{{ currency(return_order.logistics) }}</td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td>{{ currency((sub_total * 1.075) + return_order.logistics) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row no-print">
        <div class="col-12">
            <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;" @click="downloadPdf">
                <i class="fas fa-download"></i> Generate PDF
            </button>
        </div>
    </div>
</section>
</template>
<script>
import useInvoiceTools from '@/globalMethods/useInvoiceTools';
import { create } from 'lodash';
import {ref} from 'vue';

export default {
    computed:{
        sub_total(){
            if (!this.return_order.return_items?.length) return 0;
            return this.return_order.return_items.reduce(
                (sum, it) => sum + ((it.unit_price * it.quantity) - it.discount),
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
            order: {
                unique_id: '',
                order_items: [],
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
        addOrder(){
            $('#orderModal').modal('show');
        },
        approveOrder(){
            $('#approvalFormModal').modal('show');
        },
        closeModals() {
            $('#approvalFormModal').modal('hide');
            $('#deliveryNoteFormModal').modal('hide');
            $('#orderFormModal').modal('hide');
            $('#paymentFormModal').modal('hide');
        },
        createDeliveryNote(){
            $('#deliveryNoteFormModal').modal('show');
        },
        makePayment(){
            $('#paymentFormModal').modal('show');
        },
        getInitials() {
            this.loading = true 
            this.closeModals();
            axios.get('/api/sales/orders/'+ this.$route.params.id)
            .then(response => {
                this.refreshPage(response);
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Sales Order loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Sales Order was not loaded successfully',
                })
            });
        },
        mailOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.get('/api/sales_order/orders/mail/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        markCompleted(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, completed it!'
            })
            .then((result) => {
                if (result.value) {
                    this.form.get('/api/sales/orders/'+id+'/complete')
                    .then(response => {
                        this.getInitials();
                        this.$swal.fire('Completed!', 'Order has been completed.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        refreshPage(response) {
            this.order = response.data.order;
            this.closeModals();
        },
        updateOrder(){
            this.loading = true;
            this.editMode = true;
            $('#orderFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        return_order: Object,
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