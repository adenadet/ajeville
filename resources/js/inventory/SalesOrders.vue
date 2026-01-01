<template>
<section>
    <div class="modal fade" id="salesOrderModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Create New Sales Order Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <InventoryFormSalesOrder :editMode="editMode" form_type="direct_sales" :transfer_order="{}" @transferOrderReload="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">Sales Orders</h3>
            <div class="card-tools">
                <div class="input-group" style="width: 350px;">
                    <input type="text" v-model="query" name="table_search" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default" @click="getAllInitials(1)"><i class="fas fa-search"></i></button>
                        <select class="form-control ml-3" v-model="status" @change="getAllInitials(1)">
                            <option value="all">All</option>
                            <option value="0">Draft</option>
                            <option value="1">Awaiting Auth.</option>
                            <option value="2">Unaccepted</option>
                            <option value="3">Ongoing</option>
                            <option value="6">Completed</option>
                            <option value="10">Cancelled</option>
                        </select>
                        <button type="button" class="btn btn-tool" @click="createSalesOrder()"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <InventoryDetailTransferOrderList :transfer_orders="transfer_orders.data" view="sales"/>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="transactions.per_page != null ? transactions.per_page : 52" :records="transactions.total != null ? transactions.total : 550" ></pagination>
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
            query: '',
            status: 'all',
            transactions: {data: []},
            transfer_orders: {data: []},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        closeModals() {
            $('#salesOrderModal').modal('hide');
        },
        createSalesOrder(){
            $('#salesOrderModal').modal('show');
        },
        deleteSalesOrder(id) {
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
                    this.form.delete('/api/inventory/sales_orders/'+id)
                    .then(response => {
                        Fire.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        getAllInitials(page = 1) {
            this.loading = true;
            axios.get('/api/inventory/sales_orders?page='+page+'&status='+this.status+'&query='+this.query)
            .then(response => {
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Sales Orders 2 not loaded successfully',
                })
            });
        },
        refreshPage(response) {
            this.sales_orders = response.data.sales_orders;
            this.transfer_orders = response.data.transfer_orders;
            this.closeModals();
        },
    },
}
</script>