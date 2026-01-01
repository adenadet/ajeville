<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>
    <div class="modal fade" id="customerModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="!editMode">New Customer</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <CRMFormCustomer :customer="customer" :editMode="false" @customerFormReload="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uploadCustomersModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Upload Customers</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <CRMFormCustomerUpload :editMode="false" @customerFormReload="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Customers</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 350px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default"@click="getAllInitials"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-primary ml-3" @click="addCustomer"><i class="fas fa-plus"></i></button>
                                <button type="button" class="btn btn-info"  @click="uploadCustomers"><i class="fas fa-upload"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <CRMDetailCustomerList :customers="customers.data" view="sales"  @customerReload="getAllInitials"/>
                </div>
                <div class="card-footer clearfix">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="customers.per_page != null ? customers.per_page : 52" :records="customers.total != null ? customers.total : 550" >
                    </pagination>
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
            customer: {},
            customers: {data: []},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    mounted() { this.getAllInitials();},
    methods: {
        addCustomer(){
            this.loading = true;
            this.customer = {};
            this.editMode = false;
            $('#customerModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#customerModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/crm/customers?page='+this.current_page+'&query='+this.query).then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Users not loaded successfully',})
            });
        },
        refreshPage(response) {
            this.customers = response.data.customers;
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
        uploadCustomers(){
            this.loading = true;
            $('#uploadCustomersModal').modal('show');
            this.loading = false;
        },
        
    },
}
</script>