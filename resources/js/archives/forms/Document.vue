<template>
<section>
    <form>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" v-model="documentData.category_id" required>
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :value="category.id" :key="category.id">{{category.name}}</option>
                    </select>
                    <has-error :form="documentData" field="category_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" v-model="documentData.category_id" required>
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :value="category.id" :key="category.id">{{category.name}}</option>
                    </select>
                    <has-error :form="documentData" field="category_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" required class="form-control" id="date" name="date" placeholder="First Name *" v-model="documentData.date" :class="{'is-invalid' : documentData.errors.has('date') }" >
                    <has-error :form="documentData" field="date"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Staff</label>
                    <model-list-select class="form-control" :list="staffs" v-model="documentData.collected_by" option-value="id" option-text="name" placeholder="Select Bank" />
                    <has-error :form="documentData" field="collected_by"></has-error> 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Amount *</label>
                    <input type="text" required class="form-control" id="amount" name="amount" placeholder="Amount *" v-model="documentData.amount" :class="{'is-invalid' : documentData.errors.has('first_name') }" :max="trans_sum">
                    <has-error :form="documentData" field="amount"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Bank</label>
                    <model-list-select class="form-control" :list="banks" v-model="documentData.bank_id" option-value="id" option-text="account_name" placeholder="Select Bank" required />
                    <has-error :form="documentData" field="bank_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Payment Mode*</label>
                    <model-list-select class="form-control" :list="modes" v-model="documentData.mode_id" option-value="id" option-text="name" placeholder="Select Payment Type" required/>
                    <has-error :form="documentData" field="bank_id"></has-error> 
                </div>
            </div>
            <div class="col-md-12"><button :disabled="documentData.patient_id == null || documentData.patient_id == '' " @click.prevent="editMode ? updatedocumentData() : createdocumentData()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button></div>
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
            documentData: new Form({
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
            this.documentData.patient_id = patient_id;
        });
        Fire.$on('documentDataFill', user =>{
            this.documentData.fill(user); 
            alert(user.transactions.length);
            if (user.transactions != null && user.transactions.length != 0){
                for (let i = 0; i < user.transactions.length; i++){
                    /*var trans = {d: }
                    trans['amount']= user.transactions[i]['amount'];
                    trans['id']= user.transactions[i]['id'];
                    this.documentData.transactions.push(trans);*/
                    console.log(this.documentData.transactions);
                }
                this.updateTransSum(this.documentData.transactions);
            }
        });
        Fire.$on('SetTransactions', transactions =>{
            this.documentData.transactions = transactions;
            this.updateTransSum(this.documentData.transactions)
        });
    },
    methods:{
        createdocumentData(){
            this.loading = true;
            this.$Progress.start();
            this.documentData.post('/api/archives/documents')
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
        updatedocumentData(){
            this.$Progress.start();
            this.documentData.put('/api//archives/documents/'+this.documentData.id)
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
