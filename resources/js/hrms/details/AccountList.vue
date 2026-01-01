<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="hrItemFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">HR Items Detail</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <!--HrmsFormAssessmentAccount :account.sync="account" :editMode.sync="editMode" :user.sync="user" @reloadAccount="getAllInitials"/-->
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap table-striped table-hover">
        <thead>
            <tr>
                <th>Bank</th>
                <th>Account Name</th>
                <th>Account Number</th>
                <th>Primary Account</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody v-if="accounts.length > 0">
            <tr v-for="account in accounts">
                <td>{{ account.bank != null ? account.bank.bank_name : 'Deactivated Bank' }}</td>
                <td>{{ account.account_name }}</td>
                <td>{{ account.account_number }}</td>
                <td>
                    <span v-if="account.primary_account == 1"><i class="fa fa-star text-warning"></i></span>
                    <span v-else-if="account.primary_account == 0"></span>
                </td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View HR item</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateAccount(account)"><i class="fa fa-edit mr-1 text-warning"></i> Update HR Item</button>
                        <button v-if="account.status == 1" class="dropdown-item btn btn-block btn-sm" @click="deactivateHRItem(account.id)"><i class="fa fa-recycle mr-1 text-danger"></i> Deactivate Item</button>
                        <button v-if="account.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deactivateHRItem(account.id)"><i class="fa fa-recycle mr-1 text-success"></i> Reactivate Item</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Items meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            account: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['reloadAccountList'],
    methods:{
        closeModals(){
            $('#accountFormModal').modal('hide');
        },
        deleteAccount(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/accounts/'+id)
                    .then(response=>{
                        this. $swal.fire('Deleted!', 'Account has been activated', 'success');
                        this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        getAllInitials(){
            this.loading = true;
            this.$emit('reloadAccountList');
            this.closeModals();
            this.loading = false;
        },
        updateAccount(account){
            this.loading = true;
            this.editMode = true;
            this.account = account;
            $('#accountFormModal').modal('show');
            this.loading = false;
        },
        viewAccount(account){
            this.account = account;
            $('#accountModal').modal('show');
        },
    },
    mounted(){ },
    props:{
        accounts: Array,
    },
    watch:{}

}
</script>