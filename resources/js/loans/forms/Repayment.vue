<template>
<section>
    <div class="card custom-card"> 
        <div class="card-body"> 
            <form> 
                <div class="mb-3">
                    <label class="form-label">Loan </label> 
                    <input type="hidden" class="form-control" id="loan_id" name="loan_id" v-model="repaymentData.loan_id"> 
                    <div class="form-control">Loan Name - Loan ID [Balance]</div>
                </div>
                <div class="mb-3"> 
                    <label class="form-label">Amount</label> 
                    <input type="number" class="form-control" id="amount" name="amount" v-model="repaymentData.amount"> 
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> 
                </div> 
                <div class="mb-3"> 
                    <label class="form-label">Payment Method</label> 
                    <select class="form-control" id="payment_mode_id" name="payment_mode_id" v-model="repaymentData.payment_mode_id">
                        <option value=''>Select Payment Method</option>
                        <option value=1>PayStack</option>
                        <option value=2>FlutterWave</option>
                        <option value=3>Bank Deposit/Transfer</option>
                    </select> 
                </div>
                <div class="mb-3" v-show="repaymentData.payment_mode_id == 2">
                    Coming Soon
                </div> 
                <div v-show="repaymentData.payment_mode_id == 3"> 
                    <div class="mb-3">
                        <label class="form-label">Bank</label> 
                        <select class="form-control" id="bank_id" name="bank_id" v-model="repaymentData.bank_id"> 
                            <option value=''>--Select Account Paid To--</option>
                            <option v-for="bank in banks" :value="bank.id">{{ bank.bank_name+' | '+bank.number }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label> 
                        <input type="date" class="form-control" id="payment_date" name="payment_date" v-model="repaymentData.payment_date" :max="today"/> 
                    </div>
                    <div class="mb-3"> 
                        <label class="form-label">Any other Detail</label> 
                        <textarea row="5" class="form-control" id="description" name="description" v-model="repaymentData.description">
                        </textarea> 
                    </div> 
                    
                </div>
            </form> 
        </div> 
    </div>
</section>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
//import paystack from 'vue-paystack';
export default {
    component: {
        //paystack
    },
    data(){
        return {
            areas:[],
            banks:[],
            today: '',
            departments:[],
            editMode: false,
            savings:{},
            states:[],
            policy:{},
            policies:{},
            PUBLIC_KEY: "pk_test_ebc34a2431d5145c9dd5909f8745127a60d616f0",
            repaymentData: new Form({
                id: '',
                loan_id: '',
                amount: '',
                payment_mode_id: '',
                bank_id: '',
                payment_date: '',
                description: '',
            }),
        }
    },
    methods:{
        close: function(){
            console.log("Payment closed")
        },
        createRepayment(){
            this.loading = true;
            this.repaymentData.post('/api/loans/repayments')
            .then(response =>{
                this.loading = false;
                this.$emit('refreshRepayment', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Repayment details has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        genRef(){return "Task_"+ new Date().valueOf();},
        processRepayment(response){
            if (response.message == "Approved"){
                this.repaymentData.payment_mode_id = "1";
                this.repaymentData.description = response.reference+' | '+response.transaction;
                //this.createApplicant();
            }
            else{
                alert("Payment has to be made to confirm booking");
            }
        },
        processBooking(){
            console.log(response);
            this.RescheduleData.payment_method = "Holding";
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/repayments/initials').then(response =>{
                this.banks = response.data.banks;
                this.channels = response.data.channels;
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Repayments loaded successfully',
                });
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Repayments were not loaded successfully',
                });
                this.loading = false;
            });
        },
        getPolicy(page=1){
            axios.get('/api/policies/all/departmental?page='+page)
            .then(response=>{
                this.Policys = response.data.Policys;   
            });
        },
        updateRepayment(){
            this.$Progress.start();
            this.repaymentData.put('/api/loans/repayments/'+this.repaymentData.id)
            .then(response =>{
                this.$Progress.finish();
                this.$emit('refreshRepayment', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Repayment details has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            this.$Progress.fail();
            });
        },
    },
    mounted() {
        this.getAllInitials();
        const date = new Date();
        let currentDay= String(date.getDate()).padStart(2, '0');
        let currentMonth = String(date.getMonth()+1).padStart(2,"0");
        let currentYear = date.getFullYear();
        this.today = `${currentDay}-${currentMonth}-${currentYear}`;
        /*Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('/api/ums/Policys/search?q='+query)
            .then((response ) => {
                this.Policys = response.data.Policys;
            })
            .catch(()=>{

            });
        });
        Fire.$on('Reload', response =>{
            $('#PolicyModal').modal('hide');
            this.Policys = response.data.Policys;
        });*/
    },
    props:{

    }
}
</script>