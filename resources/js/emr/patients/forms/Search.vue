<template>
    <section class="container-fluid overlay-wrapper">
        <div class="modal fade" id="patientModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-html="editMode ? 'Edit Patient' : 'Create Patient'"></h4>
                        <button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <PatientFormRegistration :editMode="editMode" :nations="nations" :patient="patient" /> 
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group row">
            <label v-if="showLabel" for="inputEmail3">Search Patient</label>
            <div class="input-group input-group-sm">
                <model-list-select class="form-control" :list="patients" v-model="patient_id" option-value="unique_id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" />
                <div class="input-group-append">
                    <button class="btn btn-sm bg-dark" @click="getPatient" type="button"><i class="fa fa-search mr-1"></i></button>
                    <button class="btn btn-sm bg-primary" @click="newPatient" type="button"><i class="fa fa-user-plus mr-1"></i></button>
                </div>
            </div>
        </div>
        
    </section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    data() {
        return {
            editMode: true,
            nations: [],
            patient: {},
            patients: [],
            patient_id: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        closeModal(){
            $('#patientModal').modal('hide');
        },
        codeAndNameAndDesc (item) {
            return `${item.user.last_name}, ${item.user.first_name} ${item.user.middle_name} (${item.unique_id})`
        },
        getInitials() {
            axios.get('/api/emr/hims/patients/all')
            .then(response => {this.refreshPatients(response)})
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        getPatient(){
            this.loading = true;
            axios.get('/api/emr/hims/patients/'+this.patient_id)
            .then(response => {
                this.$store.dispatch('setPatient', response.data.patient);
            })
            .catch(() => {
                this.loading = false;
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
            Fire.$emit('getPatient', this.patient_id);
            this.loading = false;
        },
        newPatient(){
            this.loading = true;
            this.editMode = false;
            //Fire.$emit('ApplicantDataFill', {});
            this.patient = {};
            $('#patientModal').modal('show');
            this.loading = false;
        },
        refreshPatients(response) {
            this.patients = response.data.patients;
        }
    },
    props: {
        source: String,
        showLabel: Boolean,
    },
}
</script>