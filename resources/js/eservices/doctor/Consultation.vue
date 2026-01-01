<template>
<section class="content-header pt-0">
    <div class="modal fade" id="patientModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Patient Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EServiceDocPatientView :patient.sync="patient" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">Consultation Detail</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-xs btn-success" title="View Applicant" @click="showPatient()"><i class="fas fa-eye"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                    <EServiceDocFormConsent v-if="appointment.consent == null" :patient.sync="patient" :consent.sync="appointment.consent" :consultation.sync="consultation" :editMode="editMode" :appointment.sync="appointment"/>
                    <EServiceDocFormScreening v-else-if="appointment.consultation == null" :consultation.sync="consultation" :editMode="editMode" :appointment.sync="appointment" />
                    <EServiceDocView v-else :consultation.sync="appointment.consultation" :appointment.sync="appointment" :editMode="!editMode" :patient.sync="patient" :findings.sync="findings"/>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import ConsentForm from './forms/Consent.vue'; 
export default {
    data() {
        return {
            editMode: false,
            findings: [],
            appointment: {},
            consultation: {},
            consultations: {},
            patient: {},
            nations: [],
        };
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials() {
            axios.get("/api/emr/consultations/" + this.$route.params.id)
            .then(response => {
                this.refreshAppointment(response);
                this.$emit("refreshAppointment", response);
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: "error",
                    title: "Your appointments did not loaded successfully",
                });
            });
        },
        refreshAppointment(response) {
            this.appointment = response.data.appointment;
            this.consultation = response.data.appointment;
            this.patient = this.consultation.patient;
            this.findings = response.data.findings;
            this.nations = response.data.nations;
        },
        showPatient(){
            $('#patientModal').modal('show');
        },
    },
    components: { ConsentForm }
}
</script>