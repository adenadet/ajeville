<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="checkInPatient">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Patient</label>
                <div class="form-control">{{ patient != null ? FullName(patient.user) : 'Awaiting Details' }} </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" v-if="patient?.patient_type == 2">
                <label class="form-label">Payment Plan</label>
                <select class="form-control" id="payment_type" name="payment_type" v-model="checkInData.patient_insurance.payment_type">
                    <option value=''>Cash</option>
                    <option v-for="insurance_type in insurance_types" :value="insurance_type.id">{{ insurance_type.name }}</option>
                </select>            
            </div>
        </div>
        <div class="row" v-if="patient?.patient_type == 2 && checkInData.patient_insurance.payment_type != ''">
            <div class="col-md-3">
                <label class="form-label">Provider</label>
                <select class="form-control" id="provider_id" name="provider_id" v-model="checkInData.patient_insurance.provider_id">
                    <option v-for="provider in filtered_providers" :value="provider.id">{{ provider.name }}</option>
                </select>            
            </div>
            <div class="col-md-3">
                <label class="form-label">Plan</label>
                <select class="form-control" id="plan_id" name="plan_id" v-model="checkInData.patient_insurance.plan_id">
                    <option v-for="plan in filtered_plans" :value="plan.id">{{ plan.name }}</option>
                </select>            
            </div>
            <div class="col-md-3">
                <label class="form-label">Policy Number</label>
                <input type="text" class="form-control" id="plan_id" name="plan_id" v-model="checkInData.patient_insurance.policy_number">
            </div>
            <div class="col-md-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" v-model="checkInData.patient_insurance.expiry_date">
            </div>
        </div>

        <div class="row" v-if="patient.patient_type == 1">
            <div class="col-md-12">
                <label class="form-label">Payment Plan</label>
                <select class="form-control" id="plan_id" name="plan_id" v-model="checkInData.plan_id">
                    <option v-for="plan in patient.insurances" :value="plan.id">{{ plan.name }}</option>
                </select>            
            </div>
        </div>
        <button class="btn btn-success btn-sm mt-3" :disabled="loading">
            <i class="fas fa-check"></i> Check In
        </button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            checkInData: new Form({
                appointment_id: '',
                branch_id: '',
                consultant_id: '',
                payment_type: '',
                patient_id: '',
                plan_id: '',
                provider_id: '',
                specialty_id: '',
                service_type_id: '',
                patient_insurance: {
                    payment_type: '',
                    plan_id: '',
                    policy_number: '',
                    provider_id: '',
                    expiry_date: '',
                }
            }),
            filtered_plans: [],
            filtered_providers: [],
            insurance_types: [],
            loading: false,
            patient: {},
            plans: [],
            providers: [],
        }
    },
    computed: {
        hasHMO() {
            return !!this.patient?.hmo_plan
        },
    },
    methods: {
        checkInPatient() {
            this.loading = true
            this.checkInData.post('/api/emr/hims/appointments/check_in')
            .then(res => {
                this.$router.push('/emr/front_office/visits/' + res.data.visit.id)
            })
            .catch(() => {
                this.$swal.fire('Error', 'Unable to check in patient', 'error')
            })
            .finally(() => {
                this.loading = false
            })
        },
        filterPlans() {
            this.filteredPlans = this.plans.filter(
                p => p.provider_id === this.checkInData.provider_id
            )
        },
        getInitials() {
            axios.get('/api/emr/hims/visits/initials')
            .then(res => {
                this.insurance_types = res.data.insurance_types;
                this.providers = res.data.providers;
                this.filtered_providers = res.data.providers;
                this.plans = res.data.plans;
                this.filtered_plans = res.data.plans;
            })
        }
    },
    mounted() {
        this.getInitials()
        
    },
    props:{
        appointment: Object,
    },
    watch:{
        'checkInData.patient_insurance.payment_type'(val) {
            // Cash selected
            if (!val) {
                this.filtered_providers = []
                this.filtered_plans = []
                this.checkInData.patient_insurance.provider_id = ''
                this.checkInData.patient_insurance.plan_id = ''
                return
            }

            // Filter providers by hmo_type_id
            this.filtered_providers = this.providers.filter(
                p => p.hmo_type_id === val
            )

            // Reset downstream selections
            this.checkInData.patient_insurance.provider_id = ''
            this.filtered_plans = []
        },

        // 2️⃣ Watch provider → filter plans
        'checkInData.patient_insurance.provider_id'(providerId) {
            if (!providerId) {
                this.filtered_plans = []
                this.checkInData.patient_insurance.plan_id = ''
                return
            }

            this.filtered_plans = this.plans.filter(
                plan => plan.provider_id === providerId
            )

            this.checkInData.patient_insurance.plan_id = ''
        },

        appointment(){
            this.loading = true;
            this.checkInData.appointment_id = this.appointment.id;
            this.checkInData.branch_id = this.appointment.branch_id;
            this.checkInData.consultant_id = this.appointment.consultant_id;
            this.checkInData.payment_type = this.appointment.payment_type;
            this.checkInData.patient_id = this.appointment.patient_id;
            this.checkInData.plan_id = this.appointment.plan_id;
            this.checkInData.provider_id = this.appointment.provider_id;
            this.checkInData.service_type_id = this.appointment.service_type_id;
            this.checkInData.specialty_id = this.appointment.specialty_id;
            this.patient = this.appointment.patient;
            if (this.patient == null || this.patient.insurance == null){
                this.checkInData.patient_insurance = {
                    payment_type: '',
                    plan_id: '',
                    policy_number: '',
                    provider_id: '',
                    expiry_date: '',
                };
            }
            else{

            }
            this.loading = false; 
        },
    }
}
</script>
