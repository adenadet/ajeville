<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>    
    <div class="modal fade" id="incomeFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Income Form</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormIncome :income.sync="income" :editMode.sync="editMode" @reloadIncomeForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Payment Form</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormPayment :income_id.sync="income.id" :editMode.sync="editMode" source="income" @reloadPaymentForm="refreshPage" />
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
                <th>Payer</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="incomes.length > 0">
            <tr v-for="income in incomes" :key="income.id">
                <td>{{ ExcelDate(income.due_date) }}</td>
                <td>{{ ExcelDate(income.date) }}</td>
                <td>{{ income.unique_id }}</td>
                <td>{{ income.classification != null ? income.classification.name : 'No classification assigned' }}</td>
                <td>{{ currency(income.amount) }}</td>
                <td>
                    <span v-if="income.customer != null">{{ income.customer.name }}</span>
                    <span v-else-if="income.vendor != null">{{ income.vendor.name }}</span>
                    <span v-else-if="income.staff != null">{{ FUllName(income.staff) }}</span>
                    <span v-else>Not Assigned</span>
                </td>
                <td>
                    <span v-if="income.status==1" class="badge badge-warning">Unconfirmed</span>
                    <span v-else-if="income.status==5" class="badge bg-orange">Queried</span>
                    <span v-else-if="income.status==10" class="badge badge-info">Confirmed</span>
                    <span v-else-if="income.status==40" class="badge badge-danger">Rejected</span>
                    <span v-else-if="income.status==100" class="badge badge-danger">Deleted</span>
                    <span v-else-if="income.status==10" class="badge bg-success">Paid</span>
                </td>
                <td>
                    <button class="nav-link btn btn-sm btn-tool mt-1" data-toggle="dropdown" type="button">
                        <i class="fa fa-ellipsis-v text-dark"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'/finance/incomes/'+income.unique_id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Income</button></router-link>
                        <button v-if="income.status > 2" class="dropdown-item btn btn-block btn-sm" @click="createQuery(income)"><i class="fa fa-exclamation-circle mr-1 text-warning"></i> Create Dispute</button>
                        <!--button v-if="income.status < 10" class="dropdown-item btn btn-block btn-sm" @click="confirmIncome(income)"><i class="fa fa-handshake mr-1 text-info"></i> Confirm Income</button-->
                        <button v-if="income.status == 10" class="dropdown-item btn btn-block btn-sm" @click="makePayment(income)"><i class="fa fa-hand-holding-usd mr-1 text-warning"></i> Make Payment</button>
                        <button v-if="income.status <= 10" class="dropdown-item btn btn-block btn-sm" @click="updateIncome(income)"><i class="fa fa-edit mr-1 text-success"></i> Update Income</button>
                        <button v-if="income.status <= 10" class="dropdown-item btn btn-block btn-sm" @click="cancelIncome(income)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Income</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="9">No Incomes meets your criteria</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            income: {},
            form: new Form({}),
            loading: false,
            patient_id: '',
        }
    },
    emits:['refres'],
    methods: {
        addService(){
            $('#incomeFormModal').modal('show'); 
        },
        cancelIncome(income){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This income would be deleted and payment reversed",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/incomes/'+income.id)
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
        closeModals(){
            $('#incomeFormModal').modal('hide');  
            $('#paymentFormModal').modal('hide');  
            $('#serviceModal').modal('hide');  
        },
        makePayment(income){
            this.editMode = false;
            this.loading  = true;
            this.income = income;
            $('#paymentFormModal').modal('show');  
            this.loading  = false;
        },
        viaWallet(income){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The patient's wallet would be debited for this income",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.form.income_id = income.id;
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
        viewIncome(income){
            this.income = income;
            $('#incomeFormModal').modal('show');
        },
        updateIncome(income){
            this.loading = true;
            this.editMode = true;
            this.income = income;
            $('#incomeFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
        },
    },
    props:{
        incomes: Array,
        source: String,
    }
}
</script>