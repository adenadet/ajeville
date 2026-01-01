<template>
    <section class="container-fluid">
        <div class="modal fade" id="authCodeModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Enter Authorization Code</h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body bg-white">
                        <InsuranceFormAuthCode :transactions="transaction_list"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="requestCodeModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Input Authorization Code</h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body bg-white">
                        <InsuranceFormAuthRequest :transactions="transaction_list"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pending Transactions</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 450px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default" @click="inputAuthCodes">
                                        <i class="fas fa-check mr-1 text-info"></i>Enter Auth Code
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-inbox mr-1 text-primary"></i>Request Authorization 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <InsuranceDetailTransactionList :transactions="transactions.data" view="authorizations"/>
                    <div class="card-footer">
                        <div class="col-12">
                            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="transactions.per_page != null ? transactions.per_page : 52" :records="transactions.total != null ? transactions.total : 550" ></pagination>
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
            loading: true,
            transactions: {},
            transaction: {}, 
            transaction_list: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        enterAuthCode(){
            $('#authCodeModal').modal('show');
        },
        closeModal(){
            $('#authCodeModal').modal('hide');
            $('#requestCodeModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        getAllInitials(){
            //.start();
            axios.get('/api/emr/insurance/transactions?q=auth').then(response =>{
                this.refresh(response);
                //.finish();
            })
            .catch(()=>{
                //.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        },
        inputAuthCode(){

        },
        inputAuthCodes(){
            if (this.selected_transactions.length == 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No items selected!',
                    footer: 'Please select at least one item!'
                });
            }
            else{
                this.transaction_list = this.selected_transactions;
                $('#authCodeModal').modal('show');
            }
        },
        refresh(response){
            this.transactions = response.data.transactions;
            this.transaction = response.data.transactions.data[0];
            this.loading = false;
        },
        rejectTransaction(transaction){},
        requestCode(transaction){
            this.editMode = false;
            Fire.$emit('providerDataFill', {});
            $('#requestCodeModal').modal('show');
        },
        setCoverage(transaction){},
    },
}
</script>