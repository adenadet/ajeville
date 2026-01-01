<template>
<div class="card-body">
    <table class="table table-striped table-hover">
        <thead class="th-dark">
            <tr>
                <th scope="col" v-if="source != 'mine'">Customer Name</th>
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
            <tr v-for="account in accounts" :key="account.id">
                <td v-if="source != 'mine'">{{ FullName(account.user)  }}</td>
                <td>{{account.name}} <br /><span class="text-muted">{{ account.unique_id }}</span></td>
                <td>{{ account.type ? account.type.name : 'Old Type' }}</td>
                <td>{{ currency(account.amount) }}</td>
                <td class="text-warning">{{ currency(account.balance)}}</td>
                <td>{{ excelDate(account.created_at) }}</td>
                <td>{{ account.duration }} weeeks</td>
                <td><span class="badge bg-outline-primary">{{ account.status < 3 ? 'Awaiting Guarantors' : (account.status > 16 ? 'Ongoing' : 'Processing') }}</span></td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu" v-if="source != 'mine'">
                        <router-link class="btn btn-block dropdown-item" :to="'/staff/accounts/assigned/'+account.id"><i class="fa fa-eye mr-1 text-primary"></i> View Loan Account</router-link>
                        <router-link class="btn btn-block dropdown-item" :to="'/staff/confirm/loans/'+account.id"><i class="fa fa-check mr-1"></i> Confirmation</router-link>
                        <button v-show="account.cpm != null" class="btn btn-block dropdown-item" @click="updateCPM(account)"><i class="fa fa-file mr-1"></i> Update Proposal Memo</button>
                        <button v-show="account.cpm == null" class="btn btn-block dropdown-item" @click="createCPM(account)"><i class="fa fa-file mr-1"></i> Create Proposal Memo</button>
                        <router-link class="btn btn-block dropdown-item" :to="'/staff/customers/'+account.user_id"><i class="fa fa-user mr-1 text-success"></i> View Customer</router-link>
                        <button v-if="account.status > 13" class="btn btn-block dropdown-item" @click="closeLoan()"><i class="fa fa-times mr-1 text-danger"></i> Close Loan</button>
                        <button v-else class="btn btn-block dropdown-item" @click="deleteLoan(1)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Loan Request</button>
                    </div>
                    <div class="dropdown-menu" v-else>
                        <router-link class="btn btn-block dropdown-item" :to="'/loans/'+account.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                        <button v-if="account.status < 5" class="btn btn-block dropdown-item" @click="addGuarantors(account)"><i class="fa fa-user-friends mr-1 text-primary"></i> Add Guarantor </button>
                        <button v-if="account.status < 5" class="btn btn-block dropdown-item" @click="addFiles('', account.id)"><i class="fa fa-copy mr-1"></i> Add Files </button>
                        <button v-if="account.status > 13" class="btn btn-block dropdown-item" @click="closeLoan()"><i class="fa fa-times mr-1 text-danger"></i> Liquidate Loan</button>
                        <button v-else class="btn btn-block dropdown-item" @click="deleteLoan(account.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Loan Request</button>
                    </div>
                </td>
            </tr> 
        </tbody>
    </table>
</div>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            accounts: {},
            account: {},
            all_banks: [],
            loan_types: [],
            option_mode: '',
            initial_route: '',
        }
    },
    mounted() {
        this.getInitials();
        this.$on('refreshCPM', response => {this.getInitials();});
    },
    methods:{
        addNew(){
            console.log("Working");
            this.$Progress.start();
            this.editMode = false;
            this.loan = {};
            Fire.$emit('LoanDataFill', {});
            $('#loanModal').modal('show');
            this.$Progress.finish();
        },
        closeLoan(){

        },
        closeModal(){
            $('#loanModal').modal('hide');
            $('#loanCPMModal').modal('hide');
        },
        createCPM(account){
            this.account = account;
            this.editMode = false;
            this.$emit('hidePreCPM', account.cpm);
            $('#loanCPMModal').modal('show');
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
                if(result.value){
                    this.form.delete('/api/loans/account_officers/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Loan Account has been deleted.', 'success');
                        this.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            axios.get('/api/loans/account_officers?page='+page).then(response =>{
                this.reloadPage(response);
                this.closeModal();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Assigned Loan Accounts not loaded successfully',});
            });
        },
        reloadPage(response){
            this.accounts = response.data.accounts;
            this.all_banks = response.data.all_banks;
            this.loan_types = response.data.loan_types;
        },
        updateCPM(account){
            this.editMode = true;
            this.account = account; 
            this.$emit('hidePreCPM', account.cpm);
            $('#loanCPMModal').modal('show');   
        },
    },
    props:{
        accounts: Array,
        mode: String,
        user: Object,
    },
}
</script>