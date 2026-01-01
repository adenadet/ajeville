<template>
    <section class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between bg-dark border-0 b-0">
                    <div class="card-title"> Loans </div>
                    <div class="card-tools">
                        <a href="/loans/new" class="btn btn-sm btn-primary"><i class="fa fa-plus mr-1"></i> Request New</a>
                    </div>
                </div>
                <div class="card-body p-0 border-0 b-0">
                    <div class="table-responsive p-0">
                        <table class="table table-hover table-striped" v-if="accounts != null && accounts.data != null && accounts.data.length != 0">
                            <thead class="bg-green">
                                <tr>
                                    <th scope="col">Loan Name</th>
                                    <th scope="col">Loan Type</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Paid</th>
                                    <th scope="col">Created On</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="account in accounts.data" :key="account.id">
                                    <td scope="row">{{ account.name }} <br /><span class="text-muted">{{ account.unique_id
                                    }}</span></td>
                                    <td>{{ account.type ? account.type.name : 'Old Type' }}</td>
                                    <td>{{ currency(account.amount) }}</td>
                                    <td class="text-warning">{{ currency(account.balance)}}</td>
                                    <td>{{ ExcelDate(account.created_at)  }}</td>
                                    <td>{{ account.duration }} weeeks</td>
                                    <td><span class="badge bg-outline-primary">{{ account.status < 3 ? 'Awaiting Guarantors' : (account.status > 16 ? 'Ongoing' : 'Processing') }}</span></td>
                                    <td>
                                        <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu">
                                            <router-link class="btn btn-block dropdown-item" :to="'/loans/'+account.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                                            <button v-if="account.status > 13" class="btn btn-block dropdown-item" @click="closeLoan(account.id)"><i class="fa fa-times mr-1 text-danger"></i> Liquidate Loan</button>
                                            <button v-else class="btn btn-block dropdown-item" @click="deleteLoan(account.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Loan Request</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else>
                            No Loan Has been created
                        </div>
                    </div>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            accounts: {},
            account: {},
            all_banks: [],
            loan_types: [],
            option_mode: '',
            initial_route: '',
        }
    },
    created() {
        this.getInitials();

    },
    methods: {
        addNew() {
            this.$Progress.start();
            this.editMode = false;
            this.loan = {};
            Fire.$emit('LoanDataFill', {});
            $('#loanModal').modal('show');
            this.$Progress.finish();
        },
        closeLoan() {

        },
        closeModal() { $('#loanModal').modal('hide'); },
        deleteLoan(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
                .then((result) => {
                    if (result.value) {
                        this.form.delete('/api/loans/accounts/' + id)
                        .then(response => {
                            Swal.fire('Deleted!', 'Loan Account has been deleted.', 'success');
                            Fire.$emit('CatRefresh', response);
                        })
                        .catch(() => {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                        });
                    }
                });
        },
        getInitials() {
            this.initial_route = '/api/loans/accounts/customer/' + this.$route.params.id;
            axios.get(this.initial_route).then(response => {
                this.reloadPage(response);
                toast.fire({ icon: 'success', title: 'Loan Accounts loaded successfully', });
            })
                .catch(() => {
                    this.$Progress.fail();
                    toast.fire({ icon: 'error', title: 'Loan Accounts not loaded successfully', });
                });
        },
        reloadPage(response) {
            this.accounts = response.data.accounts;
            this.all_banks = response.data.all_banks;
            this.loan_types = response.data.loan_types;
        },
    },
    props: {
        mode: String,
        user: Object,
    },
}
</script>