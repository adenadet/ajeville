<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="salesOrderModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Approve Sales Order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="editMode = false"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <!--ApprovalFormSalesOrder :expense.sync="expense"/ -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">  
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Expenses</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <FinanceDetailExpenseList :expenses.sync="expenses.data" view="approvals" @salesOrderReload="getAllInitials" />
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="expenses.per_page != null ? expenses.per_page : 52" :records="expenses.total != null ? expenses.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import FinanceDetailExpenseList from '@/inventory/details/ExpenseList.vue';
export default {
    components:{FinanceDetailExpenseList},
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form_type: '',
            loading: false,
            expense: {},
            expenses: {data:[],},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        approveOrder(expense){
            this.loading = true;
            this.editMode = true;
            this.form_type = "accept";
            $('#expenseModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#expenseModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/finance/expenses?type=unapproved&page='+this.current_page+'&query='+this.query)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Sales Orders loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transfer Orders not loaded successfully',
                })
            });
        },
        issueRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "issue";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        refreshPage(response){
            this.expenses = response.data.expenses;
        },
    },
}
</script>