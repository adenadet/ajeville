<template>
<div class="container-fluid">
    <div class="modal fade" id="approvalFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">Approve Sales Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ApprovalFormSalesOrder :order="order" @approvalOrderReload="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deliveryNoteFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">Create Delivery Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesFormDeliveryNote :order="order" @deliveryReload="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentFormModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">Payment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!--FinanceFormPayment :order.sync="order" :customer="order.customer" trans_type="sales" @paymentReload="getInitials" /-->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="orderFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">Update Sales Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesFormOrder :order_id="order.unique_id" @orderFormReload="getInitials" :editMode="true" />
                </div>
            </div>
        </div>
    </div>
    <div class="row overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="col-12 p-0">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Sales Invoice</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-white mt-2"></i></button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button class="dropdown-item btn btn-block btn-sm" @click="downloadPdf()"><i class="fa fa-file-pdf mr-1 text-dark"></i> Download PDF</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="updateOrder()" v-if="order.status <= 1"><i class="fa fa-edit mr-1 text-warning"></i> Update Order</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="makePayment()" v-show="order.status <= 1"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="resendInvoice()" v-if="order.customer_id != 0 && order.customer != null && order.customer.email != null"><i class="fa fa-envelope mr-1 text-purple"></i> Resend Invoice</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="createDeliveryNote()" v-if="order.status >= 2 && order.status < 10"><i class="fa fa-truck mr-1 text-info"></i> Create Delivery Note</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="markCompleted(order.id)" v-if="order.status >= 2 && order.status < 10"><i class="fa fa-check-double mr-1 text-success"></i> Mark As Completed</button>
                            <router-link class="dropdown-item btn btn-block btn-sm" @click="fulfillOrder()" v-if="order.status != 1" :to="'/sales_orders/orders/fulfill/'+$route.params.id" target="_blank"><i class="fa fa-shopping-cart mr-1 text-success"></i> Fulfill Order</router-link>
                            <button class="dropdown-item btn btn-block btn-sm" @click="cancelOrder()" v-if="order.status <= 1"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Order</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <SalesDetailOrder :order.sync="order" view="sales" @salesOrderReload="getInitials" />        
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
import useInvoiceTools from '@/globalMethods/useInvoiceTools';
import { create } from 'lodash';
import {ref} from 'vue';

import ApprovalFormSalesOrder from '@/approvals/forms/SalesOrder.vue';
import InventoryFormFulfill from '@/inventory/forms/Fulfill.vue';
import SalesDetailOrder from '@/sales_orders/details/Order.vue';
import SalesFormDeliveryNote from '@/sales_orders/forms/DeliveryNote.vue';
import SalesFormFulfillOrderItem from '@/sales_orders/forms/FulfillOrderItem.vue'
import SalesFormOrder from '@/sales_orders/forms/Order.vue';

export default {
    components: {
        ApprovalFormSalesOrder, InventoryFormFulfill, SalesDetailOrder, SalesFormOrder, SalesFormDeliveryNote, SalesFormFulfillOrderItem
    },
    computed:{
        sub_total(){
            if (!this.order.order_items?.length) return 0;
            return this.order.order_items.reduce(
                (sum, it) => sum + it.unit_price * it.quantity * it.package_quantity,
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
            $('#orderFormModal').modal('show');
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