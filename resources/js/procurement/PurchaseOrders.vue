<template>
<section class="row">
    <div class="modal fade" id="purchaseOrderModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">New Purchase Order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormPurchaseOrder :purchase_order.sync="purchase_order" :editMode.sync="editMode" @purchaseOrderReload="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Purchase Orders</h3>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 450px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary mr-1" @click="searchPurchaseOrder"><i class="fas fa-search"></i></button>
                            <select class="form-control" v-model="status" @change="getInitials()">
                                <option value="draft">My Drafts</option>
                                <option value="pending">Awaiting Approval</option>
                                <option value="approved">Approved</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="completed">Rejected</option>
                                <option value="all">All</option>
                            </select>
                            <button type="button" class="btn btn-primary ml-1" @click="addPurchaseOrder"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-success ml-1" @click="uploadPurchaseOrders"><i class="fa fa-upload"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 500px">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <ProcurementDetailPurchaseOrderList :purchase_orders="purchase_orders.data" source="" ty="list" @refreshPurchaseOrderList="getInitials"/>
            </div>
            <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="purchase_orders.per_page != null ? purchase_orders.per_page : 52" :records="purchase_orders.total != null ? purchase_orders.total : 550" >
            </pagination>
        </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            purchase_order: {},
            purchase_orders: { data: []},
            query: '',
            source: 'mine',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addPurchaseOrder(){
            this.loading = true;
            this.purchase_order = {};
            $('#purchaseOrderModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#purchaseOrderModal').modal('hide');
        },
        deletePurchaseOrder(id) {
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
                    this.form.delete('/api/procurement/purchase_orders/' + id)
                        .then(response => {
                            this.$emit('storeReload', response);
                            this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        })
                        .catch(() => {
                            this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                        });
                }
            });
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/procurement/purchase_orders?source=mine&page='+this.current_page+'&status='+this.status+'&query='+this.query)
            .then(response => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Purchase Orders loaded successfully',
                });
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Orders not loaded successfully',
                })
            });
            this.loading = false; //.fail();
        },
        refreshPage(response) {
            this.purchase_orders = response.data.purchase_orders;
            this.closeModals();
        },
        searchPurchaseOrder(){
            this.loading = true;
            this.loading = false;
        }
    },
}
</script>