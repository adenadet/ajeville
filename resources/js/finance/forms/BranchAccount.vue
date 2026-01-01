<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateBranchAccount() : createBranchAccount()" class="form-horizontal">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Branch</label>
                <select required class="form-control" id="branch_id" name="branch_id" placeholder="Branch Name *" v-model="BranchAccountData.branch_id" :class="{'is-invalid' : BranchAccountData.errors.has('branch_id') }" >
                    <option value="">Select Branch</option>
                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                </select>
                    <has-error :form="BranchAccountData" field="date"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bank</label>
                <select class="form-control" id="bank_id" name="bank_id" v-model="BranchAccountData.bank_id" placeholder="Select Bank" >
                    <option value="">Select Bank</option>
                    <option v-for="bank in banks" :key="bank.id" :value="bank.id">{{ bank.bank_name }}</option>
                </select>
                <has-error :form="BranchAccountData" field="bank_id"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Account Name</label>
                <input type="text" required class="form-control" id="account_name" name="account_name" placeholder="Account Name *" v-model="BranchAccountData.account_name" :class="{'is-invalid' : BranchAccountData.errors.has('account_name') }" >
                <has-error :form="BranchAccountData" field="account_name"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Account Number</label>
                <input type="text" required  placeholder="Account Number *" class="form-control" id="account_number" name="account_number" v-model="BranchAccountData.account_number"   :class="{'is-invalid' : BranchAccountData.errors.has('account_number') }"/>
                <has-error :form="BranchAccountData" field="account_number"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Status</label>
                <select required class="form-control" id="status" name="status" v-model="BranchAccountData.status"   :class="{'is-invalid' : BranchAccountData.errors.has('status') }">
                    <option value="">--Select Status--</option>
                    <option value=1>Active</option>
                    <option value=0>Inactive</option>
                </select>
                <has-error :form="BranchAccountData" field="status"></has-error> 
            </div>
        </div>
        <div class="col-md-12">
            <button @click.prevent="editMode ? updateBranchAccount() : createBranchAccount()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button>
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
            BranchAccountData: new Form({
                id: '',
                account_name: '',
                account_number: '',
                bank_id: '',
                branch_id: '',
                status: '',
            }),
        }
    },
    emits: ['refreshBranchAccount'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createBranchAccount(){
            this.loading = true;
            this.BranchAccountData.post('/api/finance/branch_accounts')
            .then(response =>{
                this.$emit('refreshBranchAccount', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch Account detail has been captured',
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
                this.branches = response.data.branches;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Branch Account Form not loaded successfully',})
            });
            this.loading = false;
        },
        updateBranchAccount(){
            this.loading = true;
            this.BranchAccountData.put('/api/finance/branch_accounts/'+this.BranchAccountData.id)
            .then(response =>{
                this.$emit('refreshBranchAccount', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch Account detail has been updated',
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
        branch_account: Object,
        editMode: Boolean,
        source: String,
    },
    watch:{
        branch_account(){
            this.BranchAccountData.reset();
            this.BranchAccountData.fill(this.branch_account);
        }
    }
}
</script>