<template>
<div>
<form>
    <alert-error :form="PatientData"></alert-error> 
    <div class="row">
        <div class="col-6 col-sm-12">
            <div class="form-group">
                <label>Title*</label>
                <select class="form-control" id="registration_type" name="registration_type" required v-model="PatientData.registration_type" :class="{'is-invalid' : PatientData.errors.has('title') }">
                    <option value="">--Select Registration Type--</option>
                    <option value="temporary">New Temporary</option>
                    <option value="cash">Cash Patient</option>
                    <option value="hmo">HMO Patient</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <label>Title*</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Title *" required v-model="PatientData.title" :class="{'is-invalid' : PatientData.errors.has('title') }" />
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>Last Name*</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="PatientData.last_name" :class="{'is-invalid' : PatientData.errors.has('last_name') }" />
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="PatientData.first_name" :class="{'is-invalid' : PatientData.errors.has('first_name') }">
                <has-error :form="PatientData" field="first_name"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Other Names</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="PatientData.middle_name" :class="{'is-invalid' : PatientData.errors.has('middle_name') }"/>
                <has-error :form="PatientData" field="middle_name"></has-error> 
            </div>
        </div>  
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-12">
            <label>Date of Birth</label>
            <div class="form-group">
                <input name="dob" id="dob" type="date" data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="Birth Date" v-model="PatientData.dob" :class="{'is-invalid' : PatientData.errors.has('dob') }">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Sex</label>
                <select class="form-control" id="sex" name="sex" required v-model="PatientData.sex" :class="{'is-invalid' : PatientData.errors.has('sex') }">
                    <option value=''>---Select Sex---</option>
                    <option value="Female">Female</option>
                    <option value="Male">Male</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Nationality</label>
                <select class="form-control" id="nation_id" name="nation_id" v-model="PatientData.nation_id" :class="{'is-invalid' : PatientData.errors.has('nation_id') }">
                    <option value=''>---Select Nationality---</option>
                    <option v-for="nation in nations" v-bind:key="nation.id" :value="nation.id" >{{nation.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Occupation</label>
                <input type="text" class="form-control" id="passport_no" name="passport_no" placeholder="Enter Passport Number *" required v-model="PatientData.passport_no" :class="{'is-invalid' : PatientData.errors.has('passport_number') }" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-12">
            <label>Profile Picture</label>
            <div class="form-group">
                <input type="file" class="form-control" placeholder="Birth Date" @change="updateProfilePic">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="PatientData.phone" :class="{'is-invalid' : PatientData.errors.has('phone') }">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Alternate Phone</label>
                <input type="text" class="form-control" id="alt_phone" name="alt_phone" placeholder="Alternate Phone Number" v-model="PatientData.alt_phone" :class="{'is-invalid' : PatientData.errors.has('alt_phone') }">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address *" required v-model="PatientData.email" :class="{'is-invalid' : PatientData.errors.has('email') }">
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="PatientData.id">
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label>Address*</label>
                <wysiwyg rows="5" id="uk_address" name="uk_address" placeholder="Enter Address *" required v-model="PatientData.uk_address" :class="{'is-invalid' : PatientData.errors.has('uk_address') }"></wysiwyg>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="accordion col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4 class="card-title w-100">
                        <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">Contacts</a>
                    </h4>
                </div>
                <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
            <label>Name *</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Full Name *" required  v-model="contactForm.name" :class="{'is-invalid' : contactForm.errors.has('name') }">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address *" required v-model="contactForm.address" :class="{'is-invalid' : contactForm.errors.has('address') }">
            </div>
        </div>
    </div>    
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Phone Number*</label>
                <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" value="" required v-model="contactForm.phone" :class="{'is-invalid' : contactForm.errors.has('phone') }">
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter NOK Email Address *" required v-model="contactForm.email_address" :class="{'is-invalid' : contactForm.errors.has('email_address') }">
            </div>
        </div>
    </div>
                    </div>
                </div>
            </div>
            <div class="card card-danger">
                <div class="card-header">
                    <h4 class="card-title w-100"><a class="d-block w-100" data-toggle="collapse" href="#collapseTwo">Collapsible Group Danger</a>
                    </h4>
                </div>
                <div id="collapseTwo" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                    
                    </div>
                </div>
            </div>
            <div class="card card-success">
                <div class="card-header">
                    <h4 class="card-title w-100"><a class="d-block w-100" data-toggle="collapse" href="#collapseThree">Collapsible Group Success</a></h4>
                </div>
                <div id="collapseThree" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button @click.prevent="editMode ? updatePatientData() : createPatient()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
</form>
</div>
</template>
<script>
export default {
    data(){
        return  {
            PatientData: new Form({
                area_id: '',
                alt_phone: '',
                balance: '',
                blood_group: '',
                city: '',
                contacts: [],
                credit_limit: '',
                dob: '',
                email: '',
                first_name: '',
                genotype: '',
                image: '',
                id:'', 
                image:'',
                insurance: [], 
                joined_at: '',
                last_name: '',
                middle_name: '',
                nation_id: '',
                nok: {},
                occupation: '',
                old_emr_numbers: '',
                other_details: '',
                password: '',
                patient_type: '',
                phone: '',
                referraL_type_id: '',
                referral_details: '',
                sex: '',
                state_id: '',
                street: '',
                street2: '',
                title: '',
                username: '',
                unique_id: '',
                user_id: '',
            }),    
            areas: [],
            all_areas: [],
            blood_groups:['AB-', 'AB+', 'A-', 'A+', 'B-', 'B+', 'O-', 'O+'],
            genotype: ['AA', 'AC', 'AS', 'CC', 'SC', 'SS'],
            nations: [],
            states: [],
            registration_types: [],
            
        }

    },
    mounted() {
        this.getInitials();
        Fire.$on('ApplicantDataFill', user =>{
            this.PatientData.fill(user);
        });
    },
    methods:{
        createPatient(){
            this.$Progress.start();
            this.PatientData.post('/api/emr/hims/patients')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshAppointment', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been created',
                    showConfirmButton: false,
                    timer: 1500
                    });
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
        getInitials(page=1){
            if (this.patient != null){
                axios.get('/api/emr/hims/patients/initials')
                .then(response =>{
                    this.$Progress.finish();
                    this.refresh(response);
                })
                .catch(()=>{
                    this.$Progress.fail();
                    toast.fire({icon: 'error', title: 'Allergies failed to load successfully',});
                });
            }
            else{this.allergies = [];}
        },
        getProfilePic(){
            let photo = (this.PatientData.image.length >= 150) ? this.PatientData.image : "./"+this.PatientData.image;
            return photo;
            },
        refresh(response){
            this.all_areas = response.data.areas;
            this.areas = response.data.areas;
            //this.plans = response.data.plans;
            //this.providers = response.data.providers;
            //this.provider_types = response.data.provider_types;
            this.registration_types = response.data.registration_types;
            this.nations = response.data.nations;
            this.states = response.data.states; 
        },
        updatePatientData(){
            console.log("Tested");
            this.$Progress.start();
            this.PatientData.put('/api/emr/hims/patients/'+this.PatientData.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshAppointment', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
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
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.PatientData.image = reader.result
                    }
                reader.readAsDataURL(file)
            }
            else{
                Swal.fire({
                    type: 'error',
                    title: 'File is too large'
                })
            }
        },
        
    },
    props:{
        applicant: Object,
        editMode: Boolean,   
        nations: Array, 
    }
}
</script>