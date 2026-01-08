<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12"><PatientFormSearch source="cash_register"/></div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <PatientDetailPendingTransactions :editMode="editMode" />
            </div>
            <div class="col-md-6"><FinanceFormDeposit /></div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            active_visits: 0,
            editMode: false,
            patient: [],
            pending_invoices: {},
            transactions: {},
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshAppointment', response => {
            this.refreshAppointments(response);
        });
        Fire.$on('refreshPayment', response => {
            this.refreshAppointments(response);
            $('#paymentModal').modal('hide');
        });
    },
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
    props: {}
}
</script>