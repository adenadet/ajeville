<template>
<section class="overlay-wrapper">
    <form @submit.prevent="editMode? updateVital() : createVital()">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.respiration_rate" type="number" class="form-control" placeholder="Respiration Rate" required  />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.spo2" step="0.1" type="number" class="form-control" placeholder="SpO2"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.bp_systolic" type="number" class="form-control" placeholder="BP Systolic"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.bp_diastolic" type="number" class="form-control" placeholder="BP Diastolic"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.pulse" type="number" class="form-control" placeholder="Pulse"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.temperature" type="number" step="0.1" class="form-control" placeholder="Temperature"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input v-model="vitalData.blood_glucose" type="number" class="form-control" placeholder="Blood Glucose" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <select v-model="vitalData.consciousness" type="text" class="form-control">
                        <option value="" disabled selected>Select Consciousness</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="button" class="btn btn-secondary me-2" @click="consultation = null">Cancel</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            vitalData: new Form({
                patient_id: null,
                visit_id: null,
                consultation_id: null,
                blood_glucose: '',
                bp_systolic: '',
                bp_diastolic: '',
                consciousness: '',
                pulse: '',
                respiration_rate: '',
                spo2: '',
                temperature: '',
            }),
        };
    },
    mounted() {
        this.getInitials();
        //this.fetchConsultations(); this.fetchVitals();
    },
    methods: {
        createVital(){},
        getInitials(){
            this.loading = true;
            axios.get('/api/emr/nursing/vitals/initials')
            .then(response =>{
                this.visits = response.data.visits;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vital Form was not loaded properly',
                })
            });
        },
        updateVital(){
            this.loading = true;

        },
    },
    props:{
        editMode: Boolean,
        vital: Object,
    },
    watch:{
        vital(){
            if(this.editMode){
                this.vitalData.fill(this.vital);
            }
            else{
                this.vitalData.reset();
                this.vitalData.consultation_id = this.vital.id;
                this.vitalData.patient_id = this.vital.patient_id;
                this.vitalData.visit_id = this.vital.visit_id;
            }
        }
    }
};
</script>