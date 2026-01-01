<template>
<section class="overlay-wrapper p-0">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title">Payment Modes</h4>
            <div class="card-tools">
                <button class="btn btn-primary btn-xs" @click="addPaymentMode"><i class="fa fa-plus mr-1"></i>Add New</button>
            </div>
        </div>
        <div class="card-body p-0" style="height:500px; overflow-y: auto;">
            <FinanceDetailPaymentModeList :payment_modes="payment_modes.data" @refreshPaymentModes="getInitials" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="payment_modes.per_page != null ? payment_modes.per_page : 52" :records="payment_modes.total != null ? payment_modes.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            payment_modes: { data: [],},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addPaymentMode(){
            this.loading = true;
            this.editMode = false;
            this.payment_mode = {};
            $('#paymentModeModal').modal('show');
            this.loading = false;
        },
        getInitials() {
            this.loading = true
            axios.get('/api/finance/payment_modes').then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Payment Modes did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.payment_modes = response.data.payment_modes;
        }
    },
}
</script>