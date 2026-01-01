<template>
<section class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Purchase Orders</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 500px;">
                <ProcurementDetailPurchaseOrderList :purchase_orders.sync="purchase_orders.data" source="approvals" ty="list" />
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
            purchase_orders: { data: []},
            query: '',
            source: 'all',
            status: 'pending',
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
            axios.get('/api/procurement/purchase_orders?page='+page+'&status=pending')
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

            alert("Working");
            this.loading = false;
        }
    },
}
</script>