<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Form</h4>
                    <button type="button text-white" class="close text-white" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormPayment :payment.sync="payment" :editMode.sync="editMode" @refreshPaymentForm="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title">Payments</h4>
            <div class="card-tools">
                <div class="input-group" style="width: 650px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                    <div class="input-group-append">
                        <input type="date" v-model="start_date"  class="form-control ml-1" placeholder="Start Date">
                        <input type="date" v-model="end_date"  class="form-control ml-1" placeholder="End Date">
                        <button type="button" class="btn btn-default" @click="getInitials"><i class="fas fa-search"></i></button>
                        <select  class="form-control ml-1" v-model="status"  @change="getInitials">
                            <option value="">All</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="unconfirmed">Unconfirmed</option>
                        </select>
                        <button type="button" class="btn btn-primary ml-1" @click="addPayment"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0 table-responsive overlay-wrapper" style="height:600px">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <FinanceDetailPaymentList :payments.sync="payments.data" @refreshPaymentList="getInitials" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="payments.per_page != null ? payments.per_page : 52" :records="payments.total != null ? payments.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
import FinanceDetailPaymentList from '@/finance/details/PaymentList.vue';
import FinanceFormPayment from '@/finance/forms/Payment.vue';
export default {
    components:{
        FinanceDetailPaymentList, FinanceFormPayment
    },
    data() {
        return {
            current_page: 1,
            editMode: false,
            end_date: '',
            loading: false,
            payment: {},
            payments: {data: [], total: 0, per_page: 20},
            query: '',
            start_date: '',
            status:'',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addPayment(){
            this.loading = true;
            this.editMode = false;
            this.payment = {};
            $('#paymentModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#paymentModal').modal('hide');
        },
        getInitials() {
            this.loading = true;
            this.closeModals();
            axios.get('/api/finance/payments?end_date='+this.end_date+'&page='+this.current_page+'&query='+this.query+'&status='+this.status+'&start_date='+this.start_date)
            .then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your payments did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.payments = response.data.payments;
        }
    },
}
</script>