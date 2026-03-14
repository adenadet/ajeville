<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <form  @submit.prevent="createPatient()">
                        <div class="card-header bg-navy">
                            <h4 class="card-title">New Registration</h4>
                            <div class="card-tools">
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <select name="patient_type" id="patient_type" class="form-control float-right" v-model="PatientData.patient_type" required>
                                            <option value=''>Select Registration Type</option>
                                            <!--option v-for="reg in registration_types" :value="reg.id">{{ reg.name}}</option-->
                                            <option value="0">Temporary Registration</option>
                                            <option value="1">Full Registration</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                            <div id="accordion">
                                <div class="card">                           
                                    <div class="card-header bg-dark">
                                        <h4 class="card-title w-100">Basic Details</h4>
                                    </div>   
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label>Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="title" name="title" placeholder="Title *" required v-model="PatientData.title" />
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Last Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="PatientData.last_name" />
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>First Name <span class="text-danger">*</span></label>
                                                    <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="PatientData.first_name" />
                                                    <has-error :form="PatientData" field="first_name"></has-error> 
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Other Names</label>
                                                    <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="PatientData.middle_name"/>
                                                    <has-error :form="PatientData" field="middle_name"></has-error> 
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-12">
                                                <label>Date of Birth <span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <input name="dob" id="dob" type="date" data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="Birth Date" v-model="PatientData.dob" />
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label>Sex <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="sex" name="sex" required v-model="PatientData.sex">
                                                        <option value=''>---Select Sex---</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Male">Male</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label>Nationality</label>
                                                    <select class="form-control" id="nationality_id" name="nationality_id" v-model="PatientData.nationality_id">
                                                        <option value=''>---Select Nationality---</option>
                                                        <option v-for="nation in nations" v-bind:key="nation.id" :value="nation.id" >{{nation.name}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label>Occupation</label>
                                                    <input type="text" class="form-control" id="passport_no" name="passport_no" placeholder="Enter Occupation"  v-model="PatientData.occupation" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label>Address*</label>
                                                    <input type="text" class="form-control" id="street" name="street" placeholder="Enter Address *" v-model="PatientData.street" :class="{'is-invalid' : PatientData.errors.has('street') }" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label>Address2</label>
                                                    <input type="text" class="form-control" id="street2" name="street2" placeholder="Enter Street Desc" v-model="PatientData.street2" :class="{'is-invalid' : PatientData.errors.has('street2') }">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <label>City*</label>
                                                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter City *" v-model="PatientData.city" :class="{'is-invalid' : PatientData.errors.has('city') }">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <select class="form-control" name="state_id" id="state_id" v-model="PatientData.state_id" @change="changedState()">
                                                        <option value=''>--Select State--</option>
                                                        <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                                                    </select> 
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Area</label>
                                                    <model-list-select class="form-control" :list="all_areas" v-model="PatientData.area_id" option-value="id" option-text="name" placeholder="Select LGA of Residence" />
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
                                                    <label>Phone Number <span class="text-danger">*</span></label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="PatientData.phone" :class="{'is-invalid' : PatientData.errors.has('phone') }">
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label>Alternate Phone</label>
                                                    <input type="tel" class="form-control" id="alt_phone" name="alt_phone" placeholder="Alternate Phone Number" v-model="PatientData.alt_phone" :class="{'is-invalid' : PatientData.errors.has('alt_phone') }">
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label>Email Address <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address *" v-model="PatientData.email" :class="{'is-invalid' : PatientData.errors.has('email') }">
                                                </div>
                                            </div>
                                        </div>   
                                    </div>   
                                </div>
                                <div class="card" v-if="PatientData.patient_type == 1">
                                    <div class="card-header bg-dark">
                                        <h4 class="card-title w-100"><a class="d-block w-100 text-white" data-toggle="collapse" href="#collapseTwo">Insurance</a></h4>
                                    </div>
                                    <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="card card-primary">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Select Insurance </h3>
                                                        </div>
                                                        <form>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <label>Insurance Type</label>
                                                                <select class="form-control" v-model="insuranceForm.insurance_type_id" @change="changedInsuranceType">
                                                                    <option value="">--Select Insurance Type--</option>
                                                                    <option v-for="provider_type in provider_types" :value="provider_type.id">{{ provider_type?.name }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Providers</label>
                                                                <select class="form-control" v-model="insuranceForm.provider_type_id" @change="changedProvider">
                                                                    <option value="">--Select Provider--</option>
                                                                    <option v-for="provider in insurance_forms_providers" :value="provider.id">{{ provider?.name }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Plans</label>
                                                                <multiselect v-model="insuranceForm.plan_id" track-by="id" label="name" :options="insurance_forms_plans" :searchable="true" :close-on-select="true" :show-labels="name" placeholder="Pick a value" aria-label="pick a value"></multiselect>
                                                            </div>
                                                            <button class="btn btn-dark" type="button" @click="addInsurance">Add </button> 
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Insurance Plans </h3>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 10px">#</th>
                                                                        <th>Plan Name</th>
                                                                        <!--th>Provider Name</th-->
                                                                        <th>Enrollee Number</th>
                                                                        <th>Expiry Date</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr v-for="(plan, index) in PatientData.insurances" :key="plan.uid">
                                                                        <td>{{ addOne(index) }}</td>
                                                                        <td>{{ plan.name }}</td>
                                                                        <!--td>{{ plan.provider.name }}</td-->
                                                                        <td><input type="text" class="form-control" v-model="PatientData.insurances[index].enrollee_id" /></td>
                                                                        <td><input type="date" class="form-control" v-model="PatientData.insurances[index].expiry_date" :min="today" /></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card" v-if="PatientData.patient_type == 1">
                                    <div class="card-header bg-dark">
                                        <h4 class="card-title w-100"><a class="d-block w-100 text-white" data-toggle="collapse" href="#collapseFour">Next of Kin</a></h4>
                                    </div>
                                    <div id="collapseFour" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label>Name *</label>
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name *" v-model="PatientData.nok.name">
                                                    </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address *"  v-model="PatientData.nok.address">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label>Phone Number*</label>
                                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" value="" v-model="PatientData.nok.phone" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter NOK Email Address *" v-model="PatientData.nok.email_address">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label>Relationship</label>
                                                        <input type="text" class="form-control" id="relationship" placeholder="Enter NOK Relationship *" v-model="PatientData.nok.relationship">
                                                    </div>
                                                </div>                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card" v-if="PatientData.patient_type == 1">
                                    <div class="card-header bg-dark">
                                        <h4 class="card-title w-100"><a class="d-block w-100 text-white" data-toggle="collapse" href="#collapseThree">Contacts</a></h4>
                                    </div>
                                    <div id="collapseThree" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="card m-0 p-0">
                                                <div class="card-header">
                                                    <div class="card-tools">
                                                        <button class="btn btn-sm bg-dark" @click="addContact" type="button">Add New <i class="fa fa-plus ml-1"></i></button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row" v-for="(contact, index) in PatientData.contacts">
                                                        <div class="col-md-3 col-sm-12">
                                                            <div class="form-group">
                                                                <label>Name *</label>
                                                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name *" v-model="PatientData.contacts[index].name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3  col-sm-12">
                                                            <div class="form-group">
                                                                <label>Address</label>
                                                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address *"  v-model="PatientData.contacts[index].address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-12">
                                                            <div class="form-group">
                                                                <label>Phone Number*</label>
                                                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" value="" v-model="PatientData.contacts[index].phone" >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-12">
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter NOK Email Address *" v-model="PatientData.contacts[index].email">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-sm-12">
                                                            <div class="form-group">
                                                                <label>&nbsp; <br /></label>
                                                                <button class="btn btn-sm btn-danger" @click="deleteContact(index)"><i class="fa fa-trash"></i></button>
                                                            </div>
                                                        </div>                                              
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn bg-dark" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import Multiselect from 'vue-multiselect';
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {
        Multiselect, ModelListSelect,
    },
    computed:{
        current_age(){
            if(!this.PatientData.dob) return '';
            let diff = new Date() - new Date(this.PatientData.dob);
            return Math.floor(diff / (365.25 * 24 * 60 * 60 * 1000));
        },
        today(){
            return new Date().toJSON().slice(0, 10);
        },
    },
    data(){
        return  {
            all_areas: [],
            all_providers: [],
            all_plans: [],
            areas: [],
            blank_user: {
                name: '',
                address: '',
                email: '',
                phone: '', 
            },
            insuranceForm: new Form({
                insurance_type_id: '',
                provider_type_id: '',
                plan_id: '',
            }),
            loading: false,
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
                id:'', 
                image:'',
                insurances: [], 
                joined_at: '',
                last_name: '',
                middle_name: '',
                nation_id: '',
                nok: {
                    name: '',
                    address: '',
                    phone: '',
                    email_address: '',
                    relationship: '',
                },
                occupation: '',
                old_emr_numbers: '',
                other_details: '',
                password: '',
                patient_type: '',
                phone: '',
                referral_type_id: '',
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
            nations: [],
            providers: [],
            plans: [],
            registration_types: [],
            provider_types: [],
            insurance_forms_plans: [],
            insurance_forms_providers: [],
            states: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addContact(){
            this.PatientData.contacts.push({
                name: '',
                address: '',
                email: '',
                phone: '',
            });
        },
        addInsurance() {
            alert(this.insuranceForm.plan_id.name)
            if (!this.insuranceForm.plan_id) return;

            const selectedPlan = this.insuranceForm.plan_id;

            this.PatientData.insurances.push({
                uid: Date.now(), // unique key for Vue
                plan_id: selectedPlan.id,
                name: selectedPlan.name,
                //provider: selectedPlan.provider,
                enrollee_id: '',
                expiry_date: ''
            });

            // Optional: clear selection after adding
            this.insuranceForm.plan_id = '';
        },
        addTag (newTag) {
            const tag = {
                name: newTag,
                code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
            }
            this.PatientData.insurances.push(tag)
            this.plans.push(tag)
        },
        changedInsuranceType(){
            var insurance_type = this.provider_types.find(item => item.id === this.insuranceForm.insurance_type_id);
            this.insurance_forms_providers = insurance_type.providers;
            this.insurance_forms_plans = insurance_type.plans != null ? insurance_type.plans : this.plans;
        },
        changedProvider(){
            var provider = this.providers.find(item => item.id === this.insuranceForm.provider_type_id);
            this.insurance_forms_plans = provider.plans != null ? provider.plans : this.plans;
        },
        changedState(){
            var state = this.states.find(item => item.id === this.PatientData.state_id);
            this.all_areas = state.areas;
        },
        createPatient(){
            this.loading = true;
            this.PatientData.post('/api/emr/hims/patients')
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
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
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/patients/initials')
            .then(response => {this.loading = false; this.refreshPatients(response)})
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        getProfilePic(){
            let photo = (this.PatientData.image.length >= 150) ? this.PatientData.image : "./"+this.PatientData.image;
            return photo;
        },      
        refreshPatients(response){
            this.all_areas = response.data.areas;
            this.areas = response.data.areas;
            this.insurance_forms_plans= response.data.plans;
            this.insurance_forms_providers = response.data.providers;
            this.nations = response.data.nations;
            this.plans = response.data.plans;
            this.providers = response.data.providers;
            this.provider_types = response.data.provider_types;
            this.registration_types = response.data.registration_types;
            this.states = response.data.states;
        },
        updatePatientData(){
            this.loading = true;
            this.PatientData.put('/api/emr/hims/patients/'+this.PatientData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
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
    props:{}
}
</script>