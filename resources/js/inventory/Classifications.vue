<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>
    <div class="modal fade" id="classificationModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Classification: {{classification.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Classification</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormClassification :editMode="editMode" :classification.sync="classification" @reloadClassification="getInitials"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">All Classifications</h3>
                    <div class="card-tools"><button class="btn btn-primary btn-sm" @click="addClassification">Add New</button></div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <table class="table table-head-fixed table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody v-if="classifications != null && classifications.total != 0">
                            <tr v-for="classification in classifications.data" :key="classification.id">
                                <td>{{ classification.name }}</td>
                                <td :title="classification.description"><div v-html="readMore(classification.description, 40, '...')"></div></td>
                                <td>{{ classification.status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-tool text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <button class="btn btn-block dropdown-item" @click="updateClassification(classification)"><i class="fa fa-edit mr-1 text-primary"></i> Edit Classification </button>
                                        <button class="btn btn-block dropdown-item" @click="deleteClassification(classification)"><i class="fa fa-recycle mr-1 text-danger"></i> {{classification.status == 1 ? 'Deactivate Classification' : 'Reactivate Classification'}} </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="classifications.per_page != null ? classifications.per_page : 52" :records="classifications.total != null ? classifications.total : 550"></pagination>
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
            classifications: {},
            classification: {},
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
        addClassification(){
            this.loading = true;
            this.editMode = false;
            this.classification = {};
            $('#classificationModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#classificationModal').modal('hide');
        },
        deleteClassification(classification){
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
                    this.form.delete('/api/inventory/classifications/'+classification.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Notice has been deleted.', 'success');
                        this.$emit('reloadClassification');   
                    })
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/inventory/classifications?page='+page)
            .then(response =>{
                this.loading = false;
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Classifications loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Classifications not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.classifications = response.data.classifications;
            //this.classifications = response.data.classifications;
        },
        updateClassification(classification){
            this.loading = true;
            this.editMode = true;
            this.classification = classification;
            $('#classificationModal').modal('show');
            this.loading = false;
        },  
    },
}
</script>