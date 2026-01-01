<template>
<section class="container-fluid overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="branchPriceListFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Update' : 'Add'}} Branch Price List Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormBranchPricelist :editMode.sync="editMode" :branch_price_list.sync="branch_price_list" :main_branch.sync="branch_price_list.branch" @reloadBranchPriceList="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Branch Price List</h3>
                    <div class="card-tools">
                        <button @click="assignPriceList()" class="btn btn-success btn-sm ml-1 mb-3 mb-sm-0"><i class="fa fa-plus mr-1"></i> Add Branch Price List</button>
                    </div>
                </div>
                <div class="card-body table-responsive border rounded p-0">
                    <!--FinanceDetailBranchPricelistList :price_lists="branch_price_lists.data" @refreshPricelist="getInitials()"/-->
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>Price List</th>
                                <th>Branch</th>
                                <th>Plan</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody v-for="branch_price_list in branch_price_lists.data" :key="branch_price_list.id">
                            <tr>
                                <td>{{ branch_price_list.price_list != null ? branch_price_list.price_list.name : 'No Price List Assigned' }}</td>
                                <td>{{ branch_price_list.branch != null ? branch_price_list.branch.name : 'No Branch Assigned' }}</td>
                                <td>{{ branch_price_list.plan != null ? branch_price_list.plan.name : 'Cash'}}</td>
                                <td v-html="readMore(branch_price_list.description, 50, '..')" :title="branch_price_list.description"></td>
                                <td>{{ branch_price_list.status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <span class="nav-link" data-toggle="dropdown" href="#">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <router-link :to="'/settings/price_lists/'+branch_price_list.price_list_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-1"></i> View Price List</router-link>
                                        <button class="btn btn-block dropdown-item" type="button" @click="editBranchPricelist(branch_price_list)"><i class="fas fa-edit mr-1"></i> Update</button>
                                        <button class="btn btn-block dropdown-item" @click="deactivateBranchPricelist(branch_price_list)"><i class="fas fa-recycle mr-1"></i> Deactivate/Reactivate</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="branch_price_lists.per_page != null ? branch_price_lists.per_page : 52" :records="branch_price_lists.total != null ? branch_price_lists.total : 550" ></pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    computed:{
        current_branch(){
            var branch = this.$store.getters.currentBranch;
            return branch;
        },
    },
    data() {
        return {
            active_visits: 0,
            branch_price_list: { branch: {},},
            branch_price_lists: {}, 
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits: ['refreshBranchPricelists'],
    mounted() {
        this.$store.dispatch('getBranchCookie').then(() => {
            if (this.current_branch && this.current_branch.id) {
                this.getInitials();
            }
        });
    },
    methods: {
        assignPriceList(){
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
        getInitials(page = 1 ) {
            this.loading = true;
            this.closeModal();
            if (!this.current_branch || !this.current_branch.id) {
                return; // Or fetch the branch first
            }
            axios.get('/api/finance/branch_price_lists/?type=active&branch_id='+this.current_branch.id+'&page='+page).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.branch_price_lists= response.data.branch_price_lists;
        }
    },
    props: {},
    watch: {
        current_branch: {
            immediate: true,
            handler(newVal) {
                if (newVal && newVal.id) {
                    this.getInitials();
                }
            }
        }
    }
}
</script>