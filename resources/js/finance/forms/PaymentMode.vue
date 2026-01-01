<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updatePaymentMode() : createPaymentMode()" class="form-horizontal">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Branch</label>
                <select required class="form-control" id="branch_id" name="branch_id" placeholder="First Name *" v-model="PaymentModeData.branch_id" :class="{'is-invalid' : PaymentModeData.errors.has('branch_id') }" >
                    <option value="">Select Branch</option>
                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                </select>
                    <has-error :form="PaymentModeData" field="date"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bank</label>
                <select class="form-control" :list="staffs" v-model="PaymentModeData.collected_by" option-value="id" option-text="name" placeholder="Select Bank" >
                    <option value="">Select Bank</option>
                    <option v-for="bank in banks" :key="bank.id" :value="bank.id">{{ bank.bank_name }}</option>
                </select>
                <has-error :form="PaymentModeData" field="bank_id"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Account Name</label>
                <input type="text" required class="form-control" id="account_name" name="account_name" placeholder="Amount *" v-model="PaymentModeData.account_name" :class="{'is-invalid' : PaymentModeData.errors.has('account_name') }" >
                <has-error :form="PaymentModeData" field="account_name"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Account Number</label>
                <input type="text" required class="form-control" id="account_number" name="account_number" v-model="PaymentModeData.account_number"   :class="{'is-invalid' : PaymentModeData.errors.has('account_number') }"/>
                <has-error :form="PaymentModeData" field="account_number"></has-error> 
            </div>
        </div>
        <div class="col-md-12">
            <button @click.prevent="editMode ? updatePaymentModeData() : createPaymentModeData()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            banks: [],
            branches: [],
            PaymentModeData: new Form({
                account_name: '',
                account_number: '',
                bank_id: '',
                branch_id: '',
                status: '',
            }),
        }
    },
    emits: ['refreshPaymentMode'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createPaymentMode(){
            this.loading = true;
            this.PaymentModeData.post('/api/finance/branch_accounts')
            .then(response =>{
                this.$emit('refreshPaymentMode', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Payment Mode detail has been captured',
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
            axios.get('/api/finance/branch_accounts/initials')
            .then(response =>{
                this.banks = response.data.banks;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Payment Mode Form not loaded successfully',})
            });
            this.loading = false;
        },
        updatePaymentMode(){
            this.loading = true;
            this.PaymentModeData.put('/api/finance/branch_accounts/'+this.PaymentModeData.id)
            .then(response =>{
                this.$emit('refreshPaymentMode', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Payment Mode detail has been updated',
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
        source: String,
    },
}
</script>
