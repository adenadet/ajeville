<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
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
                <th></th>
            </tr>
        </thead>
        <tbody v-if="assets.length > 0">
            <tr v-for="(asset, index) in assets" :key="asset.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ asset.name }}</td>
                <td>{{ asset.category != null ? asset.category.name : 'N/A' }}</td>
                <td>{{ asset.assigned_to != null ? asset.category.name : 'N/A' }}</td>
                <td>{{ asset.branch ? asset.branch.name : 'No Branch Assigned'}}</td>
                <td>{{ ExcelDate(asset.purchase_date) }}</td>
                <td>{{ asset.depreciation }} </td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <button class="btn btn-block dropdown-item" @click="viewAsset(asset)"><i class="fa fa-eye mr-1 text-primary"></i> View Account </button>
                        <button class="btn btn-block dropdown-item" @click="editAsset(asset)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Account </button>
                        <button class="btn btn-block dropdown-item" @click="deactivateAsset(asset.id)"><i class="fa fa-trash mr-1 text-danger"></i> {{asset.status == 1 ? 'Deactivate' : 'Reactivate'}} Account </button>
                    
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
            $('#branchAccountModal').modal('hide');  
            $('#branchAccountFormModal').modal('hide');  
 
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
                    this.form.delete('/api/finance/assets/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, response.data.icon);
                        this.$emit('refreshAssets');             
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
            $('#branchAccountFormModal').modal('show');
            this.loading = false;  
        },
        viewAsset(asset){
            this.asset = asset;
            $('#branchAccountModal').modal('show');
        },
        refreshAssets(){
            this.closeModal();
            this.$emit('refreshAssets');            
        }
    },
    props:{
        source: String,
        assets: {type: Array, default: () => [],}
    }
}
</script>