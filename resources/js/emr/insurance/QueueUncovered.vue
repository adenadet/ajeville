<template>
    <section class="container-fluid">
        <div class="modal fade" id="authCodeModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Enter Authorization Code</h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body bg-white">
                        <InsuranceFormAuthCode source="uncovered"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="requestCodeModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Input Authorization Code</h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body bg-white">
                        <InsuranceFormAuthRequest />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pending Transactions</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 450px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-inbox mr-1 text-primary"></i>Request Authorization 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;" v-if="loading">
                        <div class="overlay-wrapper">
                            <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" v-else>
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Visit</th>
                                    <th>Service/Item</th>
                                    <th>Rate</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="transactions.data != null && transactions.data.length != 0">
                                <tr v-for="(transaction, index) in transactions.data">
                                    <td>{{ index | addOne }}</td>
                                    <td>{{ transaction.date | excelDate }}</td>
                                    <td v-if="transaction.visit != null && transaction.visit.patient != null">{{transaction.visit.patient | patientName }}</td>
                                    <td v-else>Patient Deleted</td>
                                    <td>{{ transaction.visit != null ? transaction.visit.unique_id : 'Visit Not Specified' }}</td>
                                    <td>{{ transaction.item_name  }}</td>
                                    <td>{{ transaction.item_unit_cost | currency  }}</td>
                                    <td>{{ transaction.item_qty }}</td>
                                    <td>{{ transaction.item_total | currency  }}</td>
                                    <td>{{ transaction.status == 0 ? 'Unpaid' : (transaction.status == 1 ? 'Paid' :(transaction.status == 2 ? 'Cancelled' : 'Verified')) }}</td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <button class="btn btn-block dropdown-item" @click="viewSummary(transaction)"><i class="fas fa-eye mr-2 text-success"></i> View Summary</button>
                                            <button class="btn btn-block dropdown-item" @click="inputAuthCode(transaction)"><i class="fas fa-check mr-2 text-info"></i> Enter Auth Code</button>
                                            <button class="btn btn-block dropdown-item" @click="requestCode(transaction)"><i class="fas fa-inbox mr-2 text-primary"></i> Request Authorization Code</button>
                                            <button class="btn btn-block dropdown-item" @click="rejectTransaction(transaction)"><i class="fas fa-times mr-2 text-danger"></i> Reject Transaction</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody style="height: 300px;" v-else>
                                <tr><td colspan=11>No Transaction Yet</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: true,
            selected_transactions: [],
            transactions: {},
            transaction: {}, 
            transaction_list: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        enterAuthCode(){
            
            $('#authCodeModal').modal('show');
        },
        closeModal(){
            $('#authCodeModal').modal('hide');
            $('#requestCodeModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        getAllInitials(page=1){
            this.$Progress.start();
            axios.get('/api/emr/insurance/transactions?q=uncovered&page='+page).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        },
        inputAuthCode(transaction){
            Fire.$emit('updateTransactionList', [transaction]);
            $('#authCodeModal').modal('show');
        },
        inputAuthCodes(){
            if (this.selected_transactions.length == 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No items selected!',
                    footer: 'Please select at least one item!'
                });
            }
            else{
                Fire.$emit('updateTransactionList', selected_transactions);
                $('#authCodeModal').modal('show');
            }
        },
        refresh(response){
            this.transactions = response.data.transactions;
            this.transaction = response.data.transactions.data[0];
            this.loading = false;
        },
        rejectTransaction(transaction){},
        requestCode(transaction){
            this.editMode = false;
            Fire.$emit('providerDataFill', {});
            $('#requestCodeModal').modal('show');
        },
        setCoverage(transaction){},
    },
}
</script>