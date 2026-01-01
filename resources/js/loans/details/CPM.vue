<template>
<section>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Credit Proposal Memorandum Details</h3>
        </div>
        <div class="card-body">
            <div v-if="!editMode" v-html="cpm.detail" style="max-height: 500px; overflow:scroll;"></div>
            <LoanFormCPM :editMode="editMode" :account="account" v-else-if="source == 'account'"/>
            <div class="row" v-else>
                <div class="col-md-12 text-center">
                    <h3>Credit Proposal Memorandum not created</h3>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default{
    created(){
        this.getInitials();
    },
    data () {
        return {
            account: {},
            cpm: '',
            editMode: false,
        };
    },
    methods: {
        createCheckList(){
            this.$Progress.start();
            this.loanCheckData.loan_id = this.account.id;
            this.loanCheckData.post('/api/loans/cpm_templates')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshRepayment', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            this.$Progress.fail();
            });
        },
        getInitials(){
            axios.get('/api/loans/cpms/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Accounts was not loaded successfully',
                })
            });
        },
        reloadPage(response){
            this.cpm = response.data.cpm;
            this.editMode = (this.cpm == null || this.cpm.id == null) ? true : false;
            this.account = response.data.account;
        },
       
    },
    props:{
        source: String
    }
}
</script>