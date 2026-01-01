<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ArchiveFormCategory :editMode="editMode" :category.sync="category" @categoryReload="reloadCategory"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <ArchiveFormDocumentSearch />
            <div class="card">
                <div class="card-header bg-navy">
                    <h3 class="card-title">All Documents</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" @click="searchDocument()"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-primary" @click="addDocument()"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <ArchiveDetailDocumentList :documents="documents.data" source="main" actionable="all" />
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
            documents: {data: []},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addDocument(){
            this.loading = true;
            this.editMode = false;
            this.document = {};
            $('#documentModal').modal('show');
            this.loading = false; 
        },
        closeModal(){
            $('#documentFormModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/archives/documents').then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Document was not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.documents = response.data.documents;
        },
        searchDocument(){},
    },
}
</script>