<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="uploadModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Modal</h4>
                        <button type="button" class="close" @click="closeModal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"><PharmacyFormDrugItemUpload /></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-sm-0">All Drug Items</h4>
                        <div class="card-tools">
                            <button @click="uploadDrugItems()" class="bg-info btn btn-xs"><i class="fa fa-upload mr-1"></i>Upload</button>
                            <button @click="updateDrugItems()" class="btn-default btn btn-xs"><i class="fa fa-save mr-1"></i>Save</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <vue-excel-editor v-model="DrugData.drug_items">
                                    <vue-excel-column field="item_name"        label="Item Name"       type="string" :readonly="!editMode"/>
                                    <vue-excel-column field="drug_name"        label="Drug Name"       type="select" :options="drugs"/>
                                    <vue-excel-column field="drug_form_name"   label="Drug Form"       type="select" :options="drug_forms" />
                                    <vue-excel-column field="status"           label="Status"          type="select" :options="['Inactive','Active']" />
                                </vue-excel-editor>
                            </div>
                        </div>
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
            DrugData: new Form({
                drug_items: [],
            }),
            drugs: [],
            drug_forms: [],
            editMode: false,
            loading: false,
            
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('ServiceFinderExtract', data => {
            this.loading = true;
            axios.put('/api/pharmacy/drugs')
            .then(response => {
                this.refresh(response);
                this.loading = false;
            })
        })
    },
    methods: {
        closeModal(){
            $('#uploadModal').modal('hide');
        },
        getAllInitials() {
            this.loading = true;
            this.$Progress.start();
            axios.get('/api/emr/pharmacy/drug_items')
            .then(response => {
                this.refresh(response);
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                this.loading = false;
                toast.fire({icon: 'error', title: 'Drug Items were not loaded successfully',});
            });
        },
        refresh(response) {
            this.DrugData.drug_items = response.data.drug_items;
            this.drugs = response.data.drugs;
            this.drug_forms = response.data.drug_forms;
        },
        uploadDrugItems(){
            Fire.$emit('drugItemFormReset');
            $('#uploadModal').modal('show');
        },
        updateDrugs() {
            this.$Progress.start();
            this.loading = true;
            this.DrugData.post('/api/emr/pharmacy/drugs/all')
            .then(response => {
                this.refresh(response);
                this.loading = false;
                Swal.fire({
                    icon: 'success',
                    title: 'The Price List '+ this.price_list.name+' has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
                this.$Progress.finish();
             })
            .close(() => {
                this.$Progress.fail();
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Price List was not updated successfully',
                })
            });
        },

    },
}
</script>