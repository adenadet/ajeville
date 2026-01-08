<template>
    <section>
        <div class="card card-widget widget-user-2" v-if="loading">
            <div class="overlay-wrapper">
                <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            </div>
        </div> 
        <div class="card card-widget widget-user-2" v-if="!loading">
            <div class="widget-user-header bg-warning">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" :src="patient != null && patient.user != null ? '/img/profile/'+patient.user.image : '/img/profile/default.png'" alt="User Avatar">
                </div>
                <h3 class="widget-user-username">{{ patient | patientName}}</h3>
                <h5 class="widget-user-desc">{{patient.insurances != null && patient.insurances.length != 0 ? 'Insurance' : 'Cash' }} </h5>
            </div>
            <div class="card-body table-responsive p-0">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payments</h3>
                    </div>
                    <div  class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in transaction.payments" :key="payment.id">
                                    <td>{{ payment.date }}</td>
                                    <td>{{ payment.amount | currency }}</td>
                                    <td>{{ payment.source == 1 ? 'Wallet' : '3rd party' }}</td>
                                    <td>{{ payment.status == 1 ? 'Confirmed' : 'Pending' }}</td>
                                    <td></td>
                                </tr>    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer p-0">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                        Requested By: <span class="float-right badge">{{  transaction.creator | fullName}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                        Service Type <span class="float-right badge">{{ transaction.service_type != null ? transaction.service_type.name : 'N/A' }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Payment Type <span class="float-right badge">{{ transaction.paid_by == 1 ? 'Cash' : (transaction.paid_by == 2 ? 'Credit' : 'Co-paid') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
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
            loading: true,
            transaction: {},
        }
    },
    mounted() {
        Fire.$on('viewTransaction', transaction => {
            this.loading = true;
            axios.get('/api/finance/transactions/'+transaction.id)
            .then(response => {
                this.refreshPage(response);    
            })

        });
    },
    methods: {
        refreshPage(response) {
            this.transaction = response.data.transaction;
            this.loading = false;
        },
    },
    props: {}
}
</script>