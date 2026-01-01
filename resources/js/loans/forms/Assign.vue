<template>
<section>
    <form @submit.prevent="assignLoan">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label >Loan </label>
                    <div class="form-control">{{loan_data.amount | currency}} loan for {{loan_data.user | fullName  }}</div>
                    <input type="hidden" name="loan_id" id="loan_id" v-model="loanAssignData.account_id" />
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Staff</label>
                    <select class="form-control" name="staff_id" id="staff_id" v-model="loanAssignData.staff_id" required>
                        <option value="">--Select Staff--</option>
                        <option v-for="user in users" :value="user.id" :key="user.id"> {{user | FullName}} </option>
                    </select>  
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm" >Confirm</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return {
            form: new Form({}),
            loanAssignData: new Form({
                account_id: '',
                staff_id: '',
            }),
            users: [],
            loan_data: {},
        }
    },
    methods:{
        assignLoan(){
            this.loading = true;
            this.loanAssignData.account_id = this.loan_data.id;
            this.loanAssignData.post('/api/loans/account_officers')
            .then(response =>{
                this.loading = false;
                //Fire.$emit('Reload', response);
                //this.RescheduleData.reset();
                this.$swal.fire({icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500});})
            .catch({})
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/ums/staffs/full_list')
            .then(response =>{
                this.reloadStaffs(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Staffs not loaded successfully',});
            });
        },
        reloadStaffs(response){
            this.users = response.data.users;
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('LoanAssignDataFill', loan => {
            this.loan_data = loan;
        });
    },
    props:{
        loan: Object,
    }
}
</script>