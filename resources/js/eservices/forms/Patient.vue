<template>
<div class="">
<form class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <alert-error :form="ApplicantData"></alert-error> 
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
            <label>Date of Birth</label>
            <div class="form-group">
                <input name="dob" id="dob" type="date" data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="Birth Date" v-model="ApplicantData.dob" :class="{'is-invalid' : ApplicantData.errors.has('dob') }">
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Sex</label>
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
                <input type="date" class="form-control" id="lmp" name="lmp" placeholder="Enter Last Menstrual Period *" required v-model="ApplicantData.lmp" :class="{'is-invalid' : ApplicantData.errors.has('lmp') }" />
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Nationality</label>
                <select class="form-control" id="nationality_id" name="nationality_id" v-model="ApplicantData.nationality_id" :class="{'is-invalid' : ApplicantData.errors.has('nationality_id') }">
                    <option value=''>---Select Nationality---</option>
                    <option v-for="nation in nations" v-bind:key="nation.id" :value="nation.id" >{{nation.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Passport Number</label>
                <input type="text" class="form-control" id="passport_no" name="passport_no" placeholder="Enter Passport Number *" required v-model="ApplicantData.passport_no" :class="{'is-invalid' : ApplicantData.errors.has('passport_number') }" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Visa Type</label>
                <input type="text" class="form-control" id="visa_type" name="visa_type" placeholder="Enter Visa Type *" required v-model="ApplicantData.visa_type" :class="{'is-invalid' : ApplicantData.errors.has('visa_type') }" />
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>No of accompanying children less than 11 years</label>
                <input type="number" class="form-control" id="accompanying_kids" name="accompanying_kids" placeholder="Enter Number of Accompanying Kids *" required v-model="ApplicantData.accompanying_kids" :class="{'is-invalid' : ApplicantData.errors.has('accompanying_kids') }">
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <label>Profile Picture</label>
            <div class="form-group">
                <input type="file" class="form-control" placeholder="Birth Date" @change="updateProfilePic">
            </div>
        </div>
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
        <input type="hidden" name="id" id="id" v-model="ApplicantData.id">
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Address in Nigeria*</label>
                <QuillEditor content-type="html" theme="snow" class="form-control" id="nigerian_address" name="nigerian_address" v-model:content="ApplicantData.nigerian_address" :class="{'is-invalid' : ApplicantData.errors.has('nigerian_address') }">{{ApplicantData.nigerian_address}}</QuillEditor>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Address in the UK*</label>
                <QuillEditor theme="snow" class="form-control" rows="5" id="uk_address" name="uk_address" placeholder="Enter Address *" required v-model:content="ApplicantData.uk_address" content-type="html" :class="{'is-invalid' : ApplicantData.errors.has('uk_address') }"></QuillEditor>
            </div>
        </div>
    </div>
    <button @click.prevent="editMode ? updateApplicantData() : createApplicant()" type="submit" name="submit" class="submit btn bg-navy">Submit</button>
</form>
</div>
</template>
<script>
export default {
    data(){
        return  {
            ApplicantData: new Form({
                first_name: '', 
                middle_name:'', 
                last_name:'', 
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
                passport_no: '',
            }),
            loading: false,
            nations: [],
        }
    },
    emits:['refreshPatient'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        createApplicant(){
            this.loading = true;
            this.ApplicantData.post('/api/emr/patients')
            .then(response =>{
                this.loading = false;
                this.$emit('refreshPatient', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been created',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });  
        },
        getAllInitials(){
            axios.get('/api/emr/patients/initials')
            .then(response =>{
                this.nations = response.data.nations;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Schedules not loaded successfully',
                })
            });
        },
        updateApplicantData(){
            this.loading = true;
            this.ApplicantData.put('/api/emr/patients/'+this.ApplicantData.id)
            .then(response =>{
                this.loading = false;
                console.log('Working');
                this.$emit('refreshPatient', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });    
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
                this.$swal.fire({
                    type: 'error',
                    title: 'File is too large'
                })
            }
        },
    },
    props:{
        applicant: Object,
        editMode: Boolean,   
    },
    watch:{
        applicant(){this.ApplicantData.fill(this.applicant)}
    }
}
</script>