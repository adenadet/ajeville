<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="expense-form">
        <form @submit.prevent="submitExpense" class="grid grid-cols-2 gap-4">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">Expense ID</label>
                    <input type="text" v-model="ExpenseData.unique_id" disabled class="form-control" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" v-model="ExpenseData.date"  class="form-control"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" v-model="ExpenseData.due_date" class="form-control"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <select v-model="ExpenseData.branch_id"  class="form-control">
                        <option value="">Select Branch</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Expense Classification:</label>
                    <select class="form-control" name="expense_classification" id="expense_classification" v-model="ExpenseData.classification_id">
                        <option value=''>--Select Classification--</option>
                        <option v-for="expense_classification in expense_types" :value="expense_classification.id">{{ expense_classification.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Expense For</label>
                    <select v-model="ExpenseData.expenseable_type" class="form-control">
                        <option value="">Select Type</option>
                        <option value="vendor">Vendor</option>
                        <option value="customer">Customer</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" v-if="ExpenseData.expenseable_type === 'vendor'">
                <div class="form-group">
                    <label class="form-label">Vendor</label>
                    <select v-model="ExpenseData.vendor_id"  class="form-control">
                        <option value="">Select Vendor</option>
                        <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" v-if="ExpenseData.expenseable_type === 'customer'">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select class="form-control"  v-model="ExpenseData.customer_id">
                    <option value="">Select Customer</option>
                    <option v-for="c in customers" :key="c.id" :value="c.id">
                        {{ c.name }}
                    </option>
                    </select>
                </div>
            </div>

            <div class="col-md-4" v-if="ExpenseData.expenseable_type === 'staff'">
                <div class="form-group">
                    <label class="form-label">Staff</label>
                    <select class="form-control" v-model="ExpenseData.staff_id">
                    <option value="">Select Staff</option>
                    <option v-for="s in staff" :key="s.id" :value="s.id">
                        {{ s.name }}
                    </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Expense Account</label>
                    <select v-model="ExpenseData.account_id" class="form-control">
                        <option value="">Select Account</option>
                        <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.account_name }} {{ a.bank?.bank_name || '' }} [{{a.account_number}}]</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Amount</label>
                    <input type="number" v-model="ExpenseData.amount" class="form-control" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Is Payable?</label>
                    <select v-model="ExpenseData.payable" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <div clas="col-md-6">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select v-model="ExpenseData.status" class="form-control">
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="ExpenseData.description" :class="{'is-invalid' : ExpenseData.errors.has('description') }" />
                    <has-error :form="ExpenseData" field="description"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-group">
                <button class="btn btn-success" @click="editMode ? updateExpenseData() : createExpenseData()">Submit</button>
            </div>
        </div>

        </form>
    </div>

</section>
</template>
<script>
export default {
    data(){
        return  {
            accounts: [],
            branches: [],
            classifications: [],
            customers: [],
            expense_classifications: ['Customer Refund', 'Staff Reimbursement', 'Vendor Payment'],
            expense_types: [],
            ExpenseData: new Form({
                unique_id: '', 
                date: '',
                due_date: '',
                branch_id: '',
                classification_id: '',
                expenseable_type: '',
                expenseable_id: '',
                account_id: '',
                amount: '',
                payable: '',
                vendor_id: '',
                customer_id: '',
                staff_id: '',
                description: '',
                status: '',
            }),
            invoice_types: [],
            loading: false,
            staffs: [],
            trans_sum: 1,
            vendors: [],
        }
    },
    emits: ['reloadExpenseForm'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createExpenseData(){
            this.loading = true;
            this.ExpenseData.post('/api/finance/expenses')
            .then(response =>{
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Expense details has been submited',
                    showConfirmButton: false,
                    timer: 1500
                    });
                this.$emit('reloadExpenseForm');
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
            this.loading = false;        
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/finance/expenses/initials')
            .then(response =>{
                this.customers = response.data.customers;
                this.expense_types = response.data.expense_types;
                this.accounts = response.data.accounts;
                this.branches = response.data.branches;
                this.staffs = response.data.staffs;
                this.vendors = response.data.vendors;
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Expense Form not loaded successfully',
                })
            });
            this.loading = false;
        },
        updateExpenseData(){
            this.loading = true;
            this.ExpenseData.put('/api/finances/expenses/'+this.ExpenseData.id)
            .then(response =>{
                this.$emit('reloadExpenseForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Expense details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = true;            
        },
        getProfilePic(){
            let photo = (this.ExpenseData.image.length >= 150) ? this.ExpenseData.image : "./"+this.ExpenseData.image;
            return photo;
            },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {this.ExpenseData.image = reader.result}
                reader.readAsDataURL(file)
            }
            else{
                Swal.fire({type: 'error', title: 'File is too large'});
            }
        },
    },
    props:{
        editMode: Boolean,
        expense: Object,
    }
}
</script>
