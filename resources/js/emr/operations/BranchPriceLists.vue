<template>
    <section class="col-12">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Branch Price Lists</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 300px;">
                <FinanceDetailBranchPricelistList :branch_price_lists.sync="branch_price_lists.data" source="emr" />       
            </div>
            <div class="card-footer">
                <div class="col-12">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="branch_price_lists.per_page != null ? branch_price_lists.per_page : 52" :records="branch_price_lists.total != null ? branch_price_lists.total : 550" ></pagination>
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