<template>
<section class="overlay-wrapper p-0">
    <form>
        <alert-error :form="insuranceForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Patient</label>
                    <div class="form-control" v-html="FullName(patient.user)"></div>
                    <input type="hidden" name="patient_id" id="patient_id" v-model="insuranceForm.patient_id" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Provider Type</label>
                    <select class="form-control" v-model="insuranceForm.provider_type_id" @change="loadProviders">
                        <option value="">Insurance Type</option>
                        <option v-for="t in provider_types" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Provider</label>
                    <select class="form-control" v-model="insuranceForm.provider_id" @change="loadPlans">
                        <option value="">Provider</option>
                        <option v-for="p in filtered_providers" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Plan</label>
                    <select class="form-control" v-model="insuranceForm.plan_id">
                    <option v-for="plan in filtered_plans"  :key="plan.id" :value="plan">{{ plan.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updateInsurance() : createInsurance()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>    
</section>
</template>
<script>
export default {
    data(){
        return {
            loading: false,
            filtered_plans: [],
            filtered_providers: [],
            insuranceForm: new Form({
                id: '',
                patient_id: '', 
                provider_type_id: '', 
                provider_id: '', 
                plan_id: '', 
            }),
            insurance_types: [],
            plans: [],
            providers: [],
            provider_types:[],
        }
    },
    methods:{
        createInsurance(){
            this.insuranceForm.post('/api/emr/hims/insurances')
            .then(response =>{
                this.$swal.fire({icon: 'success', title: 'The Insurance details has been created', showConfirmButton: false, timer: 1500});
                this.$emit('refreshPatientInsurance', response.data.patient);
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });          
        },
        getInitials(){
            axios.get('/api/emr/hims/insurances/initials')
            .then(response =>{
                this.reloadInsurance(response);
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Insurance form not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        loadPlans(){
            let provider = this.providers.find(p => p.id == this.insuranceForm.provider_id)
            this.filtered_plans = provider?.plans || []
        },
        loadProviders(){
            let type = this.provider_types.find(t => t.id == this.insuranceForm.provider_type_id)
            this.filtered_providers = type?.providers || []
        },
        reloadInsurance(response){
            this.provider_types = response.data.provider_types;
            this.providers = response.data.providers;
            this.plans = response.data.plans;
        },
        updateInsurance(){
            this.loading = true;
            this.insuranceForm.put('/api/emr/hims/insurances/'+this.insuranceForm.id)
            .then(response =>{
                this.$swal.fire({icon: 'success', title: 'The Insurance details has been updated', showConfirmButton: false, timer: 1500});
                this.$emit('refreshPatientInsurance', response.data.patient);
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });  
        },
    },
    mounted() {
        this.getInitials();
    },
    props:{
        editMode: Boolean,
        insurance: Object,
        patient: Object,
    },
    watch:{
        insurance(){
            this.insuranceForm.reset();
            this.insuranceForm.fill(this.insurance);
        }
    }
}
</script>