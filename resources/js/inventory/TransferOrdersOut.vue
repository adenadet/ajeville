<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Transfer Requests</h3>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-sm" @click="addRequest()">Add New</button>
                        </div>
                    </div>
                    <InventoryDetailTransferOrderList :transfer_orders="transfer_orders.data" source="out"/>
                    <div class="card-footer">
                        <div class="col-12">
                            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="transfer_orders.per_page != null ? transfer_orders.per_page : 52" :records="transfer_orders.total != null ? transfer_orders.total : 550" ></pagination>
                        </div>
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
            current_page: 1,
            editMode: false,
            form: new Form({}),
            transfer_order: {},
            transfer_orders: {data:[]},
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('transferRequestReload', response => {
            this.refreshPage(response)
        });
    },
    methods: {
        closeModals() {
            $('#storeModal').modal('hide');
        },
        deleteStore(id) {
            Swal.fire({
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
                        this.form.delete('/api/inventory/transfer_orders/' + id)
                            .then(response => {
                                Fire.$emit('storeReload', response);
                                Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                            })
                            .catch(() => {
                                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                            });
                    }
                });
        },
        getAllInitials(page = 1) {
            this.$Progress.start();
            axios.get('/api/inventory/transfer_orders?page=' + page)
                .then(response => {
                    this.refreshPage(response);
                    this.$Progress.finish();
                    toast.fire({
                        icon: 'success',
                        title: 'Transfer Requests loaded successfully',
                    });
                })
                .catch(() => {
                    this.$Progress.fail();
                    toast.fire({
                        icon: 'error',
                        title: 'Transfer Requests not loaded successfully',
                    })
                });
        },
        refreshPage(response) {
            this.stores = response.data.stores;
            this.closeModals();
        },
    },
}
</script>