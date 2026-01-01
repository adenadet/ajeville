<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="fulfillOrderItemModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Fulfill Order Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <SalesFormFulfillOrderItem :order_item.sync="order_item" @approvalSalesReload="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="invoice p-3 mb-3" ref="invoiceRef">
        <div class="row">
            <div class="col-12">
                <h4>
                    <img :src="'/img/clients/smart_distributors_nigeria_limited.png'" alt="Smart Distributors Nigeria Limited" class="img-fluid"/>
                    <small class="float-right">
                        <span class="text-right"><strong>Date: {{ order.date != null ? ExcelDate(order.date) : '1970-01-01' }}</strong></span><br />
                        <span class="text-right"><strong>RC: 1435092</strong></span><br />
                        <span class="text-right"><strong>TIN: 20541261-0001</strong></span>
                    </small>
                </h4>  
            </div>
        </div>
        <div class="row invoice-info mt-3">
            <div class="col-sm-3 invoice-col">
                From
                <address>
                    <strong>Smart Distributors Nigeria Limited</strong><br>
                    22, Providence Street,<br /> Lekki Phase I,<br>
                    Lekki, Lagos.<br>
                    Phone: +234-802-735-3553<br>
                    Email: sales@smartdistributors.net <br />
                </address>
            </div>
            <div class="col-sm-3 invoice-col">
                To
                <address v-if="order.customer != null">
                    <strong>{{ order.customer.name }}</strong><br>
                    Address: <span v-html="order.customer.address"></span><br>
                    Phone: {{ order.customer.phone }}<br>
                    Email: {{ order.customer.email }}
                </address>
                <address v-else>
                    <strong>Walk In Customer</strong><br>
                </address>
            </div>
            <div class="col-sm-3 invoice-col">
                Delivery Address
                <address v-if="order.customer != null">
                    <span v-html="order.customer.delivery_address"></span><br>
                </address>
                <address v-else>
                    <strong>Walk In Customer</strong><br>
                </address>
            </div>
            <div class="col-sm-3 invoice-col">
                <b>Order ID:</b> {{ order.unique_id }}<br>
                <b>Customer LPO ID:</b> {{ order.customer_lpo }}<br />
                <b>Payment Due:</b> {{ ExcelDate(order.payment_due_date) }}<br>
                <b>Created By:</b> {{ order.created_by != null ? FullName(order.creator) : 'N/A' }}
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Qty</th>
                            <th v-if="view == 'approvals'">Req. Qty</th>
                            <th v-if="view == 'approvals'">App. Qty</th>
                            <th v-if="view == 'approvals'">Delivered Qty</th>
                            <th>Product</th>
                            <!--th>Description</th-->
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody v-if="order.order_items.length > 0">
                        <tr v-for="(order_item, index) in order.order_items" :key="index">
                            <td>{{order_item.quantity}}</td>
                            <td v-if="view == 'approvals'">{{ order_item.requested_quantity }}</td>
                            <td v-if="view == 'approvals'">{{ order_item.approved_quantity }}</td>
                            <td v-if="view == 'approvals'">{{ order_item.delivered_quantity / order_item.package_quantity }}</td>
                            <td>{{order_item.item_name != null ? order_item.item_name : order_item.item.name}} </td>
                            <!--td>{{ order_item.package.name }} of {{ order_item.package_quantity }}</td-->
                            <td>{{ currency(order_item.unit_price) }}</td>
                            <td>{{ currency(order_item.quantity *  order_item.unit_price)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    
        <div class="row">
            <div class="col-7">
                <p class="lead">Payment Methods:</p>
                <p v-if="order.payment_terms != null" class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {{ order.payment_terms.name }}<br>{{ order.payment_terms.description }}
                </p>
                <ul>
                    <li v-for="branch_account in accounts">
                        <strong>{{ branch_account.account_name }}</strong><br />
                        {{ branch_account.bank ? branch_account.bank.bank_name : 'No Bank Assigned'}}<br /> 
                        {{ branch_account.account_number }}
                    </li>
                </ul>
            </div>
            <div class="col-5">
                <!--p class="lead">Amount Due Date {{ExcelDate(order.delivery_date)}}</p-->

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
                                <td>{{ currency(order.logistics) }}</td>
                            </tr>
                            <tr>
                                <td>Discount:</td>
                                <td>{{ currency(order.discount) }}</td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td>{{ currency( sub_total + (sub_total * 0.075) + order.logistics - order.discount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    computed:{
        sub_total(){
            if (!this.order.order_items?.length) return 0;
            return this.order.order_items.reduce(
                (sum, it) => sum + it.unit_price * it.quantity, 0);
        },
    },
    data() {
        return {
            accounts: [],
            current_page: 1,
            editMode: false,
            loading: false,
            order_item: {},
            query: null,
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addFulfillment(order_item){
            this.loading = true;
            this.order_item = order_item;
            console.log(this.order_item);
            $('#fulfillOrderItemModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#fulfillOrderItemModal').modal('hide');
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/sales/orders/display')
            .then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Returns were not loaded successfully',
                })
            });
            this.loading = false;
                
        },
        refreshPage(response) {
            this.accounts = response.data.accounts;
            //this.company = response.data.company;
            this.closeModals();
        },
        updateOrder(){
            this.loading = true;
            $('#orderFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        order: Object,
        view: String,
    },
}
</script>