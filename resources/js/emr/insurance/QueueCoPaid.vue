<template>
    <section class="container-fluid">
        <div class="modal fade" id="authCodeModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Enter Authorization Code</h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body bg-white">
                        <InsuranceFormAuthCode :transactions="transaction_list"/>
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
                        <InsuranceFormAuthRequest :transactions="transaction_list"/>
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
                                        <i class="fas fa-file mr-2 text-info"></i> Cover Transactions
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-inbox mr-2 text-primary"></i> Request Authorization 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
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
                            <tbody>
                                <tr v-for="(transaction, index) in transactions.data">
                                    <td><input type="checkbox" :value="transaction.id" v-model="selected_transactions"></td>
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
                                            <button class="btn btn-block dropdown-item"><i class="fas fa-receipt mr-2 text-warning"></i> Modify Transaction</button>
                                            <button class="btn btn-block dropdown-item"><i class="fas fa-file mr-2 text-info"></i> Cover Transaction</button>
                                            <button class="btn btn-block dropdown-item" @click="requestCode(transaction)"><i class="fas fa-inbox mr-2 text-primary"></i> Request Authorization Code</button>
                                        </div>
                                    </td>
                                </tr>
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
        addPlan(){},
        closeModal(){
            $('#requestCodeModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/insurance/transactions?q=copaid&page='+page).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Transactions were not loaded successfully',});
            });
        },
        refresh(response){
            this.transactions = response.data.transactions;
            this.transaction = response.data.transactions.data[0];
        },
        requestCode(transaction){
            this.editMode = false;
            Fire.$emit('providerDataFill', {});
            $('#requestCodeModal').modal('show');
        },
        setCoverage(transaction){},
    },
}
</script>