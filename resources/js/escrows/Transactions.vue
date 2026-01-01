<template>
<section class="row">
    <div class="modal fade" id="transactionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">Start Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormTransaction :editMode="editMode" item_type="transaction" :product.sync="{}" :transaction.sync="{}" @refreshPage="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="card-title card-title font-18">Transactions</h5>
                    </div>
                    <div class="col-6">
                        <div class="card-statistics d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                            <div class="input-group input-group">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="filterData.query">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary mr-1" @click="filterData.query"><i class="fa fa-search"></i></button>
                                    <select class="form-control" v-model="filterData.status" @change="getAllInitials()" id="status" name="status"> 
                                        <option value="all">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                        <option value="disputed">Disputed</option>
                                    </select>
                                    <button type="button" class="btn btn-primary ml-1" @click="addTransaction"><i class="fa fa-plus"></i></button>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle infobar-icon" type="button" id="notoficationlink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-calendar mr-1"></i><i class="fa fa-filter mb-1"></i></button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notoficationlink">
                                            <ul class="list-unstyled">                                                    
                                                <li class="media dropdown-item">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5 class="card-title">Advanced Filters</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <form>
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label">Status</label>
                                                                    <select v-model="filterData.status" class="form-control" id="status" name="status">
                                                                        <option value="all">All</option>
                                                                        <option value="pending">Pending</option>
                                                                        <option value="ongoing">Ongoing</option>
                                                                        <option value="completed">Completed</option>
                                                                        <option value="disputed">Disputed</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="channel" class="form-label">Channel</label>
                                                                    <select v-model="filterData.channel" class="form-control" id="channel" name="channel">
                                                                        <option value="" selected>--Show all--</option>
                                                                        <option value="paystack">Paystack</option>
                                                                        <option value="alatpay">AlatPay</option>
                                                                        <option value="quickteller">Quickteller</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="amount" class="form-label">Amount</label>
                                                                    <input v-model="filterData.amunt" type="number" class="form-control" id="amount" name="amount" placeholder="payment amount">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="receipt" class="form-label">Receipt number</label>
                                                                    <input type="text" v-model="filterData.receipt_number" class="form-control" id="receipt" name="receipt" placeholder="Receipt number">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="customer" class="form-label">Customer ID / Email</label>
                                                                    <input type="text" class="form-control" v-model="filterData.customer" id="customer" name="customer" placeholder="Customer ID or email">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="startDate" class="form-label">Start Date</label>
                                                                    <input type="date" class="form-control" v-model="filterData.start_date" id="startDate" name="startDate" placeholder="Payment Page ID">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="endDate" class="form-label">End Date</label>
                                                                    <input type="date" class="form-control" v-model="filterData.end_date" id="endDate" name="endDate" placeholder="Payment Page ID">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="terminalId" class="form-label">Terminal ID</label>
                                                                    <input type="text" class="form-control-sm form-control" id="terminalId" name="terminalId" placeholder="Terminal ID">
                                                                </div>
                                                                <div class="form-check mb-3">
                                                                    <input class="form-check-input" type="checkbox" id="saveDefault" name="saveDefault">
                                                                    <label class="form-check-label" for="saveDefault">
                                                                        Save as default filter
                                                                    </label>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-10">
                                                                        <button type="button" class="btn btn-success" @click="getAllInitials()">Filter</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success ml-1" @click="exportQuery"><i class="fa fa-download"></i></button>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>  
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px;">
                <div v-if="loading" class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                </div>
                <div v-else>
                    <EscrowDetailTransactionList :transactions="transactions.data" source="main" :user_id="user.id" @refreshPage="getAllInitials" />
                </div>
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
            filterData: new Form({
                amount: '',
                customer: '',
                end_date: '',
                channel: '',
                paymentPage: '',
                page: 1,
                query: '',
                receipt: '',
                start_date: '',
                status: 'ongoing',
                terminalId: '',
                type: 'mine',
            }),
            loading: false,
            transactions: { data: []},
            transaction: {},
            user: {},
        }
    },
    mounted() {
        this.resetPage();
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
        async exportQuery(){
            this.loading = true;
            //this.filterData.post('/api/escrows/payments/generate_report')
            try {
                const response = await axios.post('/api/escrows/transactions/generate_report', this.filterData, {responseType: 'blob',});
                const contentDisposition = response.headers['content-disposition'];
                const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'transaction_report.csv';

                // Create a blob URL and force download
                const blob = new Blob([response.data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
            } 
            catch (error) {
                console.error('Download failed:', error);
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to generate report',
                });
            }
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            this.filterData.page = this.current_page;
            this.filterData.get('/api/escrows/transactions')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
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
        },
        refreshPage(response){
            this.transactions = response.data.transactions;
            this.user = response.data.user;
            this.closeModals();
        },
        resetPage(){
            var today = new Date();
            var dd = today.getDate();
            var dt = dd + 1;
            var mm = today.getMonth()+1;
                
            var yyyy = today.getFullYear();
            if(dd<10){dd='0'+dd;} 
            if(dt<10){dt='0'+dt;} 
            if(mm<10){mm='0'+mm;} 
            today = yyyy+'-'+mm+'-'+dd;
            //var tomorrow = yyyy+'-'+mm+'-'+dt;
            var month_start = yyyy+'-'+mm+'-01';
            this.filterData.start_date = month_start;
            this.filterData.end_date = today;
            this.filterData.status = 'ongoing';
            this.filterData.channel = 'all';
        }
    },
}
</script>