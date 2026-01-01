<template>
<section>
    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Category Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ArchiveFormCategory :editMode="editMode" :category.sync="category" @categoryReload="reloadCategory" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-navy">
                    <h3 class="card-title">All Categories</h3>
                    <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default"><i class="fas fa-search"></i></button>
                            <button type="button" class="btn btn-tool" @click="addCategory"><i class="fas fa-plus text-white"></i></button>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <ArchiveDetailCategoryList :categories="categories.data" source="main" actionable="all"></ArchiveDetailCategoryList>
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
            category: {},
            categories: {data: []},
            editMode: false,
            loading: false,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        closeModal(){
            $('#categoryModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        addCategory(){
            this.loading = true;
            this.editMode = false;
            this.category = {};
            $('#categoryModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/archives/categories').then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Categories was not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
        },
        reloadCategory(){
            this.getAllInitials();
        },
    },
}
</script>