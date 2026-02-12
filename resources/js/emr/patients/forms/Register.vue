<template>
<section class="container-fluid">
    <div class="card">
        <form @submit.prevent="createPatient">
            <div class="card-header bg-navy">
                <h4 class="card-title">New Registration</h4>
                <div class="card-tools">
                    <select class="form-control form-control-sm" v-model="form.patient_type" required>
                    <option value="">Select Registration Type</option>
                    <option v-for="r in registration_types" :key="r.id" :value="r.id">{{ r.name }}</option>
                    </select>
                </div>
            </div>

            <div class="card-body overlay-wrapper">
                <div v-if="loading" class="overlay dark"><i class="fas fa-sync-alt fa-spin"></i></div>
                <patient-basic-details :form="form" :nations="nations" :states="states" :areas="areas" @state-changed="onStateChanged"/>
                <patient-insurance :provider-types="provider_types" :providers="providers" :plans="plans" v-model="form.insurances"/>
                <next-of-kin v-model="form.nok" />
                <contact-repeater v-model="form.contacts" title="Contacts" />
            </div>

            <div class="card-footer"><button class="btn btn-dark" type="submit">Submit</button></div>
        </form>
    </div>
</section>
</template>

<script>
import PatientBasicDetails from '../components/Basic.vue'
import PatientInsurance from '../components/Insurances.vue'
import ContactRepeater from '../components/Contact.vue'
import NextOfKin from '../components/NOK.vue'

const defaultPatient = () => ({
    patient_type: '',
    title: '',
    first_name: '',
    last_name: '',
    middle_name: '',
    dob: '',
    sex: '',
    phone: '',
    alt_phone: '',
    email: '',
    occupation: '',
    street: '',
    street2: '',
    city: '',
    state_id: '',
    area_id: '',
    nationality_id: '',
    image: '',
    nok: {
        name: '',
        phone: '',
        relationship: '',
        email_address: '',
        address: ''
    },
    contacts: [],
    insurances: []
})

export default {
    components: { PatientBasicDetails, PatientInsurance, ContactRepeater, NextOfKin },
    data() {
        return {
        loading: false,
        form: new Form(defaultPatient()),
        nations: [],
        states: [],
        areas: [],
        providers: [],
        plans: [],
        provider_types: [],
        registration_types: []
        }
    },
    mounted() {
        axios.get('/api/emr/hims/patients/initials').then(({ data }) => {
            this.nations = data.nations
            this.states = data.states
            this.providers = data.providers
            this.plans = data.plans
            this.provider_types = data.provider_types
            this.registration_types = data.registration_types
        })
    },
    methods: {
        onStateChanged(stateId) {
            const state = this.states.find(s => s.id === stateId)
            this.areas = state ? state.areas : []
        },
        createPatient() {
            this.loading = true
            this.form.post('/api/emr/hims/patients')
            .then(() => this.$router.push('/hims/patients'))
            .finally(() => this.loading = false)
        }
    }
}
</script>