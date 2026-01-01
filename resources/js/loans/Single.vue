<template>
<section class="col-md-12">
    <div class="modal fade" id="repaymentModal" tabindex="-1" aria-labelledby="repaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="repaymentModalLabel">Modal title</h5>
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"><i class="fa fa-times text-success"></i></button>
                </div>
                <div class="modal-body">
                    <LoanFormRepayment />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">   
            <LoanDetailSummary />
        </div>
        <div class="col-md-6">
            <LoanDetailGuarantors source="Customer"/>
        </div>
        <div class="col-md-6">
            <LoanDetailFiles source="Customer" :account="account" :files="account.files"/>
        </div>
        <div class="col-md-12">
            <LoanDetailRepayments />
        </div>
    </div>
</section>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
    data(){
        return {
            account: {},
            repayments: {},
            editMode: false,
            form: new Form({}),
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/accounts/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                toast.fire({
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
            Fire.$emit('RepaymentDataFill', repayment);
            $('#repaymentModal').modal('show');
            this.$Progress.finish();
        },
        getPolicy(page=1){
            axios.get('/api/policies/all/departmental?page='+page)
            .then(response=>{
                this.Policys = response.data.Policys;   
            });
        },
        reloadPage(response){
            this.account = response.data.account;
            this.repayments = response.data.repayments;
            this.loading = false;
        }
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>