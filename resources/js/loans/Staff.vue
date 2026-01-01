<template>
    <section class="col-md-12">
        <div class="modal fade" id="loanAssignModal" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-xl modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Assign Loan Officer</h6>
                        <button type="button" class="btn-default btn btn-sm" data-bs-dismiss="modal" aria-label="Close"
                            @click="closeModal()">
                            <i class="text-danger fa fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-3">
                        <LoanFormAssign :loan="loan" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card custom-card">
            <div class="card-header justify-content-between bg-dark">
                <div class="card-title">All Loans </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-0">
                    <table class="table text-nowrap table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Customer </th>
                                <th scope="col">Purpose/ GLC ID </th>
                                <th scope="col">Loan Account Number</th>
                                <th scope="col">Loan Account Officer</th>
                                <th scope="col">Loan Type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Created On</th>
                                <th scope="col">Duration</th>
                                <th scope="col">Status</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loans.data != null && loans.data.length != 0">
                            <tr v-for="loan in loans.data" :key="loan.id">
                                <th>{{ FullName(loan.user) }}</th>
                                <td>{{ loan.name }} <br /><span class="text-muted">{{ loan.unique_id }}</span></td>
                                <td>{{ loan.bank ? loan.bank.bank_name : '' }} <br /> {{ loan.acct_name }} ({{ loan.acct_number }})</td>
                                <td v-if="loan.account_officer != null">{{ FullName(loan.account_officer.staff)  }} </td>
                                <td v-else>Not Yet Assigned</td>
                                <td>{{ loan.type ? loan.type.name : 'Old Type' }}</td>
                                <td>{{ currency(loan.amount)  }}</td>
                                <td>{{ currency(loan.total_paid) }} / {{ currency(loan.payable) }}</td>
                                <td>{{ ExcelDate(loan.created_at) }}</td>
                                <td>{{ loan.duration }} {{ loan.frequency }} </td>
                                <td><span class="badge bg-primary">{{ loan.status == 1 ? 'Initiated' : (loan.status == 2 ? 'Requested' : (loan.status == 3 ? 'Awaiting Guarantors' : (loan.status == 4 ? 'Guaranteed' : (loan.status >= 5 && loan.status <= 14 ? 'Processing' : 'Ongoing')))) }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-default-light" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <router-link class="btn btn-block dropdown-item" :to="'/staff/loans/' + loan.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                                        <button class="btn btn-block dropdown-item" @click="assignLoan(loan)"><i class="fa fa-user-cog mr-1 text-success"></i> Assign Loan</button>
                                        <button v-if="loan.status>=14" class="btn btn-block dropdown-item" @click="closeLoan(loan.id)"><i class="fa fa-times mr-1 text-danger"></i> Liquidate Loan</button>
                                        <button  v-if="loan.status < 14" class="btn btn-block dropdown-item" @click="deleteLoan(loan.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Loan Request</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="10">There are no loan request currently, kindly create one<br />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="float-right">
                            <pagination v-model="current_page" @paginate="getInitials" :per-page="loans.per_page != null ? loans.per_page : 52" :records="loans.total != null ? loans.total : 550" ></pagination>
                        </tfoot>
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
            current_page: 1,
            editMode: false,
            loans: {},
            loan: {},
        }
    },
    created() {
        this.getInitials();
    },
    methods: {
        addNew() {
            this.loading = true;
            this.editMode = false;
            this.loan = {};
            $('#loanModal').modal('show');
            this.loading = false;
        },
        assignLoan(loan) {
            this.loading = true;
            this.loan = loan;
            $('#loanAssignModal').modal('show');
            this.loading = true;
        },
        closeLoan() {

        },
        closeModal() {
            $('#loanModal').modal('hide');
            $('#loanAssignModal').modal('hide');
        },
        deleteLoan(id) {
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
                if (result.value) {
                    this.form.delete('/api/loans/accounts/' + id)
                        .then(response => {
                            this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        })
                        .catch(() => {
                            this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                        });
                }
            });
        },
        getInitials(page=1) {
            axios.get('/api/loans/accounts/staffs?page='+page)
            .then(response => {
                this.reload(response);
                this.$toast.fire({ icon: 'success', title: 'Loan Accounts loaded successfully', });

            })
            .catch(() => {
                this.$toast.fire({ icon: 'error', title: 'Loan Accounts not loaded successfully', });
            });
        },
        reload(response){
            this.loans = response.data.accounts;
            this.closeModal();
            this.loading = false;
        }
    },
    props: {
        //'chat': Object,
    },
    watch:{

    }
}
</script>