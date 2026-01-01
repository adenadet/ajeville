<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="invoice p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-globe"></i> AdminLTE, Inc.
                    <small class="float-right">Date: 2/10/2014</small>
                </h4>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-6 invoice-col">
                Customer Details:
                <address v-if="statement.customer != null">
                    <strong>{{ statement.customer.name }}</strong><br>
                    Phone: {{statement.customer.phone }}<br>
                    Email: {{statement.customer.email }}
                </address>
            
                <address v-else>
                    Loading...
                </address>
            </div>
                <!-- /.col -->
            <div class="col-sm-5 invoice-col">
                <b>Statement</b><br>
                <br>
                <b>Start Date:</b> {{ ExcelDate(statement.start_date) }}<br>
                <b>End Date:</b> {{ ExcelDate(statement.end_date) }}<br>
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive p-0">
                <table class="table table-bordered table-striped">
                    <thead class="bg-dark">
                        <tr>
                            <th>Date</th>
                            <th>Code</th>
                            <th>Reference</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(tx, index) in transactions" :key="index">
                            <td>{{ ExcelDate(tx.date) }}</td>
                            <td>{{ tx.unique_id }}</td>
                            <td>{{ tx.reference_type }} #{{ tx.reference_id }}</td>
                            <td>{{ trans_type == 'debit' ? tx.amount : 0.00}}</td>
                            <td>{{ trans_type == 'credit' ? tx.amount : 0.00}}</td>
                            <td>{{ tx.running_balance}}</td>
                        </tr>
                    </tbody>
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
            loading: false,
            transactions: [],
            /*statement: {
                customer: {},
                opening_balance: 0,
                closing_balance: 0,
                transactions: []
            }*/
        }
    },
    mounted() {},
    methods:{
        async resetReport(){
            await axios.get('/api/finance/reports/'+transaction.id)
        }
    },
    props:{
        statement: Object, 
    },
    watch:{
        statement(){
            this.loading = true;
            this.transactions = this.statement.transactions;
            this.loading = false;
        }
    }
}
</script>