<template>
<section>
    <div class="invoice">
        <div class="invoice-head">
            <div class="row">
                <div class="col-12 col-md-7 col-lg-7">
                    <div class="invoice-logo">
                        <img :src="'/img/logo/nairafy-horizontal-logo.png'" class="img-fluid" alt="invoice-logo">
                    </div>
                    <h4>Nairafy Escrows Limited</h4>
                    <p>Secure Your Online Transactions with Confidence</p>
                </div>
                <div class="col-12 col-md-5 col-lg-5">
                    <div class="invoice-name">
                        <h5 class="text-uppercase mb-3">RECEIPT</h5>
                        <p class="mb-1">No : #98765</p>
                        <p class="mb-0">{{ExcelDate(payment.date)}}</p>
                        <h4 class="text-success mb-0 mt-3">{{currency(payment.amount)}}</h4>
                    </div>
                </div>
            </div>
        </div> 
        <div class="invoice-billing">
            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-4" v-if="payment.transaction != null && payment.transaction.seller != null">
                    <div class="invoice-address" v-if="payment.transaction.seller.company != null">
                        <h6 class="mb-3">Paid to</h6>
                        <h6 class="text-muted"><strong>{{ payment.transaction.seller.company.name }}</strong></h6>
                        <ul class="list-unstyled">
                            <li>{{ payment.transaction.seller.company.email}}</li>  
                            <li>{{ payment.transaction.seller.company.phone}}</li>   
                        </ul>
                    </div>
                    <div class="invoice-address" v-else>
                        <h6 class="mb-3">Paid to</h6>
                        <h6 class="text-dark"><strong>{{FullName(payment.transaction.seller)}}</strong></h6>
                        <ul class="list-unstyled">
                            <li v-html="payment.transaction.seller.address"></li>  
                            <li>{{payment.transaction.seller.email}}</li>  
                            <li>{{payment.transaction.seller.phone}}</li>  
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-4" v-if="payment.transaction != null && payment.transaction.buyer != null">
                    <div class="invoice-address">
                        <h6 class="mb-3">By</h6>
                        <h6 class="text-dark"><strong>{{ FullName(payment.transaction.buyer) }}</strong></h6>
                        <ul class="list-unstyled">
                            <li>{{ payment.transaction.buyer.email }}</li>  
                            <li>{{ payment.transaction.buyer.phone }}</li>  
                        </ul>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="invoice-address">
                        <div class="card">
                            <div class="card-body bg-info-rgba text-center">
                                <h6>Payment Channel</h6> 
                                <p>via {{ firstUp(payment.channel) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive ">
                <table class="table table-border table-striped">
                    <tbody v-if="payment.transaction != null">
                        <tr>
                            <td>Reference Number</td>
                            <td>{{ payment.transaction_id }}</td>
                        </tr>
                        <tr>
                            <td>Payment Date</td>
                            <td>{{ ExcelDate(payment.date) }}</td>
                        </tr>
                        <tr>
                            <td>Payment Channel</td>
                            <td>{{ firstUp(payment.channel) }}</td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>{{ currency(payment.amount) }}</td>
                        </tr>
                        <tr>
                            <td>Payment Timestamp</td>
                            <td>{{ ExcelDateFull(payment.time_stamped != null ? payment.time_stamped : payment.transaction.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>                                     
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            style: 'grid',
        }
    },
    emits:['refreshPage'],
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.payment = {};
            $('#transactionModal').modal('show');
            this.loading = false; 
        },
        closeModal(){
            $('#acceptFormModal').modal('hide');            
            $('#paymentModal').modal('hide');
            $('#transactionFormModal').modal('hide');
        },
        confirmTransaction(payment){
            this.loading = true;
            this.payment = payment;
            $('#acceptFormModal').modal('show');
            this.loading = false;
        },
        deactivateTransaction(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This payment will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/escrows/payments/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },     
    },
    mounted() {},
    props:{
        payment: Object,
        source: String,
        user_id: Number,
    },
    watch:{}
}
</script>