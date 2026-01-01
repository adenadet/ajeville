<template>
<section class="overlay-wrapper p-0 row">
    <div class="card-body table-responsive p-0" style="height: 300px;">    
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Patient</th>
                    <th>Visit ID</th>
                    <th>Requested By</th>
                    <th>Status</th>
                    <th>Processed By</th>
                    <th>Processed At</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="admissions != null && AdmissionRequest.length != null">
                <tr v-for="(admission, index) in admissions" :key="admission.id">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ admission.patient != null && admission.patient.user != null ? PatientFullName(admission.patient) : 'No Patient Assigned' }}</td>
                    <td>{{ admission.visit != null ? admission.Visit.unique_id : 'No Visit Assigned' }}</td>
                    <td>{{ admission.status }}</td>
                    <td>{{ admission.requester != null ? FullName(admission.requester) : 'No Staff' }}</td>
                    <td>{{ ExcelDate(admission.created_at) }}</td>
                    <td>
                        <span class="nav-link" data-toggle="dropdown" href="#"><i class="fa fa-bars"></i></span>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button class="btn btn-block dropdown-item" @click="acceptAdmission(admission)"><i class="fas fa-pen mr-2 text-primary"></i> Submit Vital</button>
                            <button class="btn btn-block dropdown-item" @click="assignBed(admission)"><i class="fas fa-edir mr-2 text-primary"></i> Update Vital</button>
                            <button class="btn btn-block dropdown-item" @click="cancelAdmission(admission)"><i class="fas fa-trash mr-2"></i>Delete Vital</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>    
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
    mounted() {},
    methods: {
        acceptAdmission(admission){
            
        },
        assignBed(admission){},
        cancelAdmission(admission){},
    },
    props:{
        admissions: Array,
        editMode: Boolean,
        type: String,
    }
};
</script>