<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="assetDetailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h3 class="modal-title">Asset Detail</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <EquipmentDetailAsset :asset.sync="asset" @refreshAssetDetail="refreshAssets" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assetFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h3 class="modal-title">Asset Form</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <EquipmentFormAsset :asset.sync="asset" :editMode.sync="editMode" @refreshAssetForm="refreshAssets" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Category</th>
                <th>Assigned To</th>
                <th>Location</th>
                <th>Purchase Amount</th>
                <th>Purchase Date</th>
                <th>Depreciation Rate (%)</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="assets.length > 0">
            <tr v-for="(asset, index) in assets" :key="asset.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ asset.name }}</td>
                <td>{{ asset.category != null ? asset.category.name : 'N/A' }}</td>
                <td>{{ asset.assignedUser != null ? FullName(asset.assignedUser) : 'N/A' }}</td>
                <td>{{ asset.branch ? asset.branch.name : 'No Branch Assigned'}}</td>
                <td>{{ currency(asset.purchase_value) }}</td>
                <td>{{ ExcelDate(asset.acquisition_date) }}</td>
                <td>{{ asset.depreciation_rate }} </td>
                <td>
                    <span v-if="asset.status == 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu" v-if="source == 'finance'">
                        <button class="btn btn-block dropdown-item" @click="viewAsset(asset)"><i class="fa fa-eye mr-1 text-primary"></i> View Asset </button>
                        <button class="btn btn-block dropdown-item" @click="editAsset(asset)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Asset </button>
                        <button class="btn btn-block dropdown-item" @click="deactivateAsset(asset.id)"><i class="fa fa-trash mr-1 text-danger"></i> {{asset.status == 1 ? 'Deactivate' : 'Reactivate'}} Asset </button>
                    </div>
                    <div class="dropdown-menu" v-if="source == 'equipments'">
                        <router-link :to="'equipments/assets/'+asset.id" class="btn btn-block dropdown-item"><i class="fa fa-eye mr-1 text-primary"></i> View Asset </router-link>
                        <button class="btn btn-block dropdown-item" @click="assignAsset(asset)"><i class="fa fa-edit mr-1 text-warning"></i> Assign Asset </button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="8">No Branch Account meets your requirement</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            asset: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshAssetList'],
    mounted() {},
    methods: {
        closeModal(){
            $('#assetDetailModal').modal('hide');  
            $('#assetFormModal').modal('hide');  
        },
        deactivateAsset(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Asset would be deactivated and not available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/equipments/assets/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Asset has been successfully deactivated', 'success');
                        this.$emit('refreshAssetList');             
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false;
                }
            });
        },
        editAsset(asset){
            this.loading = true;
            this.editMode = true;
            this.asset = asset;
            $('#assetFormModal').modal('show');
            this.loading = false;  
        },
        viewAsset(asset){
            this.asset = asset;
            $('#assetDetailModal').modal('show');
        },
        refreshAssets(){
            this.loading = true;
            this.closeModal();
            this.$emit('refreshAssetList');
            this.loading = false;            
        }
    },
    props:{
        source: String,
        assets: {type: Array, default: () => [],}
    }
}
</script>