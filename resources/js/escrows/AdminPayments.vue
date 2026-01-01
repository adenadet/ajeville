<template>
<section class="content-header">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-6">
                    <h5 class="card-title card-title font-18">Payments</h5>
                </div>
                <div class="col-6 p-0">
                    <div class="card-statistics text-right float-right">
                        <div class="input-group input-group" style="width: 350px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary mr-1" @click="searchPayment"><i class="fa fa-search"></i></button>
                                <select class="form-control" v-model="filterData.status" @change="getAllInitials()">
                                    <option value="all">All</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="initiated">Initiated</option>
                                    <option value="paid">Paid</option>
                                </select>
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
                                                                    <option value="rejected">Rejected</option>
                                                                    <option value="initiated">Initiated</option>
                                                                    <option value="paid">Paid</option>
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
        <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 600px;">
            <EscrowDetailPaymentList :payments.sync="payments.data" source="payments" :user_id.sync="user.id" @refreshPage="getAllInitials()" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials()" :per-page="payments.per_page != null ? payments.per_page : 52" :records="payments.total != null ? payments.total : 550" ></pagination>
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
                channel: '',
                status: 'paid',
                start_date: '',
                end_date: '',
                amount: '',
                receipt: '',
                customer: '',
                paymentPage: '',
                terminalId: '',
                saveDefault: false,
            }),
            loading: false,
            query: '',
            payments: { data: []},
            payment: {},
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
            this.payment = {};
            $('#transactionModal').modal('show');
            this.loading = false;  
        },
        advancedFilter(){
            this.loading = true;
            this.filterData.post('/api/escrows/payments/filter')
            .then(response =>{
                this.refreshPage(response)
                //this.$emit('refreshPage', response);
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;
        },
        closeModals(){
            $('#transactionModal').modal('hide');
        },
        async exportQuery(){
            this.loading = true;
            //this.filterData.post('/api/escrows/payments/generate_report')
            try {
                const response = await axios.post('/api/escrows/payments/generate_report', this.filterData, {responseType: 'blob',});
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
            } catch (error) {
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
            this.filterData.get('/api/escrows/payments?type=admin&page='+this.current_page+'&status='+this.filterData.status+'&query='+this.query)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Payments loaded successfully',
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
            this.payments = response.data.payments;
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
            var tomorrow = yyyy+'-'+mm+'-'+dt;
            var month_start = yyyy+'-'+mm+'-01';
            this.filterData.start_date = month_start;
            this.filterData.end_date = today;
            this.filterData.status = 'paid';
            this.filterData.channel = 'all';

        }
    },
}
</script>