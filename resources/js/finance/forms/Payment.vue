<template>
<section class="overly-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row p-0 m-0">
        <div :class="trans_type != null ? 'col-md-6' : ''">
            <div class="card" v-if="trans_type == 'sales' && order != null">
                <div class="card-header bg-dark" v-if="order != null && order.id != null">
                    <h3 class="card-title">Transaction Details</h3>
                </div>
                <div class="card-body">
                    <SalesDetailOrderSummary :order.sync="order" />
                </div>
            </div>
        </div>
        <div :class="trans_type != null ? 'col-md-6' : 'col-md-12'">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                <form role="form" @submit.prevent="editMode ? updatePayment() :createPayment()">
                    <div class="card-body">
                        <div class="row p-0">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <div class="form-control" v-if="customer != null" >
                                        {{ customer.name }}
                                    </div>
                                    <select v-else class="form-control" id="customer_id" name="customer_id" v-model="paymentData.customer_id">
                                        <option value="">--Select Customer--</option>
                                        <option v-for="customer in customers" :value="customer.id">{{ customer.name }}</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Payment Mode</label>
                                    <select class="form-control" id="mode_id" name="mode_id" v-model="paymentData.mode_id">
                                        <option value="">--Select Payment Type--</option>
                                        <option v-for="type in modes" :value="type.id">{{ type.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input class="form-control" type="date" id="date" name="date" v-model="paymentData.date" required/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Account</label>
                                    <select class="form-control" id="bank_id" name="bank_id" v-model="paymentData.bank_id" required>
                                        <option value="">--Select Account--</option>
                                        <option v-for="bank in banks" :value="bank.id">{{ bank.account_name }} [{{ bank.bank.name }} - {{ bank.account_number }}]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputFile">Amount</label>
                                    <input type="number" class="form-control" id="amount" step="0.01" v-model="paymentData.amount" required>
                                </div>        
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputFile">Description</label>
                                    <QuillEditor class="form-control" id="description" name="description" theme="snow" content-type="html" v-model:content="paymentData.description" />
                                </div>        
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import SalesDetailOrderSummary from '@/sales_orders/details/OrderSummary.vue'; 
export default {
    components:{SalesDetailOrderSummary},
    data(){
        return {
            banks: [],
            current_page: 1,
            customers: [],
            items: 0,
            loading: false,
            modes: [],
            order: {order_items: [],},
            paymentData: new Form({
                id: '',
                date: '',
                amount: '',
                bank_id: '',
                customer_id: '',
                mode_id: '',
                reference_id: '',
                staff_id: '',
                transaction_id: '',
                trans_type: '',
                vendor_id:'',
                description: '',
            }),
            payment_types: [],
            returns: 0,
        }
    },
    emits:['refreshPaymentForm'],
    methods:{
        createPayment(){
            this.loading = true;
            this.paymentData.trans_type = this.trans_type;
            this.paymentData.post('/api/finance/payments')
            .then(response =>{
                this.$emit('refreshPaymentForm', response);
                this.$swal.fire({icon: 'success', title: 'The Payment has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/finance/payments/initials')
            .then(response =>{
                this.banks = response.data.banks;
                this.customers = response.data.customers;
                this.modes = response.data.modes;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Payment Form not loaded successfully',});
            });
            this.loading = false;
        },
        getOrder(){
            this.loading = true;
            axios.get('/api/sales/orders/'+this.order_id)
            .then(response =>{
                this.order = response.data.order;
                this.paymentData.order_id = this.order.id;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Order Details not loaded successfully',});
            });
            this.loading = false;
        },
        updatePayment(){
            this.loading = true;
            this.paymentData.put('/api/finance/payments/'+this.paymentData.id)
            .then(response =>{
                this.$emit('refreshPaymentForm', response);
                this.$swal.fire({icon: 'success', title: 'The Payment has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
            this.loading = false
        },
    },
    mounted() {
        this.getAllInitials();
    },
    props:{
        customer: Object,
        editMode: Boolean,
        order_id: String,
        transaction: Object,
        transactions: Array,
        trans_type: String,
        payment: Object,
    },
    watch:{
        customer(){
            this.paymentData.customer_id = this.customer != null ? this.customer.id : null;
        },
        payment(){
            if (this.payment != null){
                this.paymentData.fill(this.payment);
            }
            else{
                this.paymentData.reset();
            }
        },
        order_id(){
            if (this.order_id != null){
                this.getOrder();
            }
        },
        trans_type(){
            this.paymentData.trans_type = this.trans_type;
        },
    },
}
</script>