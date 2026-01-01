<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-5">
                <CRMDetailCustomerSummary :customer="customer" />
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-navy">
                        <h3 class="card-title">Contacts</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <CRMDetailContactList source="customer" :id="customer.uuid" :contacts.sync="contacts" />
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Payments</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <FinanceDetailPaymentList source="customer" :id="customer.uuid" :payments="payments" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-navy">
                        <h3 class="card-title">Recent Sales Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <SalesDetailOrderList source="customer" :id="customer.uuid" :orders="orders" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Recent Sales Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <SalesDetailReturnList source="customer" :id="customer.uuid" />
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
            contacts: [],
            customer: {},
            editMode: false,
            form: new Form({}),
            loading: false,
            orders: [],
            payments: [],
            query: '',
        }
    },
    mounted() { this.getInitials(); },
    methods: {
        closeModals() {
            $('#customerFormModal').modal('hide');
        },
        deactivateCustomer(id) {
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
                    this.form.delete('/api/crm/customers/'+id)
                    .then(response => {
                        this.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'Customer has been deactivated.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        getInitials() {
            this.loading = true 
            axios.get('/api/crm/customers/'+this.$route.params.id+'?status='+this.source+'&search='+this.query)
            .then(response => {
                this.refreshPage(response);
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Customers loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Customers were not loaded successfully',
                })
            });
        },
        refreshPage(response) {
            this.customer = response.data.customer;
            this.order = response.data.order;
            //this.$emit('customerReload', response);
        },
        updateCustomer(customer){
            this.loading = true;
            this.customer = customer;
            this.editMode = true;
            $('#customerFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{},
    
}
</script>