<template>
<section class="overlay-wrapper">
    <div class="overlay" v-if="loading">
        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
    </div>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ transactions.total }}</h3><p>Transactions (this week)</p>
                </div>
                <div class="icon">
                    <i class="fa fa-handshake"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ users.total }}</h3><p>New User Registrations</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-plus"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>    
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ disbursements.total }}</h3><p>Pending Disbursements</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{disputes.total}}</h3><p>Pending Disputes</p>
                </div>
                <div class="icon">
                    <i class="fa fa-people-arrows"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header border-0 bg-navy">
                    <h3 class="card-title">Recent Transactions</h3>
                    <div class="card-tools"></div>
                </div>
                <div class="card-body table-responsive p-0" style="height:400px;">
                    <EscrowDetailTransactionList :transactions="transactions.data" source="admin" />
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header border-0 bg-dark">
                    <h3 class="card-title">Recent Payments</h3>
                    <div class="card-tools"></div>
                </div>
                <div class="card-body table-responsive p-0" style="height:400px;">
                    <EscrowDetailPaymentList :payments="payments.data" source="admin" />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            disbursements: { data: [], total:0},
            disputes: { data: [], total:0},
            editMode: false,
            loading: false,
            payments: { data: []},
            transactions: { data: [], total:0},
            users: { data: [], total:0},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.product = {};
            $('#productModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#disputeModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/escrows/dashboard?type=admin')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Admin Dashboard loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Admin Dashboard not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.disputes = response.data.disputes;
            this.payments = response.data.payments;
            this.transactions = response.data.transactions;
            this.users = response.data.users;
            this.closeModals();
        },
    },
}
</script>