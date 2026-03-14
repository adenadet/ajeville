<template>
<section class="container-fluid">
    <div class="card">
        <form @submit.prevent="submit">
            <div class="card-header bg-navy">
                <h4 class="card-title">New Registration</h4>
                <div class="card-tools input-group input-group-sm" style="width: 200px;">
                    <select class="form-control"
                            v-model="patientData.patient_type"
                            required>
                        <option value="">Select Registration Type</option>
                        <option value="0">Temporary Registration</option>
                        <option value="1">Full Registration</option>
                    </select>
                </div>
            </div>

            <div class="card-body overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                <ComponentPatientBasicDetails :form="patientData" :states="states" :nations="nations" :areas="areas" @stateChanged="handleStateChanged" @profileUpload="updateProfilePic"/>
                <ComponentPatientInsuranceSection v-if="patientData.patient_type == 1" :form="patientData" :providerTypes="provider_types" :providers="providers" :plans="plans" v-model="patientData.nok"/>
                <ComponentPatientNokSection v-if="patientData.patient_type == 1" :form="patientData" v-model="patientData.nok"/>
                <ComponentPatientContactsSection v-if="patientData.patient_type == 1" :contacts="patientData.contacts" @add="addContact" @remove="removeContact"/>
            </div>

            <div class="card-footer">
                <button class="btn bg-dark">Submit</button>
            </div>

        </form>
    </div>
</section>
</template>
<script>
import ComponentPatientBasicDetails from '../components/Basic.vue'
import ComponentPatientInsuranceSection from '../components/Insurances.vue'
import ComponentPatientNokSection from '../components/Nok.vue'
import ComponentPatientContactsSection from '../components/Contact.vue'

export default {
    components:{
        ComponentPatientBasicDetails, ComponentPatientInsuranceSection, ComponentPatientNokSection, ComponentPatientContactsSection
    },
    data(){
        return{
        loading:false,
        patientData: new Form({
            patient_type:'',
            first_name:'',
            last_name:'',
            dob:'',
            sex:'',
            phone:'',
            email:'',
            state_id:'',
            area_id:'',
            city:'',
            street:'',
            street2:'',
            image:'',
            insurances:[],
            contacts:[],
            nok:{
                name:'',
                address:'',
                phone:'',
                email_address:'',
                relationship:''
            }
        }),
        states:[],
        nations:[],
        providers:[],
        plans:[],
        provider_types:[],
        areas:[]
        }
    },

    methods:{
        addContact(){
            this.patientData.contacts.push({
                name:'',
                address:'',
                phone:'',
                email:''
            })
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/patients/initials')
            .then(response => {this.loading = false; this.refreshPatients(response)})
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        handleStateChanged(state){this.areas = state.areas},
        refreshPatients(response){
            this.all_areas = response.data.areas;
            this.areas = response.data.areas;
            this.insurance_patientDatas_plans= response.data.plans;
            this.insurance_patientDatas_providers = response.data.providers;
            this.nations = response.data.nations;
            this.plans = response.data.plans;
            this.providers = response.data.providers;
            this.provider_types = response.data.provider_types;
            this.registration_types = response.data.registration_types;
            this.states = response.data.states;
        },
        removeContact(index){this.patientData.contacts.splice(index,1)},
        
        submit(){
            this.loading = true
            this.patientData.post('/api/emr/hims/patients').finally(()=> this.loading = false)
        },
        updateProfilePic(file){this.patientData.image = file},
        
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>