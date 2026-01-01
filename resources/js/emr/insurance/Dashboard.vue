<template>
<section class="container-fluid overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ unconfirmed_transactions != null && unconfirmed_transactions.total != null ? unconfirmed_transactions.total : 0 }}</h3><p>Unsettled Transactions</p></div>
                <div class="icon"><i class="fa fa-tags"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ active_visits }}</h3><p>Active Visits</p></div>
                <div class="icon"><i class="fa fa-house-user"></i></div>
                <router-link to="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{ suspended_plans }}</h3><p>Suspended Plans</p></div>
                <div class="icon"><i class="fa fa-list"></i></div>
                <router-link to="/insurance/providers/suspended" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{suspended_providers}}</h3><p>Suspended Providers</p></div>
                <div class="icon"><i class="fas fa-university"></i></div>
                <router-link to="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Transactions Awaiting PA</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Visit</th>
                                <th>Service/Item</th>
                                <th>Rate</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody v-if="unconfirmed_transactions.data != null && unconfirmed_transactions.data.length != 0">
                            <tr v-for="(transaction, index) in unconfirmed_transactions.data">
                                <td>{{ index | addOne }}</td>
                                <td>{{ transaction.date | excelDate }}</td>
                                <td v-if="transaction.visit != null && transaction.visit.patient != null">{{transaction.visit.patient | patientName }}</td>
                                <td v-else>Patient Deleted</td>
                                <td>{{ transaction.visit != null ? transaction.visit.unique_id : 'Visit Not Specified' }}</td>
                                <td>{{ transaction.item_name  }}</td>
                                <td>{{ transaction.item_unit_cost | currency  }}</td>
                                <td>{{ transaction.item_qty }}</td>
                                <td>{{ transaction.item_total | currency  }}</td>
                                <td>{{ transaction.status == 0 ? 'Unpaid' : (transaction.status == 1 ? 'Paid' :(transaction.status == 2 ? 'Cancelled' : 'Verified')) }}</td>
                            </tr>
                        </tbody>
                        <tbody style="height: 300px;" v-else>
                            <tr><td colspan=11>No Transaction Yet</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <router-link to="/insurance/queue/authorizations" class="btn btn-sm bg-dark m-0 float-right">See All </router-link>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Uncovered Transactions</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Visit</th>
                                <th>Service/Item</th>
                                <th>Rate</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody v-if="uncovered_transactions.data != null && uncovered_transactions.data.length != 0">
                            <tr v-for="(transaction, index) in uncovered_transactions.data">
                                <td>{{ index | addOne }}</td>
                                <td>{{ transaction.date | excelDate }}</td>
                                <td v-if="transaction.visit != null && transaction.visit.patient != null">{{transaction.visit.patient | patientName }}</td>
                                <td v-else>Patient Deleted</td>
                                <td>{{ transaction.visit != null ? transaction.visit.unique_id : 'Visit Not Specified' }}</td>
                                <td>{{ transaction.item_name  }}</td>
                                <td>{{ transaction.item_unit_cost | currency  }}</td>
                                <td>{{ transaction.item_qty }}</td>
                                <td>{{ transaction.item_total | currency  }}</td>
                                <td>{{ transaction.status == 0 ? 'Unpaid' : (transaction.status == 1 ? 'Paid' :(transaction.status == 2 ? 'Cancelled' : 'Verified')) }}</td>
                            </tr>
                        </tbody>
                        <tbody style="height: 300px;" v-else>
                            <tr><td colspan=11>No Transaction Yet</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <router-link to="/insurance/queue/uncovered" class="btn btn-sm bg-dark m-0 float-right">See All </router-link>
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
            active_visits: 0,
            categories: [],
            contacts: [],
            editMode: false,
            loading: false,
            provider: {},
            provider_types: [],
            plans: [], 
            suspended_plans: 0,
            suspended_providers: 0,
            unconfirmed_transactions: {},
            uncovered_transactions: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addContact(){
            this.editMode = false;
            //Fire.$emit('visitDataFill', {});
            $('#providerModal').modal('show');
        },
        addPlan(){},
        closeModal(){
            $('#contactModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        createVisit() {
            this.$Progress.start();
            this.VisitForm.put('/api/emr/visits')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'A Visit has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        getAllInitials(){
            //this.$Progress.start();
            this.loading = true;
            axios.get('/api/emr/insurance/dashboard').then(response =>{
                this.refreshPage(response);
                this.loading = false;
                //this.$Progress.finish();
            })
            .catch(()=>{
                this.loading = false;
                //this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Dashboard was not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.active_visits = response.data.active_visits.length;
            this.unsettled_visits = response.data.unsettled_visits;
            this.suspended_providers = response.data.suspended_providers;
            this.suspended_plans = response.data.suspended_plans;
            this.uncovered_transactions = response.data.uncovered_transactions;
            this.unconfirmed_transactions = response.data.unconfirmed_transactions;
        },
    },
}
</script>