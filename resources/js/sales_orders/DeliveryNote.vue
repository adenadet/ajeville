<template>
<section class="col-md-12">
    <div class="card">
        <div class="card-header bg-dark no-print">
            <h3 class="card-title">Delivery Note</h3>
            <div class="card-tools">
                <button class="btn btn-xs btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-white mt-2"></i></button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <button class="dropdown-item btn btn-block btn-sm" @click="downloadPdf()"><i class="fa fa-file-pdf mr-1 text-dark"></i> Download PDF</button>
                            
                    <!--button class="dropdown-item btn btn-block btn-sm" @click="updateOrder()" v-if="order.status <= 1"><i class="fa fa-edit mr-1 text-warning"></i> Update Order</button>
                    <button class="dropdown-item btn btn-block btn-sm" @click="makePayment()" v-show="order.status <= 1"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                    <button class="dropdown-item btn btn-block btn-sm" @click="resendInvoice()" v-if="order.customer_id != 0 && order.customer != null && order.customer.email != null"><i class="fa fa-envelope mr-1 text-purple"></i> Resend Invoice</button>
                    <button class="dropdown-item btn btn-block btn-sm" @click="createDeliveryNote()" v-if="order.status >= 2 && order.status < 10"><i class="fa fa-truck mr-1 text-info"></i> Create Delivery Note</button>
                    <button class="dropdown-item btn btn-block btn-sm" @click="markCompleted()" v-if="order.status >= 2 && order.status < 10"><i class="fa fa-check-double mr-1 text-success"></i> Mark As Completed</button>
                    <router-link class="dropdown-item btn btn-block btn-sm" @click="fulfillOrder()" v-if="order.status != 1" :to="'/sales_orders/orders/fulfill/'+$route.params.id" target="_blank"><i class="fa fa-shopping-cart mr-1 text-success"></i> Fulfill Order</router-link>
                    <button class="dropdown-item btn btn-block btn-sm" @click="cancelOrder()" v-if="order.status <= 1"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Order</button-->
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="invoice p-3 mb-3" ref="invoiceRef">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-globe"></i> Smart Distributors Limited
                            <small class="float-right">{{ ExcelDate(delivery_note.created_at) }}</small>
                        </h4>
                    </div>
                </div>
                <!-- info row -->
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                    From
                        <address>
                            <strong>Smart Distributors Nigeria Limited</strong><br>
                            Lekki Phase I,<br>
                            Lekki, Lagos.<br>
                            Phone: +234-803-123-5432<br>
                            Email: sales@smartdistributors.net <br />
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                    To
                        <address v-if="delivery_note.order != null && delivery_note.order.customer">
                            <strong>{{ delivery_note.order.customer.name}}</strong><br>
                            <span v-html="delivery_note.order.customer.address"></span><br />
                            Phone: {{ delivery_note.order.customer.phone}}<br>
                            Email: {{ delivery_note.order.customer.email}}
                        </address>
                        <address v-else>
                            Walk In Customer 
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col" v-if="delivery_note.order != null">
                        <b>Delivery Note #{{ delivery_note.uuid }}</b><br><br>
                        <b>Order ID:</b> {{ delivery_note.order.unique_id }}<br>
                        <b>Payment Due:</b> {{ delivery_note.order.date }}<br>
                        <b>Created By:</b> {{ FullName(delivery_note.creator) }}
                    </div>
                </div>
                <!-- /.row -->

                <!-- Table row -->
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td>S/N</td>    
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(delivery_item, index) in delivery_note.delivery_items">
                                    <td>{{addOne(index)}}</td>
                                    <td>{{ delivery_item.item != null ?  delivery_item.item.name : "Old Item"}}</td>
                                    <td>{{  delivery_item.quantity}} units</td>
                                    <td><i class="far fa-square fa-2x"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <p class="lead">Received By:</p>
                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                            &nbsp;
                            <br />    
                        </p>
                        <hr />
                        <p class="text-center">Name, Date and Signature (for Customer)</p>
                    </div>
                    <div class="col-6">
                        <p class="lead">Delivered By:</p>
                        <p v-if="delivery_note.status >= 2" class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                            <span v-html="FullName(delivery_note.delivery_person)"></span>
                            {{ delivery_note.delivered_at != null ? ExcelDate(delivery_note.delivered_at)  : "" }}
                        </p>

                        <p v-else class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                            &nbsp;
                            <br />
                        </p>
                        <hr />
                        <p class="text-center">Name, Date and Signature (for Smart Distributors Limited)</p>
                    </div>
                </div>              
            </div>
        </div>
    </div>
</section>
</template>
<script>
import useInvoiceTools from '@/globalMethods/useInvoiceTools';
import {ref} from 'vue';
import SalesDetailDeliveryNote from '@/sales_orders/details/DeliveryNote.vue';
import SalesFormDeliveryNote from '@/sales_orders/forms/DeliveryNote.vue';
export default {
    components: {
        SalesDetailDeliveryNote, SalesFormDeliveryNote 
    },
    data(){
        return {
            customers: {total: 0,},
            delivery_note: {},
            loading: false,
        }
    },
    methods:{
        closeModals(){},
        getInitials() {
            this.loading = true 
            this.closeModals();
            axios.get('/api/sales/delivery_notes/'+ this.$route.params.id)
            .then(response => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Delivery Note loaded successfully',
                });
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Delivery Note was not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response){
            this.delivery_note = response.data.delivery_note
        },
    },
    mounted() {
        this.getInitials();
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