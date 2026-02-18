<template>
<section class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Pending Transactions</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"/></th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Service Name</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="transaction in trans" :key="transaction.id">
                            <td><input type="checkbox" :value="transaction.id" v-model="DepositData.transactions" @change="calculateSelectedTotal" /></td>
                            <td>{{ ExcelDate(transaction.date) }}</td>
                            <td>{{ transaction.service_type?.name }}</td>
                            <td>{{ transaction.item_name }}</td>
                            <td>{{ transaction.item_total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Details</h3>
            </div>
            <form>
            <div class="card-body overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <div class="row" v-if="source != null && source == 'third_party'">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" required class="form-control" id="date" name="date" placeholder="First Name *" v-model="DepositData.date" :class="{'is-invalid' : DepositData.errors.has('date') }" >
                            <has-error :form="DepositData" field="date"></has-error> 
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Staff</label>
                            <model-list-select class="form-control" :list="staffs" v-model="DepositData.collected_by" option-value="id" option-text="name" placeholder="Select Bank" />
                            <has-error :form="DepositData" field="collected_by"></has-error> 
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Amount *</label>
                            <input type="number" step="0.01" required class="form-control" id="amount" name="amount" placeholder="Amount *" v-model="DepositData.amount" :class="{'is-invalid' : DepositData.errors.has('amount') }" :min="trans_sum">
                            <has-error :form="DepositData" field="amount"></has-error> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Payment Mode*</label>
                            <model-list-select class="form-control" :list="modes" v-model="DepositData.mode_id" option-value="id" option-text="name" placeholder="Select Payment Type" required/>
                            <has-error :form="DepositData" field="mode_id"></has-error> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Bank</label>
                            <model-list-select class="form-control" :list="banks" v-model="DepositData.bank_id" option-value="id" option-text="name" placeholder="Select Bank" />
                            <has-error :form="DepositData" field="bank_id"></has-error> 
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Details</label>
                            <QuillEditor class="form-control" theme="snow" content-type="html" v-model:content="DepositData.notes" placeholder="Payment details" required/>
                            <has-error :form="DepositData" field="description"></has-error> 
                        </div>
                    </div>
                    <div class="col-md-12"><button @click.prevent="editMode ? updateDepositData() : createDepositData()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button></div>
                </div>
            </div>
            </form>
        </div>
    </div>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data(){
        return  {
            amountError: '',
            banks: [],
            DepositData: new Form({
                amount: 0,
                bank_id: '',
                mode_id: '',
                notes: '',
                patient_id:'', 
                received_by: '',
                received_at: '',
                transactions:[],
            }),
            loading: false,
            modes: [],
            selectAll: false,
            trans_sum: 0,
            trans: [],
            transactions: [], 
        }
    },
    mounted() {
        this.getInitials();
        this.getPendingTransactions();
    },
    methods:{
        calculateSelectedTotal(){
            let total = 0;
            this.trans.forEach(t => {
                if(this.DepositData.transactions.includes(t.id)){
                    total += parseFloat(t.item_total);
                }
            });
            this.trans_sum = total;
            
            if(!this.DepositData.amount || this.DepositData.amount < total){
                this.DepositData.amount = total;
            }
        },
        createDepositData(){
            this.loading = true;
            this.validateAmount();
            if(this.amountError){
                this.$swal.fire(this.amountError);
                return;
            }
            this.DepositData.patient_id = this.patient.id;
            this.DepositData.visit_id = this.visit.id;
            this.DepositData.post('/api/emr/finance/payments')
            .then(response =>{
                //Fire.$emit('Reload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Deposit details has been captured',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        getInitials(){
            axios.get('/api/finance/deposits/initials')
            .then(response =>{
                this.banks = response.data.banks;
                this.modes = response.data.modes;
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
        },
        getPendingTransactions(){
            this.loading = true;
            axios.get('/api/emr/hims/visit_transactions/'+this.patient.id+'/pending')
            .then(response =>{
                this.trans = response.data.transactions;
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
            this.loading = false;
        },
        resetForm(){
            this.DepositData.reset();
            this.DepositData.transactions = [];
            this.trans_sum = 0;
        },
        toggleSelectAll(){
            if(this.selectAll){
                this.DepositData.transactions = this.trans.map(t => t.id);
            } else {
                this.DepositData.transactions = [];
            }
        },
        updateDepositData(){
            this.loading = true;
            this.DepositData.put('/api/finance/deposits/'+this.DepositData.id)
            .then(response =>{
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });            
        },
        updateTransSum(transactions){
            for (let i = 0; i < transactions.length; i++) {this.trans_sum += transactions[i]['amount'];}
        },
        validateAmount(){
            if(this.DepositData.amount < this.trans_sum){
                this.amountError = "Amount cannot exceed selected total";
                return;
            }

            if(this.DepositData.amount <= 0){
                this.amountError = "Amount must be greater than zero";
                return;
            }

            this.amountError = null;
        },
    },
    props:{
        editMode: Boolean,
        source: String,
    },
    watch: {
        'DepositData.transactions'(val){
            if(val.length === this.trans.length && this.trans.length > 0){
                this.selectAll = true;
            } else {
                this.selectAll = false;
            }
            this.calculateSelectedTotal();
        }
    }
}
</script>
