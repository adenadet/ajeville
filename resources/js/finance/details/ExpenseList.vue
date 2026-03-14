<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>    
    <div class="modal fade" id="expenseFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Expense Form</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormExpense :expense.sync="expense" :editMode.sync="editMode" @reloadExpenseForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Expense Form</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormPayOut :expense_id.sync="expense.id" :editMode.sync="editMode" source="expense" @reloadPayOutForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped text-nowrap">
        <thead>
            <tr>
                <th>Payment Due Date</th>
                <th>Raised Date</th>
                <th>Unique ID</th>
                <th>Classification</th>
                <th>Amount</th>
                <th>Receiver</th>
                <th>Status</th>
                <th><button type="button" class="btn btn-xs btn-primary ml-1" @click="addExpense"><i class="fas fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="expenses.length > 0">
            <tr v-for="expense in expenses" :key="expense.id">
                <td>{{ ExcelDate(expense.due_date) }}</td>
                <td>{{ ExcelDate(expense.date) }}</td>
                <td>{{ expense.unique_id }}</td>
                <td>{{ expense.classification != null ? expense.classification.name : 'No classification assigned' }}</td>
                <td>{{ currency(expense.amount) }}</td>
                <td>
                    <span v-if="expense.customer != null">{{ expense.customer.name }}</span>
                    <span v-else-if="expense.vendor != null">{{ expense.vendor.name }}</span>
                    <span v-else-if="expense.staff != null">{{ FUllName(expense.staff) }}</span>
                    <span v-else>Not Assigned</span>
                </td>
                <td>
                    <span v-if="expense.status==1" class="badge badge-warning">Unconfirmed</span>
                    <span v-else-if="expense.status==5" class="badge bg-orange">Queried</span>
                    <span v-else-if="expense.status==10" class="badge badge-info">Confirmed</span>
                    <span v-else-if="expense.status==40" class="badge badge-danger">Rejected</span>
                    <span v-else-if="expense.status==100" class="badge badge-danger">Deleted</span>
                    <span v-else-if="expense.status==10" class="badge bg-success">Paid</span>
                </td>
                <td>
                    <button class="nav-link btn btn-sm btn-tool mt-1" data-toggle="dropdown" type="button">
                        <i class="fa fa-ellipsis-v text-dark"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'/finance/expenses/'+expense.unique_id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Expense</button></router-link>
                        <button v-if="expense.status > 2" class="dropdown-item btn btn-block btn-sm" @click="createQuery(expense)"><i class="fa fa-exclamation-circle mr-1 text-warning"></i> Create Dispute</button>
                        <!--button v-if="expense.status < 10" class="dropdown-item btn btn-block btn-sm" @click="confirmExpense(expense)"><i class="fa fa-handshake mr-1 text-info"></i> Confirm Expense</button-->
                        <button v-if="expense.status == 10" class="dropdown-item btn btn-block btn-sm" @click="makePayment(expense)"><i class="fa fa-hand-holding-usd mr-1 text-warning"></i> Make Payment</button>
                        <button v-if="expense.status <= 10" class="dropdown-item btn btn-block btn-sm" @click="updateExpense(expense)"><i class="fa fa-edit mr-1 text-success"></i> Update Expense</button>
                        <button v-if="expense.status <= 10" class="dropdown-item btn btn-block btn-sm" @click="cancelExpense(expense)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Expense</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="9">No Expenses meets your criteria</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import FinanceDetailExpense from '@/finance/details/Expense.vue';
import FinanceFormExpense from '@/finance/forms/Expense.vue';
import FinanceFormPayOut from '@/finance/forms/PayOut.vue';
export default {
    components: {FinanceDetailExpense, FinanceFormExpense, FinanceFormPayOut},    
    data() {
        return {
            editMode: false,
            expense: {},
            form: new Form({}),
            loading: false,
            patient_id: '',
        }
    },
    emits:['refres'],
    methods: {
        addExpense(){
            this.loading = true;
            this.editMode = false;
            this.expense = {};
            $('#expenseFormModal').modal('show');
            this.loading = false;
        },
        cancelExpense(expense){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This expense would be deleted and payment reversed",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/expenses/'+expense.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, response.data.icon);
                        this.loading = false; 
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        closeModal(){
            $('#expenseFormModal').modal('hide');  
            $('#paymentFormModal').modal('hide');  
            $('#serviceModal').modal('hide');  
        },
        makePayment(expense){
            this.editMode = false;
            this.loading  = true;
            this.expense = expense;
            $('#paymentFormModal').modal('show');  
            this.loading  = false;
        },
        viaWallet(expense){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The patient's wallet would be debited for this expense",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.form.expense_id = expense.id;
                    this.form.post('/api/finance/payments')
                    .then(response=>{
                        Swal.fire('Update!', response.data.message, response.data.icon);
                        //this.getInitials();  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewExpense(expense){
            this.expense = expense;
            $('#expenseFormModal').modal('show');
        },
        updateExpense(expense){
            this.loading = true;
            this.editMode = true;
            this.expense = expense;
            $('#expenseFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
        },
    },
    props:{
        expenses: Array,
        source: String,
    }
}
</script>