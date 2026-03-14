<template>
<section class="container-fluid">
    <div class="modal fade" id="storeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">{{ editMode ? 'Edit Store' : 'Create New Store'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormStore :editMode="editMode" :store.sync="store" @storeReload="getInitials(current_page)"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">All Stores</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" @click="addStore()">Add New</button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 600px;">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="store in stores.data" :key="store.id">
                                <td>{{ store.name }}</td>
                                <td>{{ store.branch != null ? store.branch.name: (store.branch_id == 0 ? 'Headquarter' : 'N/A') }}</td>
                                <td>{{ store.department != null ? store.department.name: 'N/A' }}</td>
                                <td>{{ store.status != 1 ? 'Inactive' : 'Active' }}</td>
                                <td>
                                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <router-link class="btn btn-block dropdown-item" :to="'./stores/'+store.id"><i class="fa fa-eye mr-1"></i> View </router-link>
                                        <button class="btn btn-block dropdown-item" @click="updateStore(store)"><i class="fa fa-edit mr-1 text-primary"></i> Edit </button>
                                        <button class="btn btn-block dropdown-item" @click="deleteStore(store.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="stores.per_page != null ? stores.per_page : 52" :records="stores.total != null ? stores.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import InventoryFormStore from '@/inventory/forms/Store.vue';
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            store: {},
            stores: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addStore(){
            this.loading = true;
            this.editMode = false;
            //Fire.$emit('StoreDataFill', {});
            $('#storeModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#storeModal').modal('hide');
        },
        deleteStore(id){
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
            this.loading = true;
            axios.get('/api/inventory/stores?page='+page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Stores loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Stores not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.stores = response.data.stores;
            this.closeModals();
        },
        updateStore(store){
            this.loading = true;
            this.editMode = true;
            this.store = store;
            $('#storeModal').modal('show');
            this.loading = false;         
        },
    },
}
</script>