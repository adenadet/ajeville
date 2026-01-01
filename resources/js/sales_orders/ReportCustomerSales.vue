<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-4">

        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Report</div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="sales_orders.length > 0">
                            <tr v-for="sales_order in sales_orders" :key="sales_order.id">
                                <td>{{ sales_order.customer }}</td>
                                <td>{{ sales_order.amount }}</td>
                                <td>{{ sales_order.total_paid }}</td>
                                <td><span class="tag tag-success">Approved</span></td>
                                <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
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
        refreshPage(response) {
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