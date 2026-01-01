<template>
<div class="col-md-12">
    <div class="modal fade" id="loanModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">{{ editMode ? 'Edit Loan Request: '+ loan.name : 'Create New Loan Request'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LoanForm :editMode="editMode" :loan.sync="account" @getGuarantors="getGuarantors" @refreshLoan="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="GuarantorModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <h4 class="modal-title">{{ editMode ? 'Edit Guarantor Request: '+ loan.name : 'Create New Guarantor Request'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <GuarantorFormRequest :editMode.sync="editMode" :guarantor.sync="guarantor" :loan.sync="account"/>
                </div>
            </div>
        </div>
    </div>
    <!--div class="modal fade" id="fileModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Statement Modal</h4>
                    <button type="button" class="close text-primary" data-dismiss="modal" @click="closeModal()" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <LoanFormFile :editMode="editMode" :type.sync="file_type"/>
                </div>
            </div>
        </div>
    </div-->
    <div class="overlay-wrapper">
        <div class="overlay" v-if="loading">
            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        </div>
        <div class="card custom-card"> 
            <div class="card-header bg-dark border-0"> 
                <div class="card-title">Loans </div> 
                <div class="card-tools"> 
                    <button class="btn btn-xs btn-primary" @click="addNew()"><i class="fa fa-plus mr-1"></i> Request New</button> 
                </div> 
            </div> 
            <div class="card-body p-0 border-0"> 
                <div class="table-responsive"> 
                    <table class="table table-striped table-hover" v-if="accounts != null && accounts.data != null && accounts.data.length != 0 "> 
                        <thead class="bg-green">
                            <tr>
                                <th scope="col">Loan Name</th>
                                <th scope="col">Loan Type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Created On</th>
                                <th scope="col">Duration</th>
                                <th scope="col">Status</th>
                                <th scope="col"></th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <tr v-for="account in accounts.data" :key="account.id">
                                <th scope="row">{{account.name}} <br /><span class="text-muted">{{ account.unique_id }}</span></th>
                                <td>{{ account.type ? account.type.name : 'Old Type' }}</td>
                                <td>{{ currency(account.amount)}}</td>
                                <td class="text-warning">{{ currency(account.balance) }}</td>
                                <td>{{ ExcelDate(account.created_at)  }}</td>
                                <td>{{ account.duration }} {{ account.frequency }}</td>
                                <td><span class="badge bg-outline-primary">{{ account.status < 3 ? 'Awaiting Guarantors' : (account.status > 16 ? 'Ongoing' : 'Processing') }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <router-link class="btn btn-block dropdown-item" :to="'/loans/'+account.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                                        <button v-if="account.status < 5" class="btn btn-block dropdown-item" @click="addGuarantors(account)"><i class="fa fa-user-friends mr-1 text-primary"></i> Add Guarantor </button>
                                        <button v-if="account.status < 5" class="btn btn-block dropdown-item" @click="addFiles('', account.id)"><i class="fa fa-copy mr-1"></i> Add Files </button>
                                        <button v-if="account.status > 13" class="btn btn-block dropdown-item" @click="closeLoan()"><i class="fa fa-times mr-1 text-danger"></i> Liquidate Loan</button>
                                        <button v-else class="btn btn-block dropdown-item" @click="deleteLoan(account.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Loan Request</button>
                                    </div>
                                </td>
                            </tr> 
                        </tbody>
                        <tfoot class="pl-5 pr-5">
                            <pagination v-model="current_page" @paginate="getInitials" :per-page="accounts.per_page != null ? accounts.per_page : 52" :records="accounts.total != null ? accounts.total : 550" ></pagination>
                        </tfoot>
                    </table>
                    <div v-else>
                        No Loan Has been created
                    </div>
                </div> 
            </div>    
        </div>
    </div> 
</div>
</template>
<script>
import Form from 'vform';
export default {
    data(){
        return  {
            accounts: {},
            account: {},
            all_banks: [],
            continue_to: '',
            current_page: 1, 
            editMode: false,
            file_type: '',
            form: new Form({}),
            guarantor: {},
            loading: true,
            loan: {},
            loan_types: [],
            option_mode: '',
            initial_route: '',
        }
    },
    created() {
        this.getInitials();
        /*Fire.$on('reloadLoans', response =>{
            this.reloadPage(response);
            this.closeModal();
        });
        Fire.$on('reload', () =>{this.closeModal(); this.getInitials(); });
        Fire.$on('getGuarantors', response => {
            this.closeModal();
            this.account = response.data.current_loan;
            this.continue_to = "AccountStatement";
            this.addGuarantors(response.data.current_loan);
        });
        Fire.$emit('addAccountStatement', id => {
            this.closeModal();
            this.continue_to = "";
            this.addFiles("account_statement", id);
        })*/
    },
    methods:{
        addFiles(type = null , id){
            this.$Progress.start();
            this.file_type = type,
            Fire.$emit('FileDataFill', id);
            $('#fileModal').modal('show');
            this.$Progress.finish();
        },
        addGuarantors(account){
            this.loading = true;
            this.account = account;
            $('#GuarantorModal').modal('show');
            this.loading = true;
        },
        addNew(){
            this.loading = true;
            this.editMode = false;
            this.loan = {};
            this.$emit('LoanDataFill', {});
            $('#loanModal').modal('show');
            this.loading = false;
        },
        assignLoan(loan){

        },
        closeLoan(){

        },
        closeModal(){
            $('#collateralModal').modal('hide');
            $('#fileModal').modal('hide');
            $('#GuarantorModal').modal('hide');
            $('#loanModal').modal('hide');
            $('#statementModal').modal('hide');
        },
        deleteLoan(id){
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
                    this.form.delete('/api/loans/accounts/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Loan Account has been deleted.', 'success'); this.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getGuarantors(response){
            this.closeModal();
            this.account = response.data.current_loan;
            this.continue_to = "AccountStatement";
            this.addGuarantors(response.data.current_loan);
        },
        getInitials(page=1){
            this.loading = true;
            axios.get('/api/loans/accounts?page='+page).then(response =>{
                this.closeModal();
                this.reloadPage(response);
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Loan Accounts not loaded successfully',});
            });
        },
        reloadPage(response){
            this.closeModal();
            this.accounts = response.data.accounts;
            this.all_banks = response.data.all_banks;
            this.loan_types = response.data.loan_types;
            this.loading = false;
        },
    },
    props:{
        mode: String,
        user: Object,
    },
}
</script>