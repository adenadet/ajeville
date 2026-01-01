<template>
<div class="card-body table-responsive p-0" style="height:500px;">
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
        <tbody v-if="transactions.data != null && transactions.data.length != 0" class="overlay-wrapper">
            <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <tr v-for="(transaction, index) in transactions">
                <td><input type="checkbox" :value="transaction" v-model="selected_transactions"></td>
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
                        <button class="btn btn-block dropdown-item"><i class="fas fa-check mr-2 text-info"></i> Enter Auth Code</button>
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
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: true,
            selected_transactions: [],
            //transactions: {},
            transaction: {}, 
            transaction_list: [],
        }
    },
    mounted() {
        //this.getAllInitials();
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
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/insurance/transactions?q=auth').then(response =>{
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
        inputAuthCode(){

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
                this.transaction_list = this.selected_transactions;
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
    props:{
        transactions: Array,
        view: String,
    },
}
</script>