<template>
<form id="register_form" class="overlay-wrapper p-0"> 
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>   
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
    <button @click.prevent="editMode ? updateContact() : createContact()" type="submit" name="submit" class="submit btn btn-success">Submit </button>
</form>
</template>
<script>
export default {
    data(){
        return {
            contactForm: new Form({
                id:'',
                name:'',
                address:'',
                email_address:'',
                phone:'',
                patient_id: '',
            }),
            loading: false,
        }
    },
    emits: ['refreshPatientContacts'],
    methods:{
        createContact(){
            this.loading = true;
            this.contactForm.patient_id = this.patient_id;
            this.contactForm.post('/api/emr/hims/contacts')
            .then(response =>{
                this.$emit('refreshPatientContacts', this.patient);
                this.$swal.fire({icon: 'success', title: 'The Contact details has been created', showConfirmButton: false, timer: 1500});
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=> {
                this.loading = false;
            }); 
        },
        updateContact(){
            this.loading = true;
            this.contactForm.put('/api/emr/hims/contacts/'+this.contactForm.id)
            .then(response =>{
                this.$emit('refreshPatientContacts', this.patient);
                this.$swal.fire({icon: 'success', title: 'The Contact details has been updated', showConfirmButton: false, timer: 1500 });})
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });          
        },    
    },
    mounted() {
    },
    props:{
        contact: Object,
        editMode: Boolean,
        patient_id: Number,
    },
    watch:{
        contact:{
            handler(newContact){
                if(!newContact) return;
                this.contactForm.fill(newContact);
            },
            immediate: true,
            deep: true
        },
    }
}
</script>