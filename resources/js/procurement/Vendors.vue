<template>
<section>
    <div class="modal fade" id="vendorFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">{{editMode ? 'Edit Vendor Detail' : 'Create New Vendor'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormVendor :editMode.sync="editMode" :vendor.sync="vendor" @vendorReload="getInitials(current_page)"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Vendors</h3>
            <div class="card-tools">
                <div class="input-group input-group" style="width: 350px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="search" @change="getInitials">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary mr-1" @click="getInitials" title="Search Vendor"><i class="fas fa-search"></i></button>
                        <button type="button" class="btn btn-primary ml-1" @click="addVendor" title="Add New Vendor"><i class="fa fa-user-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body overlay-wrapper table-responsive p-0" style="height:600px">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="vendors != null && vendors.total != 0">
                    <tr v-for="vendor in vendors.data">
                        <td>{{vendor.name}}</td>
                        <td>{{vendor.category != null ? vendor.category.name : 'Not Applicable'}}</td>
                        <td>{{vendor.email}}</td>
                        <td>{{vendor.phone}}</td>
                        <td>
                            <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu">
                                <router-link class="btn btn-block dropdown-item" :to="'./vendors/'+vendor.id"><i class="fa fa-eye mr-1 text-primary"></i> View</router-link>
                                <button class="btn btn-block dropdown-item" @click="updateVendor(vendor)"><i class="fa fa-edit mr-1"></i> Edit </button>
                                <button class="btn btn-block dropdown-item" @click="deleteVendor(vendor.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="5">No Vendor has created.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="vendors.per_page != null ? vendors.per_page : 52" :records="vendors.total != null ? vendors.total : 550" >
            </pagination>
        </div>
    </div>
</section>
</template>
<script>
import ProcurementFormVendor from './forms/Vendor.vue';
export default {
    components:{
        ProcurementFormVendor
    },
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            search: '',
            type: 'all',
            vendor: {},
            vendors: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addVendor(){
            this.loading = true;
            this.editMode = false;
            this.vendor = {};
            //Fire.$emit('StoreDataFill', {});
            $('#vendorFormModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#vendorFormModal').modal('hide');
            $('#vendorModal').modal('hide');
        },
        deleteVendor(id){
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
                if(result.value){
                    this.form.delete('/api/inventory/stores/'+id)
                    .then(response=>{
                        Fire.$emit('storeReload', response);  
                        Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/vendors?page='+page+'&type='+this.type+'&search='+this.search)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Vendors loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendors not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.vendors = response.data.vendors;
        },
        updateVendor(vendor){
            this.loading = true;
            this.editMode = true;
            this.vendor = vendor;
            $('#vendorFormModal').modal('show');
            this.loading = false;         
        },
    },
}
</script>
