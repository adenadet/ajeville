<template>
<section>
    <form>
        <alert-error :form="insuranceForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Patient</label>
                    <input disabled type="text" class="form-control"
                        :value="patient != null ? patient.last_name + ', ' + patient.first_name + ' ' + (patient.middle_name != null ? patient.middle_name : '') : 'Loading Patient Data'" />
                    <input type="hidden" name="patient_id" id="patient_id" v-model="insuranceForm.patient_id" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Insurance Type*</label>
                    <select type="text" required class="form-control" id="insurance_type_id" name="insurance_type_id" v-model="insuranceForm.allergy_type_id">
                        <option value="">--Select Type--</option>
                        <option v-for="insurance_type in insurance_types" :value="insurance_type.id">{{ insurance_type.name }}</option>
                    </select>
                    <has-error :form="insuranceForm" field="allergy_type_id"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Provider </label>
                    <select required class="form-control" id="provider_id" name="provider_id" v-model="insuranceForm.provider_id">
                    </select>
                    <has-error :form="insuranceForm" field="provider_id"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Plan </label>
                    <select required class="form-control" id="plan_id" name="plan_id" v-model="insuranceForm.plan_id" />
                    <has-error :form="insuranceForm" field="plan_id"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <wysiwyg rows="3" v-model="insuranceForm.description" name="description" id="description"/>
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
            insuranceForm: new Form({
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
            this.insuranceForm.post('/api/emr/hims/allergies')
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
            this.insuranceForm.put('/api/emr/hims/allergies/'+this.insuranceForm.id)
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
            this.insuranceForm.id = details.allergy.id;
            this.insuranceForm.patient_id = details.patient.id; 
            this.insuranceForm.allergy_type_id = details.allergy.allergy_type_id; 
            this.insuranceForm.allergy = details.allergy.allergy; 
            this.insuranceForm.description = details.allergy.description; 
        });
    },
    props:{
        'allergy': Object,
        'editMode': Boolean,
    },
}
</script>