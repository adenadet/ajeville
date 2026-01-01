<template>
<section>
    <div class="modal fade" id="assetModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h3 class="modal-title">Asset Form</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <EquipmentFormAsset :asset.sync="asset" :editMode.sync="editMode" @refreshAssetForm="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Assets</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 350px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default mr-1" @click="getInitials"><i class="fas fa-search"></i></button>
                                <select class="form-control form-control-sm ml-1" id="type" name="type" v-model="type" @change="getInitials">
                                    <option value="">-- Type --</option>
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <button type="button" class="btn btn-primary ml-1" @click="addAsset"><i class="fas fa-plus"></i></button>
                                <button type="button" class="btn btn-success ml-1" @click="downloadAssets"><i class="fas fa-download"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0 ovelay-wrapper" style="height: 500px;">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <EquipmentDetailAssetList :assets="assets.data" source="finance" @refreshAssetList="getInitials"/>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="assets.per_page != null ? assets.per_page : 52" :records="assets.total != null ? assets.total : 550" ></pagination>
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
            asset: {},
            assets: { data: [], total: 0,},
            current_page: 1,
            editMode: false,
            loading: false,
            query: '',
            type: 'active',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addAsset(){
            this.loading = true;
            this.editMode = false;
            this.asset = {};
            $('#assetModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#assetModal').modal('hide');
        },
        async downloadAssets(){
            this.loading = true;
            try {
                const response = await axios.get('/api/equipments/assets/report?page='+this.current_page+'&query='+this.query+'&type='+this.type, {responseType: 'blob',});
                const contentDisposition = response.headers['content-disposition'];
                const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'all_assets_list.csv';

                // Create a blob URL and force download
                const blob = new Blob([response.data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
            } 
            catch (error) {
                console.error('Download failed:', error);
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to generate report',
                });
            }
            this.loading = false;
        },
        getInitials(page = 1) {
            this.loading = true
            this.closeModal();
            axios.get('/api/equipments/assets?page='+this.current_page+'&query='+this.query+'&type='+this.type).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Assets did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.assets = response.data.assets;
        }
    },
}
</script>