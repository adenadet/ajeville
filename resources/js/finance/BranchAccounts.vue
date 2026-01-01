<template>
<section class="overlay-wrapper p-0">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="branchAccountModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Bank Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormBranchAccount :branch_account="branch_account" :editMode="editMode"  @refreshBranchAccount="getInitials(1)" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title">Branch Accounts</h4>
            <div class="card-tools">
                <button class="btn btn-xs btn-primary" @click="addBranchAccount">Add New</button>
            </div>
        </div>
        <div class="card-body p-0" style="height:500px; overflow-y: auto;">
            <FinanceDetailBranchAccountList :branch_accounts="branch_accounts.data" @refreshBranchAccounts="getInitials(1)" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="branch_accounts.per_page != null ? branch_accounts.per_page : 52" :records="branch_accounts.total != null ? branch_accounts.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            branch_account: {},
            branch_accounts: { data: [],},
            current_page: 1,
            editMode: false,
            loading: false,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addBranchAccount(){
            this.loading = true;
            this.editMode = false;
            this.branch_account = {};
            $('#branchAccountModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#branchAccountModal').modal('hide');
        },
        getInitials(page = 1) {
            this.loading = true
            this.closeModal();
            axios.get('/api/finance/branch_accounts?page='+page+'&type=branch').then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Branch Accounts did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.branch_accounts = response.data.branch_accounts;
        }
    },
    props: {}
}
</script>