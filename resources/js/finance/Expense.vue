<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Expense Details</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5"><FinanceDetailExpense :expense.sync="expense" /></div>
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    Payments
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <FinanceDetailPayOutAllocationList source="expense" :allocations.sync="expense.allocations" /> 
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
            expense: {allocations: [],},
            expenses: {data: [], total: 0},
            loading: false,
            query: '',
            type: 'all',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addExpense(){
            this.loading = true;
            this.editMode = false;
            this.expense = {};
            $('#expenseModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#expenseModal').modal('hide');
        },
        getAllInitials(page=1) {
            this.closeModals();
            this.loading = true;
            axios.get('/api/finance/expenses/'+this.$route.params.id).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Expenses did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.expense = response.data.expense;
        }
    },
}
</script>