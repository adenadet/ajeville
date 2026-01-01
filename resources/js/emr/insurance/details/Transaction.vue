<template>
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Transaction Details</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="30%">Name:</td>
                                <td>{{ transaction.visit.patient | patientName }}</td>
                            </tr>
                            <tr>
                                <td width="30%">Service:</td>
                                <td>{{ transaction.item_name }}</td>
                            </tr>    
                            <tr><td width="30%">Amount:</td>
                                <td>{{ transaction.item_total | currency}}</td>
                            </tr>
                            <tr>
                                <td width="30%">Insurances:</td>
                                <td>
                                    <span v-for="insurance in transaction.visit.patient.insurances">{{ insurance.plan.name }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Payments/Coverage</th></tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in transaction.payments">
                                <td>{{payment.date}}</td>
                                <td>{{payment.source == 1 ? 'Customer Wallet' : 'Insurance Coverage'}}</td>
                                <td>{{payment.plan.name }} by {{ payment.plan.provider }}</td>
                                <td>{{payment.amount | currency }}</td>
                            </tr> 
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    
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
            transactions: {},
            transaction: {}, 
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
            axios.get('/api/emr/insurance/transactions').then(response =>{
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
        refresh(response){
            this.transactions = response.data.transactions;
        },
        requestCode(transaction){
            this.editMode = false;
            Fire.$emit('providerDataFill', {});
            $('#requestCodeModal').modal('show');
        },
        setCoverage(transaction){},
    },
    props:{
        transaction: Object,
    }
}
</script>