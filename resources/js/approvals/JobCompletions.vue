<template>
    <section class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Job Completions</h3>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 450px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary mr-1" @click="searchPurchaseOrder"><i class="fas fa-search"></i></button>
                            <select class="form-control" v-model="source" @change="getInitials(1)">
                                <option value="0">My Drafts</option>
                                <option value="1">Awaiting Approval</option>
                                <option value="2">Approved</option>
                                <option value="3">Ongoing</option>
                                <option value="4">Completed</option>
                                <option value="all">All</option>
                            </select>
                            <button type="button" class="btn btn-primary ml-1" @click="addPurchaseOrder"><i class="fa fa-user-plus"></i></button>
                            <button type="button" class="btn btn-success ml-1" @click="uploadPurchaseOrders"><i class="fa fa-upload"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <ProcurementDetailPurchaseOrderList :purchase_orders="purchase_orders.data" source="approvals" ty="list" />
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
            purchase_orders: { data: []},
            query: '',
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        closeModals() {
            $('#storeModal').modal('hide');
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
        getInitials(page = 1) {
            this.loading = true //.start();
            axios.get('/api/procurement/purchase_orders?page='+page+'&status='+this.source)
                .then(response => {
                    this.refreshPage(response);
                    this.loading = false; //.finish();
                    this.$toast.fire({
                        icon: 'success',
                        title: 'Transfer Requests loaded successfully',
                    });
                })
                .catch(() => {
                    this.loading = false; //.fail();
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Transfer Requests not loaded successfully',
                    })
                });
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