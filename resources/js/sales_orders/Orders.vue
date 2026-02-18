<template>
<section class="overlay-wrapper">
    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="orderModalLabel">New Sales Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesFormOrder :order.sync="order" @orderFormReload="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Sales Orders</h3>
            <div class="card-tools">
                <div class="input-group input-group" style="width: 500px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary mr-1" @click="getInitials(1)"><i class="fas fa-search"></i></button>
                        <select class="form-control" v-model="source" @change="getInitials(1)">
                            <option value="0">My Drafts</option>
                            <option value="1">Awaiting Approval</option>
                            <option value="2">Approved</option>
                            <option value="3">Ongoing</option>
                            <option value="4">Completed</option>
                            <option value="all">All</option>
                        </select>
                        <button type="button" class="btn btn-primary ml-1" @click="addOrder()"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-success ml-1" @click="uploadOrders()"><i class="fa fa-upload"></i></button>
                        <button type="button" class="btn btn-info ml-1" @click="downloadOrders()"><i class="fa fa-download"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0 overlay-wrapper table-responsive" style="height: 500px;">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <SalesDetailOrderList :orders="orders.data" view="sales" @salesOrderReload="getInitials"/>
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="orders.per_page != null ? orders.per_page : 52" :records="orders.total != null ? orders.total : 550" ></pagination>
        </div>
    </div>
    
</section>
</template>
<script>
import SalesDetailOrderList from '@/sales_orders/details/OrderList.vue';
import SalesFormOrder from '@/sales_orders/forms/Order.vue';
export default {
    components: {SalesDetailOrderList, SalesFormOrder},
    data() {
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            order: {},
            orders: { data: []},
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
        closeModals() {
            $('#orderModal').modal('hide');
        },
        downloadOrders(){},
        getInitials(page = 1) {
            this.loading = true 
            axios.get('/api/sales/orders?page='+page+'&query='+(this.query != null ? this.query : 0) +'&status='+this.source)
                .then(response => {
                    this.refreshPage(response);
                    this.loading = false; 
                    this.$toast.fire({
                        icon: 'success',
                        title: 'Sales Orders loaded successfully',
                    });
                })
                .catch(() => {
                    this.loading = false;
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Sales Orders not loaded successfully',
                    });
                });
        },
        refreshPage(response) {
            this.orders = response.data.orders;
            this.closeModals();
        },
        searchPurchaseOrder(){
            this.loading = true;

            
            this.loading = false;
        }
    },
}
</script>