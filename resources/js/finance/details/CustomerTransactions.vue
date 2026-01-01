<template>
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Pending Transactions</h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button class="btn btn-sm btn-default" title="Print Transactions"><i class="fa fa-print"></i></button>
                    <button class="btn btn-sm btn-warning" title="Export Transactions"><i class="fa fa-file-pdf"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Service Name</th>
                        <th>Status</th>
                        <th>Booked</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="transaction in transactions" :key="transaction.id">
                        <td>{{ transaction.date }}</td>
                        <td>{{ transaction.date }}</td>
                        <td>{{ transaction.service_type.name }}</td>
                        <td>{{ transaction.item_name }}</td>
                        <td>{{ transaction.status }}</td>
                        <td>{{ transaction.created_at | excelDate }}</td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2"></i> View Transaction</button>
                                <button class="btn btn-block dropdown-item"><i class="fas fa-cc mr-2"></i> Pay for Deposit</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>    
        </div>
    </section>
</template>
<script>
export default {
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data() {
        return {
            active_visits: 0,
            invoices: {},
            patient: [],
            transactions: {},
            pending_invoices: {},
        }
    },
    mounted() {
        //this.getInitials();
        Fire.$on('refreshPatient', response => {
            this.refreshPending (response);
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