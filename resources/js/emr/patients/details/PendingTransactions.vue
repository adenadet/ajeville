<template>
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Pending Transactions</h3>
            <div class="card-tools"><button class="btn btn-sm bg-dark" :class="selected_transactions.length == 0 ? 'disabled' : ''" @click="sendForth()">Pay</button></div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover text-nowrap">
                <thead class="th-dark">
                    <tr>
                        <th>&nbsp;</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Service Name</th>
                        <th>Visit</th>
                        <th>Amount</th>
                        <th>Pending</th>
                        <th>Payment Status</th>
                        <th>Status</th>
                        <th>Booked</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="transactions != null && transactions.length != 0">
                    <tr v-for="transaction in transactions" :key="transaction.id">
                        <td><input type="checkbox" name="transactions[]" v-model="selected_transactions" :value="{'id': transaction.id, 'amount':transaction.item_total}"/></td>
                        <td>{{ transaction.date }}</td>
                        <td>{{ transaction.service_type.name }}</td>
                        <td>{{ transaction.item_name }}</td>
                        <td>{{ transaction.visit != null ? transaction.visit.unique_id : '' }}</td>
                        <td>{{ transaction.item_total | currency }}</td>
                        <td>{{ 0.00 | currency }}</td>
                        <td>{{ transaction.status }}</td>
                        <td>{{ transaction.service_status }}</td>
                        <td>{{ transaction.created_at | excelDate }}</td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2"></i> View Transaction</button>
                                <button class="btn btn-block dropdown-item"><i class="fas fa-cc mr-2"></i> Pay for Deposit</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr><td colspan="10">No Pending Transactions has been created yet. </td></tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            transactions: [],
            selected_transactions: [],
        }
    },
    mounted() {
        Fire.$on('getPatient', patient_id => {
            this.getInitials(patient_id);
        });
    },
    methods:{
        getInitials(id){
            axios.get('/api/finance/transactions/patients/'+id+'/pending')
            .then(response =>{
                this.transactions = response.data.transactions;
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
        },
        sendForth(){
            Fire.$emit('SetTransactions', this.selected_transactions);
        },
    },
    props:{
        source: String,
    }
}
</script>