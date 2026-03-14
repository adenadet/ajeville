<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>
    <div class="modal fade" id="itemTypeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Item Type: {{item_type.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Item Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormItemType :editMode="editMode" :item_type.sync="item_type" @reloadItemType="getInitials(current_page)"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">All Type</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" @click="addItemType()">Add New</button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <table class="table table-head-fixed text-nowrap table-stripped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody v-if="item_types.data.length > 0">
                            <tr v-for="item_type in item_types.data" :key="item_type.id">
                                <td>{{ item_type.name }}</td>
                                <td><div v-html="item_type.description"></div></td>
                                <td>{{ item_type.status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <button type="button" class="btn btn-tool text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <button class="btn btn-block dropdown-item" @click="updateItemType(item_type)"><i class="fa fa-edit mr-1 text-primary"></i> Edit </button>
                                        <button class="btn btn-block dropdown-item" @click="deleteItemType(item_type)"><i class="fa fa-recycle mr-1 text-danger"></i> {{item_type.status == 1 ? 'Deactivate' : 'Reactivate' }}</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="item_types.per_page != null ? item_types.per_page : 52" :records="item_types.total != null ? item_types.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import InventoryFormItemType from '@/inventory/forms/ItemType.vue';
export default {
    components:{
        InventoryFormItemType
    },
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            item_type: {},
            item_types: { data:[]},
            loading: false,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addItemType(){
            this.loading = true;
            this.editMode = false;
            this.item_type = {};
            $('#itemTypeModal').modal('show');  
            this.loading = false;
        },
        closeModals() {
            $('#itemTypeModal').modal('hide');
        },
        deleteItemType(brand){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Do you want to "+(brand.status == 1 ? "deactivate" : "reactivate")+" this item type?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes, "+(brand.status == 1 ? 'deactivate' : 'reactivate')+" it!",
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/inventory/item_types/' +brand.id)
                    .then(response => {
                        //this.$emit('itemsReload', response);
                        this.$swal.fire('Deleted!', 'Item Type has been reactivated/deactivated.', 'success');
                        this.getInitials();
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        getInitials(page=1){
            this.loading = true;
            this.closeModals();
            axios.get('/api/inventory/item_types?page='+page)
            .then(response =>{
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Item Types loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Item Types not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response){
            this.item_types = response.data.item_types;
        },
        updateItemType(item_type){
            this.loading = false;
            this.editMode = true;
            this.item_type = item_type;
            $('#itemTypeModal').modal('show');  
            this.loading = false;
        },
    },
}
</script>