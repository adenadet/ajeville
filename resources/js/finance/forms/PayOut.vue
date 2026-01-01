<template>
<section class="overly-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row p-0 m-0">
        <div :class="source == 'expense' && expense != null ? 'col-md-4' : ''">
            <FinanceDetailExpense :expense.sync="expense" v-if="expense != null && expense.id != null"/>
        </div>
        <div :class="source == 'expense' && expense != null ? 'col-md-8' : 'col-md-12'">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                <form role="form" @submit.prevent="editMode ? updatePayment() :createPayment()">
                    <div class="card-body">
                        <div class="row p-0" v-if="expense == null">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pay Out Type</label>
                                    <select class="form-control" id="payout_type" name="payout_type" v-model="paymentData.payout_type">
                                        <option value="">---Select Payment Type---</option>
                                        <option value="Customer">Customer Refund</option>
                                        <option value="Staff">Staff Reimbursement</option>
                                        <option value="Vendor">Vendor Payment</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment To:</label>
                                    <select class="form-control" id="customer_id" name="customer_id" v-model="paymentData.customer_id" v-if="paymentData.payout_type == 'Customer'">
                                        <option value="">--Select Customer--</option>
                                        <option v-for="customer in customers" :value="customer.id">{{ customer.name }}</option>
                                    </select>
                                    <select class="form-control" id="staff_id" name="staff_id" v-model="paymentData.vendor_id" v-if="paymentData.payout_type == 'Vendor'">
                                        <option value="">--Select Staff--</option>
                                        <option v-for="vendor in vendors" :value="vendor.id">{{ vendor.name }}</option>
                                    </select>
                                    <select class="form-control" id="vendor_id" name="vendor_id" v-model="paymentData.vendor_id" v-if="paymentData.payout_type == 'Vendor'">
                                        <option value="">--Select Vendor--</option>
                                        <option v-for="vendor in vendors" :value="vendor.id">{{ vendor.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row p-0" v-else>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pay Out Type</label>
                                    <div class="form-control">{{ expense.category }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment To</label>
                                    <div class="form-control">
                                        <p v-if="expense.category == 'Customer'">{{ expense.customer.name }}
                                            <input type="hidden" name="customer_id" id="customer_id" v-model="expense.customer_id" />
                                        </p>
                                        <p v-if="expense.category == 'Staff'">{{ FullName(expense.staff.user) }}
                                            <input type="hidden" name="staff_id" id="staff_id" v-model="expense.staff_id" />
                                        </p>
                                        <p v-if="expense.category == 'Vendor'">{{ expense.vendor.name }} 
                                            <input type="hidden" name="vendor_id" id="vendor_id" v-model="expense.vendor_id" />
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Receiving Account</label>
                                    <select class="form-control" v-model="paymentData.receiving_account_id">
                                        <option value=''>--Select Account Paid To--</option>
                                        <option v-if="expense.category == 'Customer'" v-for="account in expense.customer.accounts" :value="account.id">{{ account.bank.name }} [ {{ account.account_number }}]</option>
                                        <option v-else-if="expense.category == 'Staff'" v-for="account in expense.staff.accounts" :value="account.id">{{ account.bank.name }} [ {{ account.account_number }}]</option>
                                        <option v-else-if="expense.category == 'Vendor' && expense.vendor.accounts != null" v-for="account in expense.vendor.accounts" :value="account.id">{{ account.bank.name }} [ {{ account.account_number }}]</option>
                                        <option value=-1>New Account</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row p-0" v-if="paymentData.receiving_account_id == -1">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Bank</label>
                                    <select class="form-control" v-model="paymentData.receiving_account.bank_id" name="receiving_account.bank_id" id="receiving_account.bank_id">
                                        <option value="">--Select Bank--</option>
                                        <option v-for="bank in banks" :value="bank.id">{{ bank.bank_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Name</label>
                                    <input type="text" class="form-control" v-model="paymentData.receiving_account.account_name" name="receiving_account.account_name" id="receiving_account.account_name" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" class="form-control" v-model="paymentData.receiving_account.account_number" name="receiving_account.account_number" id="receiving_account.account_number" />
                                </div>
                            </div>
                        </div>
                        <div class="row p-0">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Payment Date</label>
                                    <input class="form-control" type="date" id="date" name="date" v-model="paymentData.date" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Account</label>
                                    <select class="form-control" id="account_id" name="account_id" v-model="paymentData.account_id">
                                        <option value="">--Select Account--</option>
                                        <option v-for="account in accounts" :value="account.id">{{ account.account_name }} [{{ account.bank.bank_name }} - {{ account.account_number }}]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputFile">Amount</label>
                                    <input type="number" class="form-control" id="amount" v-model="paymentData.amount">
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
export default {
    data(){
        return {
            accounts: [],
            banks: [],
            current_page: 1,
            customers: [],
            expense: {},
            items: 0,
            loading: false,
            modes: [],
            paymentData: new Form({
                id: '',
                account_id: '',
                amount: '',
                customer_id: '',
                description: '',
                expense_id: '',
                payment_mode: '',
                staff_id: '',
                vendor_id: '',
                receiving_account_id: '',
                receiving_account: {
                    bank_id: '',
                    account_name: '',
                    account_number: '', 
                }
            }),
            payment_types: [],
            returns: 0,
        }
    },
    emits:['refreshPayOutForm'],
    methods:{
        createPayment(){
            this.loading = true;
            this.paymentData.post('/api/finance/pay_outs')
            .then(response =>{
                this.$emit('refreshPayOutForm', response);
                this.$swal.fire({icon: 'success', title: 'The Payment has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/finance/pay_outs/initials')
            .then(response =>{
                this.accounts = response.data.accounts;
                this.banks = response.data.banks;
                this.customers = response.data.customers;
                this.staffs = response.data.staffs;
                this.modes = response.data.modes;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Payment Form not loaded successfully',});
            });
            this.loading = false;
        },
        getExpense(){
            this.loading = true;
            axios.get('/api/finance/expenses/'+this.expense_id)
            .then(response =>{
                this.expense = response.data.expense;
                this.paymentData.customer_id = this.expense.customer_id;
                this.paymentData.expense_id = this.expense.id;
                this.paymentData.staff_id = this.expense.staff_id;
                this.paymentData.vendor_id = this.expense.vendor_id;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Expense Details not loaded successfully',});
            });
            this.loading = false;
        },
        updatePayment(){
            this.loading = true;
            this.paymentData.put('/api/finance/pay_outs/'+this.paymentData.id)
            .then(response =>{
                this.$emit('refreshPayOutForm');
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
        editMode: Boolean,
        expense_id: Number,
        payout: Object,
        source: String,
    },
    watch:{
        expense_id(){
            this.getExpense();
        }
    },
}
</script>