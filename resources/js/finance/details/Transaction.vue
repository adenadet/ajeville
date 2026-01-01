<template>
<section class="overlay-wrapper">
    <div class="overlay-dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <h3 class="text-primary"><i class="fas fa-file"></i> {{ transaction.unique_id }}</h3>
    
    <div class="text-muted" v-if="transaction.trans_type == 'credit'">
        <p class="text-sm">Transaction Type: <b class="d-block">{{ transaction.classification != null ? transaction.classification.name : 'Not Assisgned' }}</b></p>
        <p class="text-sm">Counterpart Name: 
            <b class="d-block" v-if="transaction.customer != null">{{ transaction.customer.name }}</b>
            <b class="d-block" v-if="transaction.vendor != null">{{ transaction.vendor.name }}</b>
            <b class="d-block" v-if="transaction.staff != null">{{ FullName(transaction.staff.user) }}</b>
        </p>
        <p class="text-sm">Project Leader: <b class="d-block">Tony Chicken</b></p>
    </div>
    <div class="text-muted" v-if="transaction.trans_type == 'debit'">
        <p class="text-lg">Type: <b class="d-block">{{firstUp(transaction.reference_type)}}</b></p>
        <p class="text-lg">Customer: <b class="d-block">{{(transaction.customer.name)}}</b></p>
    </div>
    <ul class="list-unstyled text-muted">
        <li>Amount: <b class="ml-1">{{currency(transaction.amount)}}</b></li>
        <li>Created By: <b class="ml-1">{{FullName(transaction.creator)}}</b></li>
        <li>Payment Status: <b class="ml-1">{{currency(transaction.amount)}}</b></li>
        <li>Status: <b class="ml-1">{{(transaction.status == 0 ? 'Unconfirmed' : (transaction.status == 10 ? 'Completed' : (transaction.status == 40 ? 'Confirmed' : (transaction.status == 70 ? 'Queried' : (transaction.status == 100 ? 'Rejected' : 'Unknown Status')))))}}</b></li>
    </ul>
</section>
</template>
<script>
export default {
    data() {
        return {
            counterparty: {},
            loading: false,

        }
    },
    mounted() {},
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
            //this.applicant = {};
            Fire.$emit('ApplicantDataFill', {});
            $('#applicantModal').modal('show');
            this.$Progress.finish();
        },
        addAppointment(){
            this.$Progress.start();
            this.editMode = false;
            this.appointment = {};
            Fire.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1) {
            axios.get('/api/finance/transactions?page='+page).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
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
    props: {
        transaction: Object,
    },
    watch:{
        transaction(){
            this.loading = true;
            this.counterparty = this.transaction.trans_type == 'debit' ? this.transaction.customer : this.transaction.vendor;

            this.loading = false;
        }
    }
}
</script>