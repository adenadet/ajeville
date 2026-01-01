<template>
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Make Deposit</h3>
        </div>
        <form>
        <div class="card-body">
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
                        <input type="text" required class="form-control" id="amount" name="amount" placeholder="Amount *" v-model="DepositData.amount" :class="{'is-invalid' : DepositData.errors.has('first_name') }" :max="trans_sum">
                        <has-error :form="DepositData" field="amount"></has-error> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Bank</label>
                        <model-list-select class="form-control" :list="banks" v-model="DepositData.bank_id" option-value="id" option-text="account_name" placeholder="Select Bank" required />
                        <has-error :form="DepositData" field="bank_id"></has-error> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Payment Mode*</label>
                        <model-list-select class="form-control" :list="modes" v-model="DepositData.mode_id" option-value="id" option-text="name" placeholder="Select Payment Type" required/>
                        <has-error :form="DepositData" field="bank_id"></has-error> 
                    </div>
                </div>
                <div class="col-md-12"><button :disabled="DepositData.patient_id == null || DepositData.patient_id == '' " @click.prevent="editMode ? updateDepositData() : createDepositData()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button></div>
            </div>
        </div>
        </form>
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
            banks: [],
            DepositData: new Form({
                amount: '',
                mode_id: '',
                bank_id: '',
                patient_id:'', 
                transactions:[],
            }),
            modes: [],
            trans_sum: 0,
            twin: [],
        }
    },
    created() {
        this.getInitials();
        Fire.$on('getPatient', patient_id => {
            this.DepositData.patient_id = patient_id;
        });
        Fire.$on('DepositDataFill', user =>{
            this.DepositData.fill(user); 
            alert(user.transactions.length);
            if (user.transactions != null && user.transactions.length != 0){
                for (let i = 0; i < user.transactions.length; i++){
                    /*var trans = {d: }
                    trans['amount']= user.transactions[i]['amount'];
                    trans['id']= user.transactions[i]['id'];
                    this.DepositData.transactions.push(trans);*/
                    console.log(this.DepositData.transactions);
                }
                this.updateTransSum(this.DepositData.transactions);
            }
        });
        Fire.$on('SetTransactions', transactions =>{
            this.DepositData.transactions = transactions;
            this.updateTransSum(this.DepositData.transactions)
        });
    },
    methods:{
        createDepositData(){
            this.loading = true;
            this.$Progress.start();
            this.DepositData.post('/api/finance/deposits')
            .then(response =>{
                this.$Progress.finish();
                this.loading = false;
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Deposit details has been captured',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.loading = false;
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });
                    
        },
        getInitials(){
            axios.get('/api/finance/deposits/initials')
            .then(response =>{
                this.banks = response.data.banks;
                this.modes = response.data.modes;
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
        },
        updateTransSum(transactions){
            for (let i = 0; i < transactions.length; i++) {this.trans_sum += transactions[i]['amount'];}
        },
        updateDepositData(){
            this.$Progress.start();
            this.DepositData.put('/api/finance/deposits/'+this.DepositData.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });            
        },
    },
    props:{
        editMode: Boolean,
        source: String,
    },
}
</script>
