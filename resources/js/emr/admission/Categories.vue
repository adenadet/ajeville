<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="categoryFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormCategory :category.sync="category" :editMode="editMode" @refreshCategoryForm="getAllInitals()" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Categories</h3>

                <div class="card-tools">
                    <div class="input-group" style="width: 350px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <select class="form-control ml-1 mr-1" v-model="type" @change="getAllInitials">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <button type="button" class="btn btn-default mr-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                            <button type="button" class="btn btn-primary mr-1" @click="addCategory"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 500px;">
                <table class="table table-head-fixed table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th><button class="btn btn-primary btn-sm" type="button" @click="addCategory"><i class="fa fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(category, index) in categories.data" :key="category.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ category.name }}</td>
                            <td v-html="readMore(category.description, 50, '...')" :title="category.description"></td>
                            <td>
                                <span v-if="category.status == 1" class="badge badge-success">Active</span>
                                <span v-else class="badge badge-danger">Inactive</span>
                            </td>
                            <td>{{ ExcelDate(category.created_at) }}</td>
                            <td>{{ ExcelDate(category.updated_at) }}</td>
                            <td>
                                <button class="nav-link btn btn-default btn-xs" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <button class="dropdown-item btn btn-block btn-sm" @click="updateCategory(category)"><i class="fa fa-edit mr-1 text-warning"></i> Update Room Type</button>
                                    <button class="dropdown-item btn btn-block btn-sm" @click="deactivateCategory(category)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Room Type</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="categories.per_page != null ? categories.per_page : 52" :records="categories.total != null ? categories.total : 550" ></pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            category: {},
            categories: {total: 0, data: []},
            current_page: 1,
            editMode: false,
            query: '',
            type: 'active',
        }
    },
    methods: {
        addCategory(){
            this.editMode = false;
            this.loading = true;
            this.appointment = {};
            $('#categoryFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#categoryFormModal').modal('hide');
        },
        deactivateCategory(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/emr/admissions/categories/' + id)
                    .then(() => {
                        this.$swal.fire({ icon: 'success', title: 'The Category has been cancelled', showConfirmButton: false, timer: 1500 });
                        this.getAllInitials();
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                    })
                }
            })
        },
        getAllInitials(){
            this.loading = true;
            this.closeModals();
            axios.get('/api/emr/admissions/categories?type='+this.type+'&query='+this.query)
            .then(res => {
                this.categories = res.data.categories;
            })
            .finally(() => {
                this.loading = false
            })
        },
        updateCategory(category){
            this.editMode = true;
            this.loading = true;
            this.category = category;
            $('#categoryFormModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        this.getAllInitials()
    },
}
</script>