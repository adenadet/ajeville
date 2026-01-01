<template>
<section>
    <form>
        <alert-error :form="allergyForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Patient</label>
                    <input disabled type="text" class="form-control"
                        :value="patient != null ? patient.last_name + ', ' + patient.first_name + ' ' + (patient.middle_name != null ? patient.middle_name : '') : 'Loading Patient Data'" />
                    <input type="hidden" name="patient_id" id="patient_id" v-model="allergyForm.patient_id" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Allergy Type*</label>
                    <select type="text" required class="form-control" id="allergy_type_id" name="allergy_type_id" v-model="allergyForm.allergy_type_id">
                        <option value="">--Select Type--</option>
                        <option v-for="allergy_type in allergy_types" :value="allergy_type.id">{{ allergy_type.name }}</option>
                    </select>
                    <has-error :form="allergyForm" field="allergy_type_id"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Allergy</label>
                    <input required type="text" class="form-control" id="allergy" name="allergy" v-model="allergyForm.allergy" />
                    <has-error :form="allergyForm" field="allergy"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <wysiwyg rows="3" v-model="allergyForm.description" name="description" id="description"/>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updatePatientAllergy() : createPatientAllergy()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>    
</section>
</template>
<script>
export default {
    data(){
        return {
            allergyForm: new Form({
                id: '',
                patient_id: '', 
                allergy_type_id: '', 
                allergy: '', 
                description: '', 
            }),
            allergy_types: [],
            patient: {},
        }
    },
    methods:{
        createPatientAllergy(){
            this.$Progress.start();
            this.allergyForm.post('/api/emr/hims/allergies')
            .then(response =>{
                this.$Progress.finish();
                Swal.fire({icon: 'success', title: 'The Allergy details has been created', showConfirmButton: false, timer: 1500});
                Fire.$emit('refreshPatientAllergies', response.data.patient);
            })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });          
        },
        getInitials(){
            axios.get('/api/emr/hims/allergy_types')
            .then(response =>{
                this.$Progress.finish();
                this.reloadAllergy(response);
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Allegry form not loaded successfully',
                })
            });
        },
        reloadAllergy(response){
            this.allergy_types = response.data.allergy_types;
        },
        updatePatientAllergy(){
            this.$Progress.start();
            this.allergyForm.put('/api/emr/hims/allergies/'+this.allergyForm.id)
            .then(response =>{
                this.$Progress.finish();
                Swal.fire({icon: 'success', title: 'The Allergy details has been updated', showConfirmButton: false, timer: 1500});
                Fire.$emit('refreshPatientAllergies', response.data.patient);
            })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });          
        },
    },
    mounted() {
        Fire.$on('AllergyDataFill', details => {
            this.getInitials();
            this.patient = details.patient;
            this.allergyForm.id = details.allergy.id;
            this.allergyForm.patient_id = details.patient.id; 
            this.allergyForm.allergy_type_id = details.allergy.allergy_type_id; 
            this.allergyForm.allergy = details.allergy.allergy; 
            this.allergyForm.description = details.allergy.description; 
        });
    },
    props:{
        'allergy': Object,
        'editMode': Boolean,
    },
}
</script>