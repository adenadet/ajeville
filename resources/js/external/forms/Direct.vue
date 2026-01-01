<template>
<div class="card card-primary ">
    <div class="modal fade" id="termsModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy"><h4 class="modal-title">Terms and Conditions</h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true" class="text-white">&times;</span></button></div>
                <div class="modal-body">
                    <ExternalDetailPolicy />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-navy">Schedule An Appointment</div>
    <div class="card-body">
        <form class="overlay-wrapper" method="POST" @submit.prevent="processAppointment">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <alert-error :form="ApplicantData"></alert-error> 
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Service</label>
                        <select class="form-control" id="service_id" name="service_id" v-model="ApplicantData.service_id" required>
                            <option value="">--Select Service--</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">{{service.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Appointment Date</label>
                        <input class="form-control" type="date" name="date" id="date" :min="today" v-model="ApplicantData.date" @change="searchSchedule()"/>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Schedule</label>
                        <select class="form-control" id="schedule" name="schedule" v-model="ApplicantData.schedule" required>
                            <option value=''>--Select Available Time--</option>
                            <option v-for="(schedule, index) in schedules" :key="index" :value="schedule.schedule">{{schedule.schedule}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Last Name*</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="ApplicantData.last_name" :class="{'is-invalid' : ApplicantData.errors.has('last_name') }" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="ApplicantData.first_name" :class="{'is-invalid' : ApplicantData.errors.has('first_name') }">
                        <has-error :form="ApplicantData" field="first_name"></has-error> 
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="ApplicantData.middle_name" :class="{'is-invalid' : ApplicantData.errors.has('middle_name') }"/>
                        <has-error :form="ApplicantData" field="middle_name"></has-error> 
                    </div>
                </div>  
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <label>Date of Birth *</label>
                    <div class="form-group">
                        <input name="dob" id="dob" type="date" :max="today" data-provide="datepicker" data-date-autoclose="true" class="form-control" required placeholder="Birth Date" v-model="ApplicantData.dob" :class="{'is-invalid' : ApplicantData.errors.has('dob') }" @change="updateAmount()" >
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Sex *</label>
                        <select class="form-control" id="sex" name="sex" required v-model="ApplicantData.sex" :class="{'is-invalid' : ApplicantData.errors.has('sex') }">
                            <option value=''>---Select Sex---</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Last Menstrual Period (Females only)</label>
                        <input type="date" class="form-control" id="lmp" name="lmp" placeholder="Enter Last Menstrual Period *" v-model="ApplicantData.lmp" :class="{'is-invalid' : ApplicantData.errors.has('lmp') }" v-show="ApplicantData.sex == 'Female'"/>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Nationality</label>
                        <select class="form-control" id="nationality_id" name="nationality_id" v-model="ApplicantData.nationality_id" :class="{'is-invalid' : ApplicantData.errors.has('nationality_id') }">
                            <option value=''>---Select Nationality---</option>
                            <option v-for="nation in nations" v-bind:key="nation.id" :value="nation.id" >{{nation.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Passport Number</label>
                        <input type="text" class="form-control" id="passport_no" name="passport_no" placeholder="Enter Passport Number *" required v-model="ApplicantData.passport_no" :class="{'is-invalid' : ApplicantData.errors.has('passport_number') }" />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Visa Type</label>
                        <input type="text" class="form-control" id="visa_type" name="visa_type" placeholder="Enter Visa Type *" required v-model="ApplicantData.visa_type" :class="{'is-invalid' : ApplicantData.errors.has('visa_type') }" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="ApplicantData.phone" :class="{'is-invalid' : ApplicantData.errors.has('phone') }">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address *" required v-model="ApplicantData.email" :class="{'is-invalid' : ApplicantData.errors.has('email') }">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Address in Nigeria*</label>
                        <QuillEditor rows="5" id="nigerian_address" name="nigerian_address" placeholder="Enter Address *" required v-model="ApplicantData.nigerian_address" :class="{'is-invalid' : ApplicantData.errors.has('nigerian_address') }"></QuillEditor>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Address in the UK*</label>
                        <QuillEditor rows="5" id="uk_address" name="uk_address" placeholder="Enter Address *" required v-model:content="ApplicantData.uk_address" :class="{'is-invalid' : ApplicantData.errors.has('uk_address') }"></QuillEditor>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" v-model="terms">
                        <label class="form-check-label">I have read and accepted the <a href="#" @click.prevent="viewTerms()">Terms and conditions</a> of St. Nicholas Hospital as well as the United Kingdom Home office.</label>
                    </div>
                </div>
            </div>
            <paystack class="btn btn-primary" v-html="'PAY NGN '+ApplicantData.amount+' Online'" buttonClass="'btn btn-primary'" currency="NGN" :publicKey="PUBLIC_KEY" :email="ApplicantData.email" :amount="ApplicantData.amount*100" :reference="genRef()" :onSuccess="processAppointment" :onCancel="processAppointment" :disabled="terms == 0 || ApplicantData.email == '' || ApplicantData.first_name == '' || ApplicantData.last_name == '' || ApplicantData.schedule == ''"></paystack>
        </form>
    </div>
    <div class="card-footer">
        Kindly note that terms 
    </div>
</div>
</template>
<script>
import paystack from 'vue3-paystack';
export default {
    components: {
        paystack
    },
    data(){
        return  {
            terms: 0,
            today: '',
            PUBLIC_KEY: "pk_live_9e3c92567f7ad310ae7c28e248b8edb67ca2661a",
            amount: 0,
            loading: false,
            nations: [],
            schedules: [],
            services: [], 
            ApplicantData: new Form({
                first_name: '', 
                middle_name:'', 
                last_name:'', 
                amount: 0,
                dob: '',
                sex:'',
                lmp:'', 
                nationality_id: '',
                alt_phone:'', 
                phone:'', 
                email:'',
                id:'', 
                image:'', 
                nigerian_address:'', 
                uk_address:'',
                accompanying_kids: 0,
                visa_type: '',
                passport_number: '',
                schedule: '',
                service_id: '',
                date: '',
                payment_method:'',
                payment_reference: '',
                payment_transaction: '',
            }),
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        callback: function(response){
            console.log(response)
        },
        closeModal(){
            $('#termsModal').modal('hide');
        },
        close: function(){
            console.log("Payment closed")
        },
        createApplicant(){
            this.loading = true;
            this.ApplicantData.post('/api/scheduler')
            .then(response =>{
                this.loading = false;
                this.ApplicantData.reset();
                this.$swal.fire({icon: 'success', title: 'The Profile details has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        genRef(){
            return "Task_"+ new Date().valueOf();
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/scheduler')
            .then(response => {;
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; 
                var yyyy = today.getFullYear();
                if(dd<10){dd='0'+dd;} 
                if(mm<10){mm='0'+mm;} 
                today = yyyy+'-'+mm+'-'+dd;
                console.log(today);
            this.today = today;
            this.refreshScheduler(response)
            this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        getProfilePic(){
            let photo = (this.ApplicantData.image.length >= 150) ? this.ApplicantData.image : "./"+this.ApplicantData.image;
            return photo;
        },
        isWeekend(date){
            var cast = new Date(date)
            console.log(cast);
            console.log(cast.getDay() === 6 || cast.getDay() === 0);
            return cast.getDay() === 6 || cast.getDay() === 0;
        },
        refreshScheduler(response){
            this.services = response.data.services;
            this.nations = response.data.nations;
        },
        processAppointment(response){
            if (response.message == "Approved"){
                alert("Payment was successful");
                this.ApplicantData.payment_method = "Paystack";
                this.ApplicantData.payment_reference= response.reference;
                this.ApplicantData.payment_transaction = response.transaction;

                this.createApplicant();
            }
            else{
                alert("Payment has to be made to confirm booking");
            }
        },
        processBooking(){
            console.log(response);
            this.ApplicantData.payment_method = "Holding";
        },
        searchSchedule(){
            if (this.ApplicantData.service_id == ""){
                alert("Please select the service type");
                this.ApplicantData.date = "";
                return;
            }
            else if (this.isWeekend(this.ApplicantData.date)){
                alert("Weekend not available for selection");
                this.ApplicantData.date = "";
                return;
            }
            axios.get('/api/schedules?service_id='+this.ApplicantData.service_id+'&date='+this.ApplicantData.date)
            .then(response =>{
                this.schedules = response.data.schedules;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({ icon: 'error', title: 'Schedules not loaded successfully',})
            });
        },
        updateAmount(){
            var dob = new Date(this.ApplicantData.dob);
            var month_diff = Date.now() - dob.getTime();  
            var age_dt = new Date(month_diff);   
            var year = age_dt.getUTCFullYear();  
            var age = Math.abs(year - 1970);  
            
            if (age >= 11){this.ApplicantData.amount = 100000;}
            else {this.ApplicantData.amount = 50000;}
        },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.ApplicantData.image = reader.result
                    }
                reader.readAsDataURL(file)
            }
            else{
                this.$swal.fire({type: 'error', title: 'File is too large'})
            }
        },
        viewTerms(){
            $('#termsModal').modal('show');
        }
    },
    props:{
    }
}
</script>