<template>
    <section class="overlay-wrapper p-0">
        <table class="table table-striped table-hover text-nowrap">
            <thead>
                <tr>
                    <th></th>
                    <th>Date</th>
                    <th>Service Name</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Completion Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="!(loading) && requests != null && requests.data.length != 0">
                <tr v-for="(request, index) in requests.data" :key="request.id" :class="request.status == 0 ? 'text-danger' : ''">
                    <td>{{ addOne(index)  }}</td>
                    <td>{{ ExcelDate(request.date) }}</td>
                    <td>{{ request.item_name }}</td>
                    <td>{{ request.item_total }}</td>
                    <td>
                        <span v-if="request.status == 400" class="badge badge-danger">Cancelled</span>
                        <span v-else-if="request.status == 100" class="badge badge-success">Paid</span>
                        <span v-else-if="request.status == 1" class="badge badge-dark">Unpaid</span>
                    </td>
                    <td>
                        <span v-if="request.status == 400" class="badge badge-danger">Cancelled</span>
                        <span v-else-if="request.status == 1000" class="badge badge-info">Transferred</span>
                        <span v-else-if="request.service_status == 1" class="badge badge-success">Done</span>
                        <span v-else class="badge badge-warning">Pending</span>
                    </td>
                    <td>
                        <span class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fa fa-ellipsis-v"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button class="btn btn-block dropdown-item" @click="viewTransaction(request)"><i class="fas fa-eye mr-2"></i> View Transaction</button>
                            <button class="btn btn-block dropdown-item" v-if="request.status == 1 && (request.paid_by == 1 || request.paid_by == 3)" @click="viaWallet(request)"><i class="fas fa-wallet mr-2"></i> Pay via Wallet</button>
                            <button class="btn btn-block dropdown-item" v-if="request.service_status == 0" @click="cancelTransaction(request)"><i class="fas fa-times mr-2"></i> Cancel</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else-if="loading">
                <tr>
                    <td colspan="8">
                        <div class="card">
                            <div class="overlay-wrapper">
                                <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="8">No Transaction Created</td>
                </tr>
            </tbody>
        </table>    
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
        }
    },
    mounted() {
        
    },
    methods: {
        viaWallet(transaction){
            var force = 0;
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The patient's wallet would be debited for this transaction",
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "Pay Wallet",
                denyButtonText: "Force Debit",
                confirmButtonColor: '#3035d6',
                cancelButtonColor: '#d33',
                denyButtonColor: '#0D0',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if((result.isConfirmed) || (result.isDenied)){
                    force = result.isDenied ? 1 : 0;
                    this.form.get('/api/emr/finance/transactions/'+transaction.id+'/payment?forced='+force)
                    .then(response=>{
                        this.$swal.fire('Paid', 'Transaction paid', 'success');
                        this.$emit('refreshTransactionList');
                    })
                    .catch(error => {
                        let message = 'Payment failed.';
                        if (error.response && error.response.data) {
                            message = error.response.data.transaction || error.response.data.message || message;
                        }
                        this.$swal.fire({ icon: 'error', title: 'Payment Failed', text: message});
                    });
                }
            });
        },
        pay_from_wallet(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Debit patient's wallet for transaction!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/lms/courses/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        }
    },
    props: {
        actionable: String,
        requests: Array,
        source: String,
    }
}
</script>