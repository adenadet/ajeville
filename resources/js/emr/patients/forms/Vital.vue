<template>
<section>
    <form>
        <alert-error :form="VitalData"></alert-error> 
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Title*</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Title *" required v-model="VitalData.title" :class="{'is-invalid' : VitalData.errors.has('title') }" />
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Last Name*</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="VitalData.last_name" :class="{'is-invalid' : VitalData.errors.has('last_name') }" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="VitalData.first_name" :class="{'is-invalid' : VitalData.errors.has('first_name') }">
                    <has-error :form="VitalData" field="first_name"></has-error> 
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="VitalData.middle_name" :class="{'is-invalid' : VitalData.errors.has('middle_name') }"/>
                    <has-error :form="VitalData" field="middle_name"></has-error> 
                </div>
            </div>  
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <label>Date of Birth</label>
                <div class="form-group">
                    <input name="dob" id="dob" type="date" data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="Birth Date" v-model="VitalData.dob" :class="{'is-invalid' : VitalData.errors.has('dob') }">
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Sex</label>
                    <select class="form-control" id="sex" name="sex" required v-model="VitalData.sex" :class="{'is-invalid' : VitalData.errors.has('sex') }">
                        <option value=''>---Select Sex---</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Nationality</label>
                    <select class="form-control" id="nationality_id" name="nationality_id" v-model="VitalData.nationality_id" :class="{'is-invalid' : VitalData.errors.has('nationality_id') }">
                        <option value=''>---Select Nationality---</option>
                        <option v-for="nation in nations" v-bind:key="nation.id" :value="nation.id" >{{nation.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Passport Number</label>
                    <input type="text" class="form-control" id="passport_no" name="passport_no" placeholder="Enter Passport Number *" required v-model="VitalData.passport_no" :class="{'is-invalid' : VitalData.errors.has('passport_number') }" />
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
                    <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="VitalData.phone" :class="{'is-invalid' : VitalData.errors.has('phone') }">
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Alternate Phone</label>
                    <input type="text" class="form-control" id="alt_phone" name="alt_phone" placeholder="Alternate Phone Number" v-model="VitalData.alt_phone" :class="{'is-invalid' : VitalData.errors.has('alt_phone') }">
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address *" required v-model="VitalData.email" :class="{'is-invalid' : VitalData.errors.has('email') }">
                </div>
            </div>
            <input type="hidden" name="id" id="id" v-model="VitalData.id">
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                    <label>Address in the UK*</label>
                    <wysiwyg rows="5" id="uk_address" name="uk_address" placeholder="Enter Address *" required v-model="VitalData.uk_address" :class="{'is-invalid' : VitalData.errors.has('uk_address') }"></wysiwyg>
                </div>
            </div>
        </div>
        
        <button @click.prevent="editMode ? updateVital() : createVital()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            VitalData: new Form({
                patient_id:'',
                appointment_id:'',
                blood_pressure:'',
                temp:'',
                blood_sugar:'',
                height:'',
                weight:'',
                pulse:'',
                taken_by:'',
            }),
        }
    },
    mounted() {
        Fire.$on('VitalDataFill', user =>{
            this.VitalData.fill(user);
        });
        Fire.$on('AfterCreation', ()=>{
            //axios.get("api/profile").then(({ data }) => (this.VitalData.fill(data)));
        });
    },
    methods:{     
        createVital(){
            this.$Progress.start();
            this.VitalData.post('/api/emr/hims/patients')
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
        updateVitalData(){
            console.log("Tested");
            this.$Progress.start();
            this.VitalData.put('/api/emr/hims/patients/'+this.VitalData.id)
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
        getProfilePic(){
            let photo = (this.VitalData.image.length >= 150) ? this.VitalData.image : "./"+this.VitalData.image;
            return photo;
            },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.VitalData.image = reader.result
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