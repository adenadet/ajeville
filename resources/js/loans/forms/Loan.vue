<template>
<section class="row overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="col-md-6">
        <div class="row">
            <form @submit.prevent="editMode ? updateLoan() : createLoan() ">
                <alert-error :form="loanData"></alert-error> 
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label>Loan Type*</label>
                            <select class="form-control" id="name" name="name" placeholder="Name *" v-model="loanData.loan_type_id" required>
                                <option value=''>---Select Loan Type---</option>
                                <option v-for="(loan_type, index) in loan_types" :value="loan_type.ProductCode" :key="index">{{ loan_type.ProductName }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label>Purpose</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name *" v-model="loanData.name" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount *" v-model.number="loanData.amount" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Payback Freq.</label>
                            <select class="form-control" id="frequency" name="frequency" placeholder="frequency *" v-model.number="loanData.frequency" required>
                                <option value="">--Select Payback Frequency--</option>
                                <option value="weeks">Weekly</option>
                                <option value="months">Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Duration</label>
                            <input type="number" class="form-control" id="duration" name="duration" placeholder="Duration *" v-model.number="loanData.duration" required>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label>Bank</label>
                            <select class="form-control" id="bank_id" name="bank_id" v-model="loanData.bank_id" required>
                                <option value=''>--Select Bank To Pay To--</option>
                                <option v-for="(bank, index) in banks" :value="bank.bank_code != null ? bank.bank_code : bank.BankCode" :key="index">{{ bank.bank_name != null ? bank.bank_name : bank.bank_code }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" class="form-control" id="acct_name" name="acct_name" placeholder="Account Name *" v-model="loanData.acct_name" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Account Number (NUBAN)</label>
                            <input type="text" class="form-control" id="acct_number" name="acct_number" placeholder="Account Number *" v-model="loanData.acct_number" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Signature Type</label>
                            <select class="form-control" id="signature_type" name="signature_type" placeholder="Name *" v-model="loanData.signature_type" required>
                                <option value=''>--Select Signature Type--</option>
                                <option value="upload">Upload Signature Image</option>
                                <option value="manual">Sign Manually</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group" v-if="loanData.signature_type == 'manual'">
                            <label>Signature</label>
                            <VueSignaturePad :options="options" class="signature" ref="signaturePad" required />
                            <button @click="undo" class="btn btn-sm btn-default">Undo</button>
                        </div>
                        <div class="form-group" v-else>
                            <label>Signature</label>
                            <input type="file" class="form-control" name="signature" id="signature" required @change="updateSignature"/>
                        </div>
                    </div>
                </div>
                <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Principal</th>
                    <th>Interest</th>
                    <th>Repayment</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(n, index) in loanData.duration" :key="index">
                    <td>{{ addOne(index)  }}.</td>
                    <td>{{ currency(loanData.amount / loanData.duration)}}</td>
                    <td>{{ currency(emi.toFixed(2) - ((loanData.amount / loanData.duration).toFixed(2)))}}</td>
                    <td>{{ currency(emi.toFixed(2))}}</td>
                </tr>
            </tbody>
        </table>
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
export default {
    computed:{
        adminInterestRate(){
            let getIndex = this.loan_types.map(object => object.id).indexOf(this.loanData.loan_type_id);
            let interest = 36;
            return 1;
        },
        tenureMonths(){return Number(this.loanData.duration);},
        emi(){
            var emiMonthly = this.newTotalPayment / this.loanData.duration;
            return Number(emiMonthly);
        },
        totalPayment(){return Number(this.emi * this.tenureMonths);},
        totalInterest(){return Number(this.totalPayment - this.loanAmount);},
        newTotalPayment(){
            var tenure = this.loanData.frequency == "weeks" ? (this.loanData.duration/52) : (this.loanData.duration/12);
            return Number(this.loanData.amount * (1 + (0.66 * tenure)));
        }
    },
    data(){
        return {
            banks: [],
            loading: false,
            loan_types: [],
            loanData: new Form({
                acct_name: '', 
                acct_number: '',
                amount: '', 
                bank_id: '',
                duration: '', 
                emi: '', 
                frequency: '', 
                id:'',
                loan_type_id: '', 
                payable: '',
                signature: '',
                unique_id: '', 
                user_id: '',
            }),
            options:{
                penColor:"rgb(0,0,255)",
                backgroundColor:"rgb(255,255,255)",
            },
            pad: 0,
        }
    },
    emits: ['getGuarantors', 'refreshLoan'],
    methods:{
        change() {this.options = {penColor: "#00f",};},
        createLoan(){
            this.loading = true;
            this.loanData.emi = this.emi;
            alert(this.loanData.bank_id);
            if (this.loanData.signature_type == 'manual'){this.save();}
            this.loanData.post('/api/loans/accounts')
            .then(response=>{
                this.loading = false;
                this.$swal.fire({icon: 'success', title: response.data.message,});
                this.$emit('getGuarantors', response);
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/loans/accounts/initials').then(response =>{
                this.loading = false;
                this.banks = response.data.all_banks;
                this.loan_types = response.data.loan_types;
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Loan Form Initialization failed',})
            });
        },
        resume() {this.options = {penColor: "#00f",};},
        save() {
            const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
            this.loanData.signature = data;
        },
        undo() {
                this.$refs.signaturePad.undoSignature();
        },
        updateLoan(id){
            this.loading = true;
            this.loanData.put('/api/loans/accounts/'+this.loanData.id)
            .then(response=>{
                this.loading = false;
                this.$swal.fire({icon: 'success', title: response.data.message,});
                this.$emit('refreshLoan', response);
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Your form was not sent try again later!',});
            });
        },
        updateSignature(e){
            let file = e.target.files[0];
            let image_types = ['image/bmp', 'image/jpg', 'image/jpeg', 'image/png']; 
            let reader = new FileReader();
            if (file['size'] >= 2000000) {this.$swal.fire({icon: 'error', title: 'File is too large'});}
            else if (!(image_types.includes(file['type']))){this.$swal.fire({icon: 'error', title: 'File is not an image'});}
            else if (file['size'] < 2000000){
                reader.onloadend = (e) => {this.loanData.signature = reader.result;}
                reader.readAsDataURL(file)
            }
            else{
                this.$swal.fire({type: 'error', title: 'File is too large'});
            }
        }, 
    },
    mounted() {
        this.getInitials();   
    },
    props: {
        editMode: Boolean,
        loan: Object,
    },
    watch:{
        loan(){
            this.loanData.fill(this.loan);
        }
    }
}
</script>
<style>
.signature {
	border: double 3px transparent;
	border-radius: 5px;
	background-image: linear-gradient(white, white), radial-gradient(circle at top left, #4bc5e8, #9f6274);
	background-origin: border-box;
	background-clip: content-box, border-box;
	height: 300px;
}

.container {
  width: "100%";
  padding: 8px 16px;
}

.buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-top: 8px;
}
</style>
