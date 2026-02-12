<template>
<section>
    <div class="row">
        <div class="col-md-4">
            <EMRPatientFormSearch :no_add="true" @getPatient="getPatient"/>
            <div class="card mt-3" v-if="visit != null">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="lead"><b>{{ patientName(visit.patient) }}</b></h2>

                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small">
                                    <span class="fa-li"><i class="fas fa-building"></i></span>
                                    Address: {{ patientAddress(visit.patient) }}
                                </li>
                                <li class="small">
                                    <span class="fa-li"><i class="fas fa-phone"></i></span>
                                    Phone #: {{ visit.patient?.user?.phone || '-' }}
                                </li>
                                <li class="small">
                                    <span class="fa-li"><i class="fas fa-money-bill"></i></span>
                                    Balance: {{ currency(visit.patient.balance) }}
                                </li>
                                <li class="small">
                                    <span class="fa-li"><i class="fas fa-stopwatch"></i></span>
                                    Started: {{ visit.start_timestamp }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab" data-toggle="pill" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Pending Transactions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="home-tab" data-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true">Current Visit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="messages-tab" data-toggle="pill" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                            <!--EMRVisitDetailTransactions source="finance" :patient_id.sync="patient_id"/>    
                        </div>
                        <div class="tab-pane fade show active" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                            <EMRFinanceDetailPatientPendingTransactions :patient_id.sync="patient_id" :pending_transactions="pending_transactions"/>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                            <EMRFinanceDetailPatientTransactions :patient_transaction.sync="patient_id"/-->
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
data() {
        return {
            loading: false,
            patient: {},
            patient_id: null,
            pending_transactions: [],
            visit: null,
            visit_transactions: [],
        };
    },

    methods: {
        getPatient(response){
            this.patient = response.data.patient;
            this.pending_transactions = response.data.pending_transactions;
            this.transactions = response.data.transactions;
            this.visit = response.data.visit;
        },
        handlePatientSelected(patient_id) {
            this.patient_id = patient_id;

            this.pending_transactions = [];
            this.visit = null;
            this.visitTransactions = [];

            this.getPendingTransactions();
            this.getActiveVisit();
        },

        getPendingTransactions() {
            axios
                .get(`/api/emr/finance/transactions/patients/${this.patient_id}/pending`)
                .then(res => {
                    this.pending_transactions = res.data.transactions || [];
                })
                .catch(() => {
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Failed to load pending transactions',
                    });
                });
        },

        getActiveVisit() {
            axios.get(`/api/emr/visit/patient/${this.patient_id}`)
            .then(res => {
                this.visit = res.data.visit || null;

                if (this.visit) {
                    this.getVisitTransactions(this.visit.id);
                }
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to load active visit',
                });
            });
        },

        getVisitTransactions(visit_id) {
            axios.get(`/api/emr/finance/visit_transactions/${visit_id}`)
            .then(res => {
                this.visit_transactions = res.data.transactions || [];
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to load visit transactions',
                });
            });
        },
    },
};
</script>