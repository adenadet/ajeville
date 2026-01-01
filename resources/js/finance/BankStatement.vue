<template>
<section class="overlay-wrapper">
    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Query</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Bank Account</label>
                            <select class="form-control" id="account_id" name="account_id" v-model="reportData.account_id">
                                <option value="">--Select Bank Account---</option>
                                <option v-for="account in accounts" :value="account.id">{{ account.bank.name+' ['+ account.account_number+']' }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Report Type</label>
                            <select class="form-control" id="report_type_id" name="report_type_id" v-model="reportData.report_type_id">
                                <option value="">--Select Report Type---</option>
                                <option v-for="report_type in report_types" :value="report.id">{{ report.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputFile">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" v-model="reportData.start_date" placeholder="Start Date">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputFile">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" v-model="reportData.end_date" placeholder="End Date">
                        </div>
                        <button class="btn btn-sm btn-primary" type="button" @click="runReport">Run Report</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">

        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            accounts: [],
            loading: false,
            report_types: [],
            reportData: new Form({
                account_id: '',
                report_type_id: '',
                start_date: '',
                end_date: '',
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
        runReport(){
            this.loading = true;
            this.reportData.post('/api/finance/branch_accounts/reports')
            .then(response =>{

            })
            .catch(() => {

            })
            this.loading = false;
        }
    },
}
</script>