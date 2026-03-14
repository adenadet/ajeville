<template>
<div class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <alert-error :form="BioData"></alert-error> 
        <div class="row" v-if="source == 'registration'">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" disabled="disabled" class="form-control" id="email" name="email" placeholder="Enter Email Address *" required v-model="BioData.email" :class="{'is-invalid' : BioData.errors.has('email') }">
                </div>
            </div>    
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password *" required v-model="BioData.password" :class="{'is-invalid' : BioData.errors.has('password') }">
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter Password *" required v-model="BioData.password_confirmation" :class="{'is-invalid' : BioData.errors.has('password_confirmation') }">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Last Name*</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-model="BioData.last_name" :class="{'is-invalid' : BioData.errors.has('last_name') }" />
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" required class="form-control" id="first_name" name="first_name" placeholder="First Name *" v-model="BioData.first_name" :class="{'is-invalid' : BioData.errors.has('first_name') }">
                    <has-error :form="BioData" field="first_name"></has-error> 
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-model="BioData.middle_name" :class="{'is-invalid' : BioData.errors.has('middle_name') }"/>
                    <has-error :form="BioData" field="middle_name"></has-error> 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Address*</label>
                    <input type="text" class="form-control" id="street" name="street" placeholder="Enter Address *" required v-model="BioData.street" :class="{'is-invalid' : BioData.errors.has('street') }" />
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Address2</label>
                    <input type="text" class="form-control" id="street2" name="street2" placeholder="Enter Street Desc" v-model="BioData.street2" :class="{'is-invalid' : BioData.errors.has('street2') }">
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>City*</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter City *" v-model="BioData.city" :class="{'is-invalid' : BioData.errors.has('city') }">
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>State</label>
                    <select class="form-control" id="state_id" name="state_id" placeholder="Enter State / County *" required v-model="BioData.state_id" :class="{'is-invalid' : BioData.errors.has('state_id') }">
                        <option value="">--Select State--</option>
                        <option v-for="state in states" v-bind:key="state.id" :value="state.id" >{{state.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>LGA</label>
                    <select class="form-control" id="area_id" name="area_id" required v-model="BioData.area_id" :class="{'is-invalid' : BioData.errors.has('area_id') }">
                        <option value="">--Select area--</option>
                        <option v-for="area in areas" v-bind:key="area.id" :value="area.id" >{{area.id+' '+area.name}}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number *" required v-model="BioData.phone" :class="{'is-invalid' : BioData.errors.has('phone') }">
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Sex</label>
                    <select class="form-control" id="sex" name="sex" required v-model="BioData.sex" :class="{'is-invalid' : BioData.errors.has('sex') }">
                        <option value="">---Select Sex---</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <label>Date of Birth</label>
                <div class="form-group">
                    <input name="dob" id="dob" type="date" data-provide="datepicker" data-date-autoclose="true" class="form-control" placeholder="Birth Date" v-model="BioData.dob" :class="{'is-invalid' : BioData.errors.has('dob') }">
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <label>Profile Pic</label>
                <div class="form-group">
                    <input type="file" class="form-control" placeholder="Birth Date" @change="updateProfilePic">
                </div>
            </div>
            <input type="hidden" name="id" id="id" v-model="BioData.id">
        </div>
        <button @click.prevent="editMode ? (source == 'registration' ? completeRegistration() : updateBioData()) : createBioData()" type="submit" name="submit" class="submit btn btn-success">{{editMode ? 'Update' : 'Submit'}}</button>
    </form>
</div>
</template>
<script>
export default {
    data(){
        return  {
            BioData: new Form({
                alt_phone:'', 
                area_id:'', 
                branch_id:'', 
                city:'', 
                department_id:'', 
                dob:'', 
                email:'',
                first_name: '', 
                id:'', 
                image:'', 
                joined_at: '',
                unique_id: '',
                last_name:'', 
                middle_name:'', 
                password:'',
                password_confirmation:'', 
                personal_email: '', 
                phone:'', 
                roles:'',
                sex:'', 
                state_id:'', 
                street:'', 
                street2:'',
                unique_id: '', 
            }),
            loading: false,
            areas: [],
            states: [],
        }
    },
    emits:['reloadUser'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        completeRegistration(){
            this.loading = true;
            this.BioData.put('/api/registration/'+ this.BioData.id)
            .then(response =>{
                this.$swal.fire({icon: 'success', title: 'Registration Completed Successfully', showConfirmButton: false, timer: 1500});
                this.$emit('reloadUser', response);
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'User details not updated!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        createBioData(){
            this.loading = true;
            this.BioData.post('/api/ums/users')
            .then(response =>{
                this.$emit('reloadUser');
                this.$swal.fire({icon: 'success', title: 'The User '+ response.data.user.first_name+' '+  response.data.user.last_name+' has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/registration/initials').then(response =>{
                this.reloadProfile(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Profile not loaded successfully',
                })
            });
        },
        getProfilePic(){
            let photo = (this.BioData.image.length >= 150) ? this.BioData.image : "./"+this.BioData.image;
            return photo;
        },
        reloadProfile(response){
            this.areas = response.data.areas;
            this.branches = response.data.branches;
            this.departments = response.data.departments;
            this.states = response.data.states;
            this.nok = response.data.nok;
            this.nations = response.data.nations;
        },
        updateBioData(){
            this.loading = true;
            this.BioData.put('/api/ums/users/'+ this.BioData.id)
            .then(response =>{
                this.$emit('reloadUser');
                this.$swal.fire({icon: 'success', title: 'The User has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'User update did not complete!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });            
        },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.BioData.image = reader.result;
                    console.log(reader.result);
                    }
                reader.readAsDataURL(file)
            }
            else{this.$swal.fire({type: 'error', title: 'File is too large'});}
        },
    },
    props:{
        source: String,
        user: Object,
        editMode: Boolean,
    },
    watch:{
        user: {
            handler(newUser){
                if(!newUser) return;
                this.BioData.fill(newUser);
            },
            immediate: true,
            deep: true
        },
        'BioData.state_id'(newStateId) {
            if (!newStateId) {
                this.areas = [];
                this.BioData.area_id = ''; // reset area selection
                return;
            }
            // find the selected state
            let selectedState = this.states.find(state => state.id === newStateId);
            if (selectedState && selectedState.areas) {
                this.areas = selectedState.areas;
                this.BioData.area_id = ''; // reset previous selection
            } 
            else {
                this.areas = [];
                this.BioData.area_id = '';
            }
        }
    }
}
</script>
