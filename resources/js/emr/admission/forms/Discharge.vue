<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="dischargePatient()">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter category name" v-model="categoryData.name" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><strong>Final Diagnosis</strong></label>
                    <DiagnosisComponent :patient_id="admission.patient_id" :encounter_id="admission.encounter_id" v-model="form.diagnosis_ids" />
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Discharge Medications</strong></label>
                <PrescriptionComponent
                    :patient_id="admission.patient_id"
                    :encounter_id="admission.encounter_id"
                    context="discharge"
                />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Follow-Up Appointments</strong></label>

                <div v-for="(appt, index) in form.appointments" :key="index" class="row mb-2">
                    <div class="col-md-4">
                        <input type="text" v-model="appt.doctor_name" class="form-control" placeholder="Doctor Name">
                    </div>
                    <div class="col-md-4">
                        <input type="date" v-model="appt.date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="time" v-model="appt.time" class="form-control">
                    </div>
                </div>

                <button class="btn btn-sm btn-outline-primary" @click="addAppointment">
                    + Add Appointment
                </button>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Activity / Directions</strong></label>
                <QuillEditor content-type="html" theme="snow" v-model:content="form.directions" rows="4" class="form-control" placeholder="E.g. No lifting over 10kg, daily 30-minute walk..." />
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Diet Instructions</strong></label>
                <QuillEditor v-model:content="form.diet" rows="3" class="form-control" theme="snow" content-type="html" placeholder="Permitted / restricted foods..." />
            </div>
        </div>

        <button class="btn btn-danger" @click="submitDischarge" :disabled="loading">
            <span v-if="loading">Processing...</span>
            <span v-else>Discharge Patient</span>
        </button>
    </form>
</section>
</template>
<script>
import axios from 'axios'
import DiagnosisComponent from '@/components/emr/consultant/components/Diagnosis.vue'
import PrescriptionComponent from '@/components/emr/consultant/components/Prescription.vue'

export default {
    props: {
        admission: {
            type: Object,
            required: true
        }
    },

    components: {
        DiagnosisComponent,
        PrescriptionComponent
    },

    data() {
        return {
            loading: false,
            form: {
                diagnosis_ids: [],
                appointments: [],
                directions: '',
                diet: ''
            }
        }
    },

    methods: {
        addAppointment() {
            this.form.appointments.push({
                doctor_name: '',
                date: '',
                time: ''
            })
        },
        async submitDischarge() {
            this.loading = true
            try {
                await axios.post(
                    `/api/emr/admissions/requests/${this.admission.id}/discharge`,
                    this.form
                )
                this.$toast.success('Patient discharged successfully')
                this.$emit('discharged')
            } catch (e) {
                console.error(e)
                this.$toast.error('Failed to discharge patient')
            }
            this.loading = false
        }
    }
}
</script>