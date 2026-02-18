<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-5">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">{{ transaction?.service_type?.name || transaction?.item_name }}</h3>
                    <p class="text-muted text-center">{{ patientName(transaction?.patient) }}</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Visit:</b> <a class="float-right">{{ transaction?.visit?.unique_id }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Payment Status:</b> <a class="float-right">{{ transaction?.status == 100 ? 'Paid' : 'Unpaid' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Status:</b>
                            <span class="float-right" v-if="transaction?.status == 400"> Cancelled</span>
                            <span class="float-right" v-else-if="transaction?.status == 1000"> Transferred</span>
                            <span class="float-right" v-else-if="transaction?.service_status == 1"> Completed</span>
                        </li>
                    </ul>
                    <!--a href="#" class="btn btn-primary btn-block"><b>Follow</b></a-->
                </div>
                <div class="card-footer p-0">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                            Requested By: <span class="float-right badge">{{ FullName(transaction?.creator) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                            Service Type <span class="float-right badge">{{ transaction?.service_type?.name || 'N/A' }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Payment Type <span class="float-right badge">{{ transaction?.paid_by == 1 ? 'Cash' : (transaction?.paid_by == 2 ? 'Credit' : 'Co-paid') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">

        </div>
        <div class="col-md-3">

        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            active_visits: 0,
            invoices: {},
            patient: [],
            transaction: {},
            pending_invoices: {},
        }
    },
    mounted() {},
    methods: {
        getInitials() {
            axios.get('/api/emr/visit/transactions/'+this.transaction_id).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'The transaction did not load successfully',});
            });
        },
        refreshPage(response) {
            this.transaction = response.data.transaction;
        }
    },
    props: {
        transaction_id: Number,
    },
    watch:{
        transaction_id(){
            this.getInitials();
        }
    }
}
</script>