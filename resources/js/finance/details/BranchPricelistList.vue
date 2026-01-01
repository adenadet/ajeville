<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="branchPriceListFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Pricelist Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormBranchPricelist :editMode.sync="editMode" :source.sync="source" :branch_price_list.sync="branch_price_list" @reloadBranchPriceList="refreshBranchPricelists()" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Price List Name</th>
                <th>Branch</th>
                <th v-if="source == 'emr'">Plan Name</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(branch_price_list, index) in branch_price_lists" :key="branch_price_list.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ branch_price_list.price_list != null ? branch_price_list.price_list.name : 'Invalid Price List' }}</td>
                <td>{{ branch_price_list.branch != null ? branch_price_list.branch.name : 'Unassigned  to  Branch'}}</td>
                <td v-if="source == 'emr'">{{ branch_price_list.plan != null ? branch_price_list.plan.name : 'Not Attached'}}</td>
                <td>{{ branch_price_list.status == 1 ? 'Active' : 'Deactivated'}}</td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'/settings/price_lists/'+branch_price_list.price_list_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-1"></i> View Price List</router-link>
                        <button class="btn btn-block dropdown-item" @click="editBranchPricelist(branch_price_list)"><i class="fas fa-edit mr-1 text-success"></i> Edit Branch Pricelist</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateBranchPricelist(branch_price_list.id)"><i class="fas fa-recycle mr-1 text-warning"></i> Deactivate/Reactivate</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            branch_price_list: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshBranchPricelists'],
    mounted() {},
    methods: {
        closeModal(){
            $('#branchPriceListModal').modal('hide');  
            $('#branchPriceListFormModal').modal('hide');  
        },
        deactivateBranchPricelist(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Branch PriceList would be deactivated and not available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/branch_price_lists/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, response.data.icon);
                        this.$emit('refreshBranchPricelists');             
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false;
                }
            });
        },
        editBranchPricelist(branch_price_list){
            this.loading = true;
            this.editMode = true;
            this.branch_price_list = branch_price_list;
            $('#branchPriceListFormModal').modal('show');
            this.loading = false;  
        },
        viewBranchPricelist(branch_price_list){
            this.branch_price_list = branch_price_list;
            $('#branchPriceListModal').modal('show');
        },
        refreshBranchPricelists(){
            this.closeModal();
            this.$emit('refreshBranchPricelists');            
        }
    },
    props:{
        source: String,
        branch_price_lists: {type: Array, default: () => [],}
    }
}
</script>