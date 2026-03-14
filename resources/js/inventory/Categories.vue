<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>
    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Category: {{category.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormCategory :editMode="editMode" :category.sync="category" @reloadCategory="getInitials"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">All Categories</h3>
                    <div class="card-tools"><button class="btn btn-primary btn-sm" @click="addCategory">Add New</button></div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <table class="table table-head-fixed table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <td>Name</td>
                                <td>Type</td>
                                <td>Classification</td>
                                <td>Parent Category</td>
                                <td>&nbsp;</td>
                            </tr>
                        </thead>
                        <tbody v-if="categories != null && categories.total != 0">
                            <tr v-for="category in categories.data" :key="category.id">
                                <td>{{ category.name }}</td>
                                <td>{{ category.item_type != null ? category.item_type.name: 'N/A' }}</td>
                                <td>{{ category.classification != null ? category.classification.name: 'N/A' }}</td>
                                <td>{{ category.category != null ? category.category.name : 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-tool text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <button class="btn btn-block dropdown-item" @click="updateCategory(category)"><i class="fa fa-edit mr-1 text-primary"></i> Edit Category </button>
                                        <button class="btn btn-block dropdown-item" @click="deleteCategory(category)"><i class="fa fa-recycle mr-1 text-danger"></i> {{category.status == 1 ? 'Deactivate Category' : 'Reactivate Category'}} </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="categories.per_page != null ? categories.per_page : 52" :records="categories.total != null ? categories.total : 550"></pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import InventoryFormCategory from '@/inventory/forms/Category.vue';
export default {
    components:{
        InventoryFormCategory
    },
    data(){
        return  {
            categories: {},
            category: {},
            current_page: 1,
            cat_type: 'all',
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addCategory(){
            this.loading = true;
            this.editMode = false;
            this.category = {};
            $('#categoryModal').modal('show');
            this.loading = false;
        },
        deleteCategory(category){
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
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/inventory/categories/'+category.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Notice has been deleted.', 'success');
                        this.$emit('reloadCategory');   
                    })
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        getInitials(page=1){
            this.loading = true;
            axios.get('/api/inventory/categories?page='+page)
            .then(response =>{
                this.loading = false;
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Categories loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Categories not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            //this.categorys = response.data.categorys;
        },
        updateCategory(category){
            this.loading = true;
            this.editMode = true;
            this.category = category;
            $('#categoryModal').modal('show');
            this.loading = false;
        },
        
    },
}
</script>