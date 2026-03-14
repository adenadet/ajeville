<template>
<section class="container-fluid">
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Transactions</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body p-0 table-responsive" style="height: 600px;">
            <FinanceDetailTransactionList :transactions.sync="transactions.data" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="transactions.per_page != null ? transactions.per_page : 52" :records="transactions.total != null ? transactions.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
import FinanceDetailTransactionList from '@/finance/details/TransactionList.vue';
export default {
    components: {
        FinanceDetailTransactionList
    },
    data() {
        return {
            current_page: 1,
            loading: false,
            transactions: { data: [], total:0},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addAppointment(){
            this.$Progress.start();
            this.editMode = false;
            this.appointment = {};
            Fire.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1) {
            this.loading = true;
            axios.get('/api/finance/transactions?page='+page).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Transactions did not load properly',
                })
            });
            this.loading = false;
        },
        makePayment(appointment){
            this.$Progress.start();
            this.paySpecific = true;
            Fire.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshPage(response) {
            this.transactions = response.data.transactions;
        }
    },
    props: {}
}
</script>