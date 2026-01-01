<template>
<section class="container-fluid">
    <form @submit.prevent="createDisbursement()">
        <alert-error :form="loanDisbursementData"></alert-error> 
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                    <label>Loan Detail</label>
                    <input type="hidden" id="loan_id" name="loan_id"  v-model="loanDisbursementData.loan_id" required>
                    <div class="form-control">{{ account.user | FullName }} loan of {{account.amount | currency }} for {{ account.name }}</div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount"  v-model="loanDisbursementData.amount" required>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date"  v-model="loanDisbursementData.payment_date" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Comments</label>
                    <wysiwyg rows="5" v-model="loanDisbursementData.description"></wysiwyg>
                </div>
            </div>
        </div>
        <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
    </form>
</section>
</template>
<script>
export default {
    data(){
        return {
            actions: [],
            account: {},
            current: {},
            loan_types: [],
            loanDisbursementData: new Form({
                payment_date: '',
                amount: '',
                description: '',
                loan_id:'', 
                id: '',
            }),
        }
    },
    methods:{
        createDisbursement(){
            this.loanDisbursementData.loan_id = this.account.id;
            this.loanDisbursementData.post('/api/loans/disbursements')
            .then(response=>{
                Fire.$emit('refreshUndisbursed', response);
                Swal.fire({
                    icon: 'success',
                    title: 'Disbursement was created successfully',
                });
                this.loanDisbursementData.reset();
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
        updateDisbursement(){
            this.loanDisbursementData.put('/api/loans/disbursements/'+this.loanDisbursementData.id)
            .then(response=>{
                Fire.$emit('refreshUndisbursed', response);
                Swal.fire({
                    icon: 'success',
                    title: 'Disbursement was created successfully',
                });
                this.loanDisbursementData.reset();
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        }
    },
    mounted() {
        Fire.$on('LoanDisbursementDataFill', account=>{
            this.account =account;
            if (this.account.disbursement != null) {this.loanDisbursementData.fill(requirement);}
            else{this.loanDisbursementData.loan_id = account.id;}
        });     
    },
    props: {
        editMode: Boolean,
    },
}
</script>