<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="categoryFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Update Category: '+category.name : 'New Category' }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormVendorCategory :category.sync="category" :editMode.sync="editMode" @vendorCategoryReload="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Vendor Categories</h3>
            <div class="card-tools">
                <button class="btn btn-sm btn-primary" @click="addCategory()">Add Category</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0" style="max-height: 600px;">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(category, index) in categories.data" :key="index">
                        <td>{{ category.id }}</td>
                        <td>{{ category.name }}</td>
                        <td v-html="readMore(category.description, 25, '...')"></td>
                        <td>{{ category.status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" @click="editCategory(category)">Edit</button>
                            <button class="btn btn-danger btn-sm" @click="deleteCategory(category)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            category: {},
            categories: {},
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
        addCategory(){
            this.loading = true;
            this.editMode = false;
            this.category = {};
            $('#categoryFormModal').modal('show');  
            this.loading = false;
        },
        editCategory(category){
            this.loading = true;
            this.editMode = true;
            this.category = category;
            $('#categoryFormModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#categoryFormModal').modal('hide');
            $('#vendorCategoryModal').modal('hide');
        },
        deleteCategory(id){
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
                    this.form.delete('/api/procurement/vendor_categories/'+id)
                    .then(response=>{
                        this.$emit('vendorCategoryReload', response);  
                        this.$swal.fire('Deleted!', 'Vendor Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/vendor_categories?page='+page+'&type='+this.type+'&search='+this.search)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendor Categories not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
        },
        updateVendorCategory(vendor){
            this.loading = true;
            this.editMode = true;
            this.vendor = vendor;
            $('#vendorCategoryFormModal').modal('show');
            this.loading = false;         
        },
    },
}
</script>