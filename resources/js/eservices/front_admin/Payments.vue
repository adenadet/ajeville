<template>
    <section class="container-fluid">
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Payments</h3>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-primary" @click="addPayment"><i class="fa fa-calendar-plus"></i> Make Payment</button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <EServiceDetailPaymentList :payments.sync="payments" />
                        </div>
                        <div class="card-footer">    
                            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="payments.per_page != null ? payments.per_page : 52" :records="payments.total != null ? payments.total : 550" >
                            </pagination>
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
            //this.unpaid_appointments;
            Fire.$emit('UnpaidAppointments', this.unpaid_apppointments);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        getAllInitials() {
            axios.get('/api/emr/payments').then(response => {
                this.refreshPayments(response)
                //Fire.$emit('refreshPayment', response);
                })
                .catch(() => {
                    this.$Progress.fail();
                    toast.fire({
                        icon: 'error',
                        title: 'Your payments did not loaded successfully',
                    })
                });
        },
        getPayments(page=1){
            axios.get('/api/emr/payments?page='+page)
            .then(response=>{this.payments = response.data.payments;});
        },
        refreshPayments(response) {
            this.payments = response.data.payments;
            this.unpaid_apppointments = response.data.unpaid_appointments;
        },

    },
    props: {}
}
</script>