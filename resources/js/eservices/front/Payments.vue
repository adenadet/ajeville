<template>
    <section class="content-header p-0">
        <div class="container-fluid">
            <div class="modal fade" id="paymentModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Make Payment</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <EServiceFormPayment />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card overlay-wrapper">
                        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                        <EServiceDetailPaymentList :payments.sync="payments" />
                        <div class="card-footer">    
                            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="payments.per_page != null ? payments.per_page : 52" :records="payments.total != null ? payments.total : 550" >
                            </pagination>
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
            current_page: 1,
            loading: false,
            payment: {},
            payments: {},
            unpaid_apppointments: {},
            editMode: true,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addPayment(){
            this.$Progress.start();
            this.paySpecific = false;
            this.$emit('UnpaidAppointments', this.unpaid_apppointments);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        getAllInitials(page=1) {
            this.loading = true;
            axios.get('/api/emr/payments?page='+page).then(response => {
                this.refreshPayments(response);
                this.loading = false;
            })
                .catch(() => {
                    this.loading = false;
                    toast.fire({
                        icon: 'error',
                        title: 'Your payments did not loaded successfully',
                    })
                });
        },
        refreshPayments(response) {
            this.payments = response.data.payments;
            this.unpaid_apppointments = response.data.unpaid_appointments;
        },

    },
    props: {}
}
</script>