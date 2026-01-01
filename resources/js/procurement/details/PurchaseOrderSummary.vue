<template>
<section>
    <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-globe"></i> AdminLTE, Inc.
                    <small class="float-right">Date: 2/10/2014</small>
                </h4>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                Vendor
                <address v-if="purchase_order.vendor != null">
                    <strong>{{ purchase_order.vendor.name }}</strong><br>
                    <p v-html="purchase_order.vendor.address"></p>
                    Phone: {{purchase_order.vendor.phone}}<br>
                    Email: {{purchase_order.vendor.email}}
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                To
                <address v-if="purchase_order.store != null">
                    <strong>{{ purchase_order.store != null ? purchase_order.store.name: 'Warehouse'}}</strong><br>
                    795 Folsom Ave, Suite 600<br>
                    San Francisco, CA 94107<br>
                    Phone: (555) 539-1037<br>
                    Email: john.doe@example.com
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <b>Order ID:</b> {{purchase_order.unique_id}}<br>
                <b>Payment Due:</b> {{ purchase_order.date }}<br>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(order_item, index) in purchase_order.order_items" :key="order_item.id">
                            <td>{{addOne(index)}}</td>
                            <td>{{ order_item.item_name }}</td>
                            <td>{{ order_item.package != null ? order_item.package.name+' of '+order_item.package_quantity+' items': 'Units'}}</td>
                            <td>{{ order_item.quantity }}</td>
                            <td>{{ order_item.unit_price }}</td>
                            <td>{{ order_item.unit_price * order_item.quantity}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {{purchase_order.payment_terms != null ? purchase_order.payment_terms.name : '30 days validity'}}
                </p>
            </div>
            <div class="col-6">
                <p class="lead">Amount Due 2/22/2014</p>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr><td style="width:50%">Subtotal:</td ><td>$250.30</td></tr>
                            <tr><td>Tax (9.3%)</td><td>$10.34</td></tr>
                            <tr><td>Shipping:</td><td>$5.80</td></tr>
                            <tr><td>Total:</td><td>$265.24</td></tr>
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
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    mounted() {},
    methods: {},
    props:{
        purchase_order: Object,
    },
    watch:{
        purchase_orders(){
        }
    },
}
</script>