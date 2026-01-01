<template>
<section class="overlay-wrapper">
    <div class="modal fade" id="brandFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Brand: {{brand.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Brand</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormBrand :editMode="editMode" :brand.sync="brand" @reloadBrand="getAllInitials"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="brandModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Brand: {{brand.name}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryDetailBrand :brand.sync="brand" /> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">All Brands</h3>
                    <div class="card-tools"><button class="btn btn-primary btn-xs" @click="addBrand"><i class="fa fa-plus mr-1"></i>Add New</button></div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px">
                    <table class="table table-head-fixed table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <td>S/N</td>
                                <td>Name</td>
                                <td>Status</td>
                                <td>Description</td>
                                <td>&nbsp;</td>
                            </tr>
                        </thead>
                        <tbody v-if="brands != null && brands.total != 0">
                            <tr v-for="(brand, index) in brands.data" :key="brand.id">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ brand.name }}</td>
                                <td>{{ firstUp(brand.status) }}</td>
                                <td v-html="readMore(brand.description, 50, '...')"></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-tools text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <button type="button" class="btn btn-block dropdown-item" @click="viewBrand(brand)"><i class="fa fa-eye mr-1 text-primary"></i> View Brand </button>
                                        <button type="button" class="btn btn-block dropdown-item" @click="editBrand(brand)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Brand </button>
                                        <button type="button" class="btn btn-block dropdown-item" @click="deactivateBrand(brand)"><i class="fa fa-circle-notch mr-1 text-danger"></i> Deactivate Brand </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else></tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="brands.per_page != null ? brands.per_page : 52" :records="brands.total != null ? brands.total : 550"></pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            brand: {},
            brandData: new Form({
                id: '',
                name: '',
                description: '',
                status: '',
            }),
            brands: {data:[], total: 0},
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits: ['itemsReload'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addBrand(){
            this.loading = true;
            this.editMode = false;
            this.item = {};
            $('#brandFormModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#brandFormModal').modal('hide');
        },
        deactivateBrand(brand){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Do you want to "+(brand.status == 'Active' ? "deactivate" : "reactivate")+" this brand?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes, "+(brand.status == 'Active' ? 'deactivate' : 'reactivate')+" it!",
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/inventory/brands/' +brand.id)
                    .then(response => {
                        //this.$emit('itemsReload', response);
                        this.$swal.fire('Deleted!', 'Brand has been reactivated/deactivated.', 'success');
                        this.getAllInitials();
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        editBrand(brand){
            this.loading = true;
            this.editMode = true;
            this.brand = brand;
            $('#brandFormModal').modal('show');
            this.loading = false;  
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/inventory/brands?type=all&page='+this.current_page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Items loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Items not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.brands = response.data.brands;
            this.closeModals();
        },
        viewBrand(brand){
            this.loading = true;
            this.brand = brand;
            $('#brandModal').modal('show');
            this.loading = false;
        }
    },
}
</script>