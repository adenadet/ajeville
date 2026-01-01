<template>
<section class="col-md-12">
    <div class="modal fade" id="repaymentModal" tabindex="-1" aria-labelledby="repaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="repaymentModalLabel">{{ editMode ? 'Update' : 'Add' }} Payment</h5>
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"><i class="fa fa-times text-success"></i></button>
                </div>
                <div class="modal-body">
                    <LoanFormRepayment />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"><LoanDetailSummary /></div>
        <div class="col-md-12"><LoanDetailCPM v-if="source != 'mine'"/></div>
        <div class="col-md-12"><LoanDetailGuarantors :source="source"/></div>
        <div class="col-md-12"><LoanDetailFiles :source="source" :account="account" :files="account.files"/></div>
        <div class="col-md-12"><LoanDetailRepayments :source="source"/></div>
        <div class="col-md-12"><LoanDetailCheckList v-if="source != 'mine'"/></div>
        <div class="col-md-12"><LoanDetailCreditScore /></div>
        <div class="col-md-12"><LoanDetailConfirmations /> </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            account: {},
            editMode: false,
            form: new Form({}),
            loading: false,
            repayments: {},
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/accounts/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Account was loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Accounts was not loaded successfully',
                })
            });
        },
        makeRepayment(repayment){
            $('#repaymentModal').modal('show');
            this.loading = false;
        },
        reloadPage(response){
            this.account = response.data.account;
            this.repayments = response.data.repayments;
            this.loading = false;
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        account
    },
}
</script>