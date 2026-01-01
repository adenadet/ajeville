<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="priceListFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Branch Price List Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormBranchPricelist :editMode.sync="editMode" :branch_price_list.sync="branch_price_list" @refreshPriceListForm="refreshPriceLists" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <!--div class="card-header">
                <h3 class="card-title">Fixed Header Table</h3>

                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div-->
            <div class="card-body table-responsive p-0" style="height: 400px;">
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Branch </th>
                            <th>Plan</th>
                            <th>Price List</th>
                            <th><button class="btn btn-xs btn-primary" @click="createBranchPriceList"><i class="fa fa-plus mr-1"></i>Add</button></th>
                        </tr>
                    </thead>
                    <tbody v-if="branch_price_lists.data.length > 0">
                        <tr v-for="(bpl, index) in branch_price_lists.data" :key="bpl.id">
                            <td>{{addOne(index)}}</td>
                            <td>{{bpl.branch != null ? bpl.branch.name : 'No Branch Assigned' }}</td>
                            <td>{{bpl.plan != null ? bpl.plan.name : (bpl.plan_id == 0 ? 'Cash': 'Discontinued Plan/Cash') }}</td>
                            <td>{{bpl.price_list != null ? bpl.price_list.name : 'Discontinued Plan' }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="5">No Price List Plans available for this Price List</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            branch_price_list: {},
            branch_price_lists: {data: [], total: 0, per_page: 0, current_page: 0},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshBranchPriceList'],
    mounted() {},
    methods: {
        createBranchPriceList(){
            this.loading = true;
            this.editMode = false;
            this.branch_price_list = { branch: this.current_branch,};
            $('#branchPriceListFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#branchPriceListFormModal').modal('hide');
        },
        deactivateBranchPricelist(branch_price_list){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Branch Price list would be "+(branch_price_list.status == 1 ? "deactivated and not ": "reactivated and ")+" available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/branch_price_lists/'+branch_price_list.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.getInitials();            
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
        getPriceListPlans(){
            this.loading = true;
            axios.get('/api/finance/price_lists/'+this.price_list.id+'/plans')
            .then(response=>{
                this.branch_price_lists = response.data.branch_price_lists;
                this.loading = false;
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                this.loading = false;
            });
        },
        viewPriceList(asset){
            this.asset = asset;
            $('#branchPriceListModal').modal('show');
        },
        refreshPriceLists(){
            this.closeModal();
            this.$emit('refreshBranchPriceList');            
        }
    },
    props:{
        source: String,
        price_list: {type: Object, default: () => {},}
    },
    watch: {
        price_list(){
            this.getPriceListPlans();
        }   
    }
}
</script>