<template>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Loan Summary</h3>
            <button type="button" v-if="source == 'staff'" class="card-tools btn-sm btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
            <div class="dropdown-menu" v-if="source == 'staff'">
                <router-link class="btn btn-block dropdown-item" :to="'/staff/customers/'+account.user_id"><i class="fa fa-user mr-1 text-primary"></i> View Customer </router-link>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered table-hover table-stripped">
                <tbody>
                    <tr v-if="source == 'staff'">
                        <td >Loan Obligor</td>
                        <td colspan="5"><strong>{{ FullName(account.user) }}</strong></td>
                    </tr>
                    <tr>
                        <td >Loan Name</td>
                        <td colspan="3"><strong>{{ account.name }} [{{ account.unique_id }}]</strong></td>
                        <td>Loan Type</td>
                        <td><strong>{{ account.type ? account.type.name : 'Old Type' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Request Date</td>
                        <td><strong>{{ ExcelDate(account.request_date)}}</strong></td>
                        <td>Duration</td>
                        <td><strong>{{ account.duration}} {{ account.frequency}}</strong></td>
                        <td>Approved On</td>
                        <td><strong>{{ ExcelDate(account.approved_date) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Start Date</td>
                        <td colspan="2">
                            <strong v-if="account.payout_date != null">{{ExcelDate(account.payout_date)}}</strong>
                            <strong v-else>Not Yet Started</strong>
                        </td>
                        <td>Guaranteed On</td>
                        <td colspan="2">
                            <strong v-if="account.guaranteed_date != null">{{  ExcelDate(account.guaranteed_date) }}</strong>
                            <strong v-else>Not Yet Guaranteed</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td><strong>{{ currency(account.amount) }}</strong></td>
                        <td>Estimated {{ account.frequency == 'weeks' ? 'Weekly' : 'Monthly' }} Installment</td>
                        <td><strong>{{ currency(account.emi) }}</strong></td>
                        <td>Payment Summary</td>
                        <td><strong>{{ currency(account.balance)}} / {{ currency(account.payable)}}</strong></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><strong>{{ account.status < 4 ? 'Filing' : (account.status == 4 ? 'Awaiting Guarantors' : (account.status > 4 && account.status <= 13 ? 'Processing' : (account.status == 14 ? 'Ongoing' : 'Closed')))}}</strong></td>
                        <td>Closed Date</td>
                        <td><strong>{{ (account.status == 14 ? ExcelDate(account.updated_at) : 'N/A') }}</strong></td>
                        <td>Account Officer</td>
                        <td>
                            <strong v-if="account.account_officer != null">{{ FullName(account.account_officer.staff)}}</strong>
                            <strong v-else>Not Yet Assigned</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
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
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/accounts/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                this.loading = false;
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
        reloadPage(response){
            this.account = response.data.account;
            //this.repayments = response.data.repayments;
        }
    },
    mounted() {
        this.getAllInitials();
    },
    props: {
        source: String,
    }
}
</script>