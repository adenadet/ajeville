<template>
    <div class="card custom-card border-0">
        <div class="modal fade" id="repaymentModal" tabindex="-1" aria-labelledby="repaymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="repaymentModalLabel">New Loan Repayment</h5>
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"><i class="fa fa-times text-success"></i></button>
                    </div>
                    <div class="modal-body">
                        <LoanFormRepayment :loan="account"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-header bg-dark border-0">
            <h3 class="card-title">Repayments </h3> 
        </div>
        <div class="card-body p-0 border-0"> 
            <div class="table-responsive">
                <table class="table text-nowrap">
                    <thead class="bg-green">
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Details</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody  v-if="repayments != null && repayments.length != 0">
                        <tr v-for="repayment in repayments" :key="repayment.id">
                            <td>{{ repayment.date | excelDate }}</td>
                            <td>{{ repayment.payment_mode_id }}</td>
                            <td>{{ repayment.description }}</td>
                            <td>{{ repayment.amount | currency }}</td>
                            <td v-if="repayment.status == 3"><span class="badge bg-outline-primary">Confirmed</span></td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr v-for="index in account.duration" :key="index">
                            <td><small>{{ account.frequency == 'weeks' ? 'Week' : 'Month'}} {{index}} </small></td>
                            <td><small>Not Decided Yet</small></td>
                            <td>CyberPay planned</td>
                            <td>{{account.emi | currency}}</td>
                            <td ><span class="badge bg-outline-primary">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
            editMode: false,
            form: new Form({}),
            repayments: {},
            repayment: {},
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/repayments/'+this.$route.params.id).then(response =>{
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
            //Fire.$emit('RepaymentDataFill', repayment);
            $('#repaymentModal').modal('show');
            this.loading = false;
        },
        reloadPage(response){
            this.account = response.data.account;
            this.repayments = response.data.repayments;
        }
    },
    mounted() {
        this.getAllInitials();
        /*Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('/api/ums/Policys/search?q='+query)
            .then((response ) => {
                this.Policys = response.data.Policys;
            })
            .catch(()=>{

            });
        });
        Fire.$on('Reload', response =>{
            $('#PolicyModal').modal('hide');
            this.Policys = response.data.Policys;
        });*/
    },
}
</script>