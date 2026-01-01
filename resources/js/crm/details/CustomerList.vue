<template>
    <section class="overlay-wrapper table-responsive p-0">
        <div class="modal fade" id="customerFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Create New Customer</h4>
                        <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <CRMFormCustomer :editMode="editMode" :customer.sync="customer" @customerFormReload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card-body table-responsive p-0" style="height: 600px;">
            <table class="table table-head-fixed table-striped text-nowrap">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Unique ID</th>
                        <th>Balance</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Description</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="customers.length > 0">
                    <tr v-for="customer in customers" :key="customer.uuid">
                        <td>{{ customer.name }}</td>
                        <td>{{ customer.uuid }}</td>
                        <td>{{ currency(customer.balance) }}</td>
                        <td>{{ customer.category != null ? customer.category.name : 'N/A' }}</td>
                        <td>{{ customer.email }}</td>
                        <td>{{ customer.phone }}</td>
                        <td v-html="readMore(customer.description, 25, '...')"></td>
                        <td>
                            <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <router-link :to="'/sales_orders/customers/'+(customer.uuid != null ? customer.uuid : customer.id)"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Customer</button></router-link>
                                <button class="dropdown-item btn btn-block btn-sm" @click="updateCustomer(customer)"><i class="fa fa-edit mr-1 text-success"></i> Update Record</button>
                                <button class="dropdown-item btn btn-block btn-sm" @click="deactivateCustomer(customer.uuid != null ? customer.uuid : customer.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Customer</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr><td colspan="8" class="text-center" v-if="customers.length == 0">No customers found.</td></tr>
                </tbody>
            </table>
        </div>    
    </section>
</template>
<script>
export default {
    data() {
        return {
            customer: {},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    emits:['customerReload'],
    mounted() {},
    methods: {
        closeModal() {
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
                        this.$emit('customerReload', response);
                        this.$swal.fire('Deleted!', 'Customer has been deactivated.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        refreshPage(){
            this.$emit('customerReload');
            this.closeModal();
        },
        updateCustomer(customer){
            this.loading = true;
            this.customer = customer;
            this.editMode = true;
            $('#customerFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        customers: Array,
        source: String,
    },
    watch:{
        customers(){}
    },
}
</script>