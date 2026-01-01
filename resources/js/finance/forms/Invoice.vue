<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateInvoice() : createInvoice()" class="row form-horizontal">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Branch</label>
                <select required class="form-control" id="branch_id" name="branch_id" placeholder="Branch Name *" v-model="invoiceData.branch_id" :class="{'is-invalid' : invoiceData.errors.has('branch_id') }" >
                    <option value="">Select Branch</option>
                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                </select>
                <has-error :form="invoiceData" field="branch_id"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Vendor</label>
                <select class="form-control" id="bank_id" name="bank_id" v-model="invoiceData.vendor_id" placeholder="Select Bank" >
                    <option value="">Select Bank</option>
                    <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">{{ vendor.name }}</option>
                </select>
                <has-error :form="invoiceData" field="bank_id"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Expense Type</label>
                <select required class="form-control" id="expense_type_id" name="expense_type_id" placeholder="Branch Name *" v-model="invoiceData.expense_type_id" :class="{'is-invalid' : invoiceData.errors.has('expense_type_id') }" >
                    <option value="">--Select Expense Type--</option>
                    <option v-for="expense_type in expense_types" :key="expense_type.id" :value="expense_type.id">{{ expense_type.name }}</option>
                </select>
                <has-error :form="invoiceData" field="expense_type_id"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Invoice Number</label>
                <input type="text" class="form-control" id="invoice_number" name="invoice_number" v-model="invoiceData.invoice_number"  placeholder="Enter Date" :class="{'is-invalid' : invoiceData.errors.has('invoice_number') }" />
                <has-error :form="invoiceData" field="invoice_number"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Date</label>
                <input type="date" class="form-control" id="date" name="date" v-model="invoiceData.date"  placeholder="Enter Date" :class="{'is-invalid' : invoiceData.errors.has('date') }" />
                <has-error :form="invoiceData" field="date"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" v-model="invoiceData.due_date" placeholder="Enter payment Due Date" :class="{'is-invalid' : invoiceData.errors.has('due_date') }" >
                <has-error :form="invoiceData" field="due_date"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" required class="form-control" id="amount" name="amount" placeholder="Account Name *" v-model="invoiceData.amount" :class="{'is-invalid' : invoiceData.errors.has('amount') }" >
                <has-error :form="invoiceData" field="amount"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Description</label>
                <QuillEditor content-type="html" theme="snow" required placeholder="Explain this invoice" class="form-control" id="description" name="description" v-model:content="invoiceData.description" :class="{'is-invalid' : invoiceData.errors.has('description') }">
                </QuillEditor>
                <has-error :form="invoiceData" field="description"></has-error> 
            </div>
        </div>
        <div class="col-md-12">
            <button @click.prevent="editMode ? updateInvoice() : createInvoice()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button>
        </div>
    </form>
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data(){
        return  {
            banks: [],
            branches: [],
            expense_types: [],
            invoiceData: new Form({
                id: '',
                amount: '',
                branch_id: '',
                date: '',
                description: '',
                due_date: '',
                expense_type_id: '',
                invoice_file: '', 
                invoice_number: '',
                status: '',
                unique_id: '',
                vendor_id: '',
            }),
            loading: false,
            vendors: [],
        }
    },
    emits: ['refreshInvoiceForm'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createInvoice(){
            this.loading = true;
            this.invoiceData.post('/api/finance/invoices')
            .then(response =>{
                this.$emit('refreshInvoiceForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Invoice detail has been captured',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;    
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/finance/invoices/initials')
            .then(response =>{
                this.branches = response.data.branches;
                this.expense_types = response.data.expense_types;
                this.vendors = response.data.vendors;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Invoice Form not loaded successfully',})
            });
            this.loading = false;
        },
        updateInvoice(){
            this.loading = true;
            this.invoiceData.put('/api/finance/invoices/'+this.invoiceData.id)
            .then(response =>{
                this.$emit('refreshInvoiceForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Invoice detail has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;            
        },
    },
    props:{
        editMode: Boolean,
        invoice: Object,
    },
    watch:{
        invoice(){
            console.log(this.invoice);
            this.invoiceData.reset();
            this.invoiceData.fill(this.invoice);
        }
    }
}
</script>