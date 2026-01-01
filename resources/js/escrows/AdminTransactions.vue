<template>
<section class="row">
    <div class="modal fade" id="transactionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Start Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormTransactionProduct :editMode="editMode" item_type="transaction" :product.sync="product" :transaction.sync="transaction" @refreshPage="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-navy">
                <h3 class="card-title">Transactions</h3>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 450px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary mr-1" @click="searchEmployee"><i class="fas fa-search"></i></button>
                            <select class="form-control" v-model="status" @change="getAllInitials()">
                                <option value="all">All</option>
                                <option value="rejected">Rejected</option>
                                <option value="pending">Pending</option>
                                <option value="agreed">Agreed</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="confirmed">Inspection Period</option>
                                <option value="completed">Completed</option>
                                <option value="disputed">Disputed</option>
                            </select>
                            <button type="button" class="btn btn-primary ml-1" @click="addTransaction"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px;">
            <EscrowDetailTransactionList :transactions="transactions.data" source="admin" :user_id="user.id" @refreshPage="getAllInitials" />
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="transactions.per_page != null ? transactions.per_page : 52" :records="transactions.total != null ? transactions.total : 550" >
                </pagination>
            </div>
        </div>
    </div>
</section>    
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            loading: false,
            product: {},
            query: '',
            status: 'all',
            transactions: { data: []},
            transaction: {},
            user: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#transactionModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/escrows/transactions?type=admin&page='+this.current_page+'&status='+this.status+'&query='+this.query)
            .then(response =>{
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Transactions loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transactions not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response){
            this.transactions = response.data.transactions;
            this.user = response.data.user;
            this.closeModals();
        },
    },
}
</script>