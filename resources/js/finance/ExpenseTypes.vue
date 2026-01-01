<template>
<section class="card overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>    
    <div class="modal fade" id="expenseTypeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Expense Type Form</h3>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <FinanceFormExpenseType :expense_type.sync="expense_type" :editMode.sync="editMode" @reloadExpenseTypeForm="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-dark">
        <h3 class="card-title">Expense Types</h3>
        <div class="card-tools">
            <div class="input-group input-group-sm" style="width: 350px;">
                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                <div class="input-group-append">
                    <button type="button" class="btn btn-default mr-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                    <select class="form-control form-control-sm ml-1" id="type" name="type" v-model="type">
                        <option value="">-- Status --</option>
                        <option value="all">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="deleted">Deleted</option>
                    </select>
                    <button type="button" class="btn btn-primary ml-1" @click="addExpenseType"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0" style="height: 500px">
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Last Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="expense_types.total > 0">
                <tr v-for="expense_type in expense_types.data">
                    <td>{{ expense_type.name }}</td>
                    <td>{{ expense_type.status == 1 ? 'Active' : 'Inactive' }}</td>
                    <td :title="expense_type.description" v-html="readMore(expense_type.description, 100, '...')"></td>
                    <td>{{ FullName(expense_type.creator) }} <br /><span class="text-muted text-sm">{{ ExcelDate(expense_type.created_at) }}</span></td>
                    <td>{{ FullName(expense_type.updater) }} <br /><span class="text-muted text-sm">{{ ExcelDate(expense_type.updated_at) }}</span></td>
                    <td>
                        <button class="nav-link btn btn-sm btn-tool mt-1" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v text-dark"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!--router-link :to="'/finance/expenses/'+expense.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Transaction</button></router-link-->
                            <button class="dropdown-item btn btn-block btn-sm" @click="updateExpenseType(expense_type)"><i class="fa fa-edit mr-1 text-primary"></i> Update Expense Type</button>
                            <button v-if="expense_type.status == 1" class="dropdown-item btn btn-block btn-sm" @click="deactivateExpenseType(expense_type)"><i class="fa fa-recycle mr-1 text-danger"></i> Deactivate Expense Type</button>
                            <button v-if="expense_type.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deactivateExpenseType(expense_type)"><i class="fa fa-recycle mr-1 text-success"></i> Deactivate Expense Type</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr><td colspan="6"> No Expense Type meets your criteria</td></tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer"><pagination v-model="current_page" @paginate="getAllInitials" :per-page="expense_types.per_page != null ? expense_types.per_page : 52" :records="expense_types.total != null ? expense_types.total : 550" ></pagination></div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            expense_type: {},
            expense_types: {data: [], total: 0},
            loading: false,
            query: '',
            type: 'all',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addExpenseType(){
            this.loading = true;
            this.editMode = false;
            this.expense_type = {};
            $('#expenseTypeModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#expenseTypeModal').modal('hide');
        },
        deactivateExpenseType(expense_type){
            this.$swal.fire({
                title: 'Are you sure?',
                text: expense_type.status == 1 ? "This Expense Type would be deactivated and not available for assignment" : "This Pricelist would be reactivated and now be available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/expense_types/'+expense_type.id)
                    .then(response=>{
                        this.$emit('refreshPriceLists');
                        this.$swal.fire('Success!', response.data.message, 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials(page=1) {
            this.closeModals();
            this.loading = true;
            axios.get('/api/finance/expense_types?query='+this.query+'&page='+this.current_page+'&type='+this.type).then(response => {
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
            this.expense_types = response.data.expense_types;
        },
        updateExpenseType(expense_type){
            this.loading = true;
            this.editMode = true;
            this.expense_type = expense_type;
            $('#expenseTypeModal').modal('show');
            this.loading = false;
        },
    },
}
</script>