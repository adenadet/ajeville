<template>
<div class="modal fade" id="categoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h4 class="modal-title" v-show="editMode">Edit Category: {{category.name}}</h4>
                <h4 class="modal-title" v-show="!editMode">New Category</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
            </div>
            <div class="modal-body">
                <InventoryFormCategory :editMode="editMode" :category.sync="category" @categoryReload="reloadCategory"/> 
            </div>
        </div>
    </div>
</div>
<div class="card-header">
    <h3 class="card-title">All Categories</h3>
    <div class="card-tools"><button class="btn btn-primary btn-sm">Add New</button></div>
</div>
<div class="card-body table-responsive p-0" style="height: 600px;">
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Parent Category</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="category in categories.data" :key="category.id">
                <td>{{ category.name }}</td>
                <td>{{ category.type != null ? category.type.name: 'N/A' }}</td>
                <td>{{ category.parent != null ? category.parent.name : 'N/A' }}</td>
                <td>
                    <!--button type="button" class="btn btn-sm btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <router-link class="btn btn-block dropdown-item" :to="'/admin/branches/'+branch.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                        <button class="btn btn-block dropdown-item" @click="deleteBranch(1)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Loan Request</button>
                    </div-->
                </td>
            </tr>
        </tbody>
    </table>
</div>
</template>
<script>
export default {
    data(){
        return  {
            category: {},
            editMode: false,
            loading: false,
        }
    },
    emits: ['reloadCategories'],
    mounted() {
        //this.$on('ItreloadCategoriesFill', item =>{
        //    this.ItemData.fill(item);
        //});
    },
    methods:{
        addCategory(){
            this.loading = true;
            this.editMode = false;
            this.category = {};
            $('#categoryModal').modal('show');
            this.loading = false; 
        },
        closeModals(){
            $('#categoryModal').modal('hide');
        },
        createItem(){
            this.loading = true
            this.ItemData.post('/api/inventory/categorys')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadCategories', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        getInitials(page=1){
            this.loading = true
            axios.get('/api/inventory/items?page='+page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
        },
        reloadCategory(){
            this.$emit('reloadCategories');
        },
        updateItem(){
            this.loading = true
            this.ItemData.put('/api/inventory/items/'+this.ItemData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('reloadCategories', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });              
        },
    },
    props:{
        categories: Array,
        source: String,
    }
}
</script>