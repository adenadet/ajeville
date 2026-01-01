<template>
<section class="overlay-wrapper p-0 row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ type }} Vitals</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 300px;">
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Visit ID</th>
                            <th>Status</th>
                            <th v-if="type != 'Pending'">NEWS Score</th>
                            <th v-if="type != 'Pending'">Glasgow Scale</th>
                            <th v-if="type != 'Pending'">Remark</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="vitals != null && vitals.length != 0">
                        <tr v-for="vital in vitals" :key="vital.id">
                            <td>{{ ExcelDate(vital.created_at) }}</td>
                            <td>{{ vital.patient != null && vital.patient.user != null ? FullName(vital.patient.user) : 'No Patient' }}</td>
                            <td>{{ vital.visit != null ? vital.visit.unique_id : 'No Visit Created' }}</td>
                            <td>{{ vital.status }}</td>
                            <td v-if="type != 'Pending'">{{ vital.status < 1 ? 'Pending' : vital.news_score }}</td>
                            <td v-if="type != 'Pending'">{{ vital.status < 1 ? 'Pending' : vital.glascow_score }}</td>
                            <td v-if="type != 'Pending'">{{ vital.status < 1 ? 'Pending' : vital.remark }}</td>
                            <td>
                                <span class="nav-link" data-toggle="dropdown" href="#"><i class="fa fa-bars"></i></span>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <button class="btn btn-block dropdown-item" @click="addVital(vital.id)"><i class="fas fa-pen mr-2 text-primary"></i> Submit Vital</button>
                                    <button class="btn btn-block dropdown-item" @click="editVital(vital)"><i class="fas fa-edir mr-2 text-primary"></i> Update Vital</button>
                                    <button class="btn btn-block dropdown-item" @click="deleteVital(vital)"><i class="fas fa-trash mr-2"></i>Delete Vital</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr><td :colspan="type != 'Pending' ? 8 : 5" class="text-center">No Vitals Found <br /><button class="btn btn-primary" @click="addVital(null)">Add New Vital</button></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            vitalData: new Form({
                consultation_id: null,
                patient_id: null,
                visit_id: null,
                respiration_rate: '',
                spo2: '',
                bp_systolic: '',
                bp_diastolic: '',
                pulse: '',
                temperature: '',
                blood_glucose: '',
                consciousness: '',
            }),
        };
    },
    mounted() {
        this.fetchConsultations();
        this.fetchVitals();
    },
    methods: {
        fetchConsultations() {
        fetch('/api/consultations')
            .then((res) => res.json())
            .then((data) => (this.consultations = data));
        },
        fetchVitals() {
        fetch('/api/vitals')
            .then((res) => res.json())
            .then((data) => (this.vitals = data));
        },
        selectConsultation(consultation) {
        this.selectedConsultation = consultation;
        this.form.patient_id = consultation.patient_id;
        this.form.consultation_id = consultation.id;
        },
        submitVitals() {
        fetch('/api/vitals', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            },
            body: JSON.stringify({
            ...this.form,
            patient_id: this.selectedConsultation.patient_id,
            consultation_id: this.selectedConsultation.id,
            }),
        })
            .then((res) => res.json())
            .then(() => {
            this.fetchVitals();
            this.selectedConsultation = null;
            });
        },
        formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        },
    },
    props:{
        editMode: Boolean,
        type: String,
        vitals: Array,
    }
};
</script>

