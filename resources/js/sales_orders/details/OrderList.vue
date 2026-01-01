<template>
    <div class="modal fade" id="approvalSalesOrderModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Approve Sales Order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ApprovalFormSalesOrder :order.sync="order" @approvalSalesReload="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <!--div class="modal fade" id="deliveryNoteFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">Create Delivery Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesFormDeliveryNote :order.sync="order" @deliveryReload="refreshPage" />
                </div>
            </div>
        </div>
    </div-->
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
                    <SalesFormOrder :order_id.sync="order.unique_id" @orderFormReload="refreshPage" :editMode="true" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">Payment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormPayment :order_id="order.unique_id" :customer="order.customer" trans_type="sales" @refreshPaymentForm="refreshPage" :editMode="editMode" />
                </div>
            </div>
        </div>
    </div>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <table class="table table-striped table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Store</th>
                    <th>Order Number</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="orders.length > 0">
                <tr v-for="(order, index) in orders" :key="index">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ order.customer != null ? order.customer.name : 'Walk In Customer'  }}</td>
                    <td>{{ order.store != null ? order.store.name : 'Not Assigned' }}</td>
                    <td>{{ order.unique_id }}</td>
                    <td>{{ ExcelDate(order.date) }}</td>
                    <td>{{ currency(order.grand_amount) }}</td>
                    <td>
                        <span v-if="order.status == 0" class="badge badge-secondary">Draft</span>
                        <span v-else-if="order.status == 1" class="badge badge-secondary">Awaiting Approval</span>
                        <span v-else-if="order.status == 2" class="badge badge-primary">Approved</span>
                        <span v-else-if="order.status == 10" class="badge badge-success">Completed</span>
                        <span v-else-if="order.status == 40" class="badge badge-danger">Completed</span>
                        <span v-else class="badge badge-primary">Ongoing</span>
                    </td>
                    <td>
                        <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="view == 'approvals'">
                            <router-link :to="'/approvals/sales_orders/'+order.unique_id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View</router-link>
                            <button v-if="order.status == 1" class="dropdown-item btn btn-block btn-sm" @click="approveOrder(order.id)"><i class="fa fa-file-signature mr-1 text-info"></i> Approve Order</button>
                            <button v-if="order.status > 1 && order.status < 10" class="dropdown-item btn btn-block btn-sm" @click="resendAppointment(order.id)"><i class="fa fa-envelope mr-1 text-purple"></i> Resend Invoice</button>
                            <button v-if="order.status <= 1 || order.status == 40" class="dropdown-item btn btn-block btn-sm" @click="deleteOrder(order.unique_id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="view == 'sales'">
                            <router-link :to="'/sales_orders/orders/'+order.unique_id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View</router-link>
                            <button v-if="order.status <= 1" class="dropdown-item btn btn-block btn-sm" @click="updateOrder(order)"><i class="fa fa-edit mr-1 text-warning"></i> Update Order</button>
                            <button v-show="order.status <= 1" class="dropdown-item btn btn-block btn-sm" @click="makePayment(order)"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                            <button v-if="order.status > 1 && order.status < 10" class="dropdown-item btn btn-block btn-sm" @click="resendInvoice(order.id)"><i class="fa fa-envelope mr-1 text-purple"></i> Resend Invoice</button>
                            <!--button v-if="order.status >= 2 && order.status < 10" class="dropdown-item btn btn-block btn-sm" @click="createDeliveryNote(order.id)"><i class="fa fa-truck mr-1 text-info"></i> Create Delivery Note</button-->
                            <router-link v-if="order.status >= 2 && order.status < 10" :to="'/sales_orders/orders/fulfill/'+order.unique_id" target="_blank" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-shopping-cart mr-1 text-success"></i> Fulfill Order</router-link>
                            <button v-if="order.status >= 2 && order.status < 10" class="dropdown-item btn btn-block btn-sm" @click="markCompleted(order.id)"><i class="fa fa-check-double mr-1 text-success"></i> Mark As Completed</button>
                            <button v-if="order.status <= 1 || order.status == 10" class="dropdown-item btn btn-block btn-sm" @click="deleteOrder(order.unique_id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else>
                            <router-link :to="'/inventory/direct_purchases/'+order.unique_id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View</router-link>
                            <button v-show="order.status == 0" class="dropdown-item btn btn-block btn-sm" @click="makePayment(order)"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                            <button v-show="order.status == 1" class="dropdown-item btn btn-block btn-sm" @click="viewPayment(order)"><i class="fa fa-file-pdf mr-1 text-success"></i> View Receipt</button>
                            <button v-show="order.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deleteAppointment(order.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                            <button v-if="order.status <= 1" class="dropdown-item btn btn-block btn-sm" @click="updateOrder(order)"><i class="fa fa-edit mr-1 text-warning"></i> Update Order</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="8" class="text-center">No Purchase Orders Found</td>
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
            order: {},
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    emits:['salesOrderReload'],
    mounted() {},
    methods: {
        approveOrder(id){
            this.loading = true;
            axios.get('/api/sales/orders/'+ id)
            .then(response => {
                this.order = response.data.order;
                $('#approvalSalesOrderModal').modal('show');
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Sales Order was not loaded successfully',
                })
            });
        },
        closeModals() {
            $('#approvalSalesOrderModal').modal('hide');
            $('#deliveryNoteFormModal').modal('hide');
            $('#orderFormModal').modal('hide');
            $('#paymentFormModal').modal('hide');
            $('#storeModal').modal('hide');
        },
        createDeliveryNote(id){
            this.loading = true;
            axios.get('/api/sales/orders/'+ id)
            .then(response => {
                this.order = response.data.order;
                $('#deliveryNoteFormModal').modal('show');
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Sales Order was not loaded successfully',
                })
            });
        },
        deleteOrder(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/sales/orders/' + id)
                    .then(response => {
                        this.$emit('salesOrderReload');
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        makePayment(order){
            this.loading = true;
            this.editMode = false;
            this.customer = order.customer;
            this.order = order;
            $('#paymentFormModal').modal('show');
            this.loading = false;
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
                        this.$emit('salesOrderReload');
                        this.$swal.fire('Completed!', 'Order has been completed.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        refreshPage() {
            this.closeModals();
            this.$emit('salesOrderReload');
        },
        updateOrder(order) {
            this.loading = true;
            this.order = order;
            this.editMode = true;
            $('#orderFormModal').modal('show');
            this.loading = false;
        },
    },
    props:{
        orders: Array,
        source: String,
        view: String,
    },
    watch:{
        orders(){
        /*    if (this.purchase_orders.length == 0){this.loading = true;}
            else{this.loading = false;}*/
        }
    },
}
</script>