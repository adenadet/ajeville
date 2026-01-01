<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Income Details</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5"><FinanceDetailIncome :income.sync="income" /></div>
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    Payments
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <FinanceDetailPaymentAllocationList source="income" :allocations.sync="income.allocations" /> 
                                </div>
                            </div>
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
            current_page: 1,
            editMode: false,
            income: {allocations: [],},
            loading: false,
            query: '',
            type: 'all',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addIncome(){
            this.loading = true;
            this.editMode = false;
            this.income = {};
            $('#incomeModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#incomeModal').modal('hide');
        },
        getAllInitials(page=1) {
            this.closeModals();
            this.loading = true;
            axios.get('/api/finance/incomes/'+this.$route.params.id).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Incomes did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.income = response.data.income;
        }
    },
}
</script>