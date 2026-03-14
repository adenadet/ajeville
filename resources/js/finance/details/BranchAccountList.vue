<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="branchAccountFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Branch Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormBranchAccount :branch_account="branch_account" :editMode="editMode"  @refreshBranchAccount="refreshBranchAccounts" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th v-if="source != 'dashboard'">Branch</th>
                <th v-if="source != 'dashboard'">Account Name</th>
                <th v-if="source != 'dashboard'">Account Number</th>
                <th>Bank</th>
                <th>Balance</th>
                <th v-if="source != 'dashboard'">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="branch_accounts.length > 0">
            <tr v-for="(branch_account, index) in branch_accounts" :key="branch_account.id">
                <td>{{ addOne(index) }}</td>
                <td v-if="source != 'dashboard'">{{ branch_account.branch ? branch_account.branch.name : 'No Branch Assigned'}}</td>
                <td v-if="source != 'dashboard'">{{ branch_account.account_name }}</td>
                <td v-if="source != 'dashboard'">{{ branch_account.account_number }}</td>
                <td>{{ branch_account.bank ? branch_account.bank.bank_name : 'No Bank Assigned'}}</td>
                <td>{{ currency(branch_account.balance)}}</td>
                <td v-if="source != 'dashboard'">{{ branch_account.status == 1 ? 'Active' : 'Deactivated'}}</td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <button class="btn btn-block dropdown-item" @click="viewBranchAccount(branch_account)"><i class="fa fa-eye mr-1 text-primary"></i> View Account </button>
                        <button class="btn btn-block dropdown-item" @click="editBranchAccount(branch_account)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Account </button>
                        <button class="btn btn-block dropdown-item" @click="deactivateBranchAccount(branch_account.id)"><i class="fa fa-trash mr-1 text-danger"></i> {{branch_account.status == 1 ? 'Deactivate' : 'Reactivate'}} Account </button>
                    
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Branch Account meets your requirement</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import FinanceFormBranchAccount from '@/finance/forms/BranchAccount.vue';
export default {
    components:{
        FinanceFormBranchAccount,
    },
    data() {
        return {
            branch_account: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['refreshBranchAccounts'],
    mounted() {},
    methods: {
        closeModal(){
            $('#branchAccountModal').modal('hide');  
            $('#branchAccountFormModal').modal('hide');  
 
        },
        deactivateBranchAccount(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Branch Account would be deactivated and not available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/branch_accounts/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, response.data.icon);
                        this.$emit('refreshBranchAccounts');             
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false;
                }
            });
        },
        editBranchAccount(branch_account){
            this.loading = true;
            this.editMode = true;
            this.branch_account = branch_account;
            $('#branchAccountFormModal').modal('show');
            this.loading = false;  
        },
        viewBranchAccount(branch_account){
            this.branch_account = branch_account;
            $('#branchAccountModal').modal('show');
        },
        refreshBranchAccounts(){
            this.closeModal();
            this.$emit('refreshBranchAccounts');            
        }
    },
    props:{
        source: String,
        branch_accounts: {type: Array, default: () => [],}
    }
}
</script>