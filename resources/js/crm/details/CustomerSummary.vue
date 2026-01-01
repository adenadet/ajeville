<template>
    <section class="container-fluid">
        <div class="modal fade" id="customerFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Update New Customer</h4>
                        <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <CRMFormCustomer :editMode="editMode" :customer.sync="customer" @customerReload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-dark card-outline">
            <div class="card-header">
                <h3 class="card-title">Customer Summary</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" @click="updateCustomer()"><i class="fas fa-edit"></i> Update Customer</button>
                </div>
            </div>
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" :src="'/dist/img/user4-128x128.jpg'" alt="User profile picture" />
                </div>

                <h3 class="profile-username text-center">{{ customer.name }}</h3>

                <p class="text-muted text-center">{{ customer.category != null ? customer.category.name : 'Uncategorized' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Account Balance</b> <a class="float-right">{{ currency(customer.balance) }}</a>
                    </li>
                </ul>
                <button class="btn btn-dark btn-block" @click="updateCustomer()"><b>Update Details</b></button>
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
            loading: false,
            query: '',
        }
    },
    mounted() {},
    methods: {
        closeModals() {
            $('#customerFormModal').modal('hide');
        },
        refreshPage(){
            this.$emit('customerReload');
            $('#customerFormModal').modal('hide');
        },
        updateCustomer(){
            this.loading = true;
            this.editMode = true;
            $('#customerFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        customer: Object,
        source: String,
    },
    watch:{
        customers(){}
    },
}
</script>