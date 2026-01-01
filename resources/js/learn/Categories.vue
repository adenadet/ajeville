<template>
    <div class="row">
        <div class="modal fade" id="categoryModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-show="editMode">Edit Category: {{Category.name}}</h4>
                        <h4 class="modal-title" v-show="!editMode">New Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <LmsFormCategory :category="Category" :editMode="editMode"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Categories</h3>
                    <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" @click="addCategory">Add New
                    </button>
                    </div>
                </div>
                <div class="card-body">
                    <LearnDetailCategoryList :categories="categories.data" />
                </div>
            </div>
        </div>
        
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <AdminSubCategory :categories="categories" :category_name="category_name" :sub_categories="sub_categories" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            categories:[],
            Category:{},
            category_name: '',
            editMode: false,
            form: new Form({}),
            sub_categories:[],
        }
    },
    methods:{
        addCategory(category){
            this.Category = {};
            this.editMode = false;
            Fire.$emit('editCategory', category);    
            $('#categoryModal').modal('show');
        },
        CategoryLoad(category){
            Fire.$emit('CategoryLoad', category);
        },
        deleteCategory(id){
            Swal.fire({
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
                    this.form.delete('/api/lms/categories/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        Fire.$emit('CatRefresh', response);   
                        })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                        });
                    }
                });  
            },
        editCategory(category){
            this.Category = category;
            this.editMode = true;
            Fire.$emit('editCategory', category);    
            $('#categoryModal').modal('show');
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/lms/categories')
            .then(response =>{
                this.categories = response.data.categories;
                this.sub_categories = response.data.categories[0].sub_categories;
                this.category_name = response.data.categories[0].name;
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Categories loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Categories not loaded successfully',
                })
            });
        },
        subCategoryLoad(category){
            this.sub_categories = category.sub_categories;
            this.category_name =  category.name;
        },
    },
    
    mounted() { 
        this.getAllInitials();
        Fire.$on('CatRefresh', response =>{
            $('#categoryModal').modal('hide');
            this.categories = response.data.categories;
            this.sub_categories = response.data.categories[0].sub_categories;
            this.category_name = response.data.categories[0].name;
            });
        Fire.$on('CategoryLoad', category =>{
            this.sub_categories = category.sub_categories;
            this.category_name =  category.name;
            });
        Fire.$on('SubCatRefresh', response =>{
            this.categories = response.data.categories;
            this.sub_categories = response.data.categories[0].sub_categories;
            this.category_name = response.data.categories[0].name;
            });
    }
}
</script>
