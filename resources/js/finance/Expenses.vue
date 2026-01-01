<template>
<section class="card overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>    
    <div class="modal fade" id="expenseModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Expense Form</h3>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormExpense :expense.sync="expense" :editMode.sync="editMode" @reloadExpenseForm="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-dark">
        <h3 class="card-title">Expenses</h3>
        <div class="card-tools">
            <div class="input-group input-group-sm" style="width: 350px;">
                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                <div class="input-group-append">
                    <button type="button" class="btn btn-default mr-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                    <select class="form-control form-control-sm ml-1" id="type" name="type" v-model="type">
                        <option value="">-- Type --</option>
                        <option value="all">All</option>
                        <option value="completed">Completed</option>
                        <option value="overdue">Overdue</option>
                        <option value="pending">Pending</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                    <button type="button" class="btn btn-primary ml-1" @click="addExpense"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-success ml-1" @click="downloadExpense"><i class="fas fa-download"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0" style="height: 600px">
        <FinanceDetailExpenseList :expenses.sync="expenses.data" @reloadExpenseList="getAllInitials" />
    </div>
    <div class="card-footer"><pagination v-model="current_page" @paginate="getAllInitials" :per-page="expenses.per_page != null ? expenses.per_page : 52" :records="expenses.total != null ? expenses.total : 550" ></pagination></div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            expense: {},
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
            axios.get('/api/finance/expenses?query='+this.query+'&page='+this.current_page+'&type='+this.type).then(response => {
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
            this.expenses = response.data.expenses;
        }
    },
}
</script>