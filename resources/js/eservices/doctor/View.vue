<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#consent" data-toggle="tab">Consent Form</a></li>
                    <li class="nav-item"><a class="nav-link" href="#consultation" data-toggle="tab">Consultation</a></li>
                    <li class="nav-item"><a class="nav-link" href="#report" data-toggle="tab">Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="#laboratory" data-toggle="tab">Laboratory</a></li>
                    <li class="nav-item"><a class="nav-link" href="#certificate" data-toggle="tab">Issue Certificate</a></li>
                    <li class="nav-item"><a class="nav-link" href="#referral" data-toggle="tab" v-show="appointment.issue_action != null && appointment.issue_action != 'certificate'">Referral</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane" id="certificate">
                            <div class="card" v-if="((appointment.report == null) && (appointment.laboratory == null)) && (appointment.status != 8)"><div class="card-header">Awaiting Report</div><div class="card-body"><p>The report for this applicant is still pending, you can call the </p></div></div>
                            <EServiceDetailIssueView :appointment="appointment" v-else-if="appointment.issuer != null" />
                            <EServiceDocFormIssue v-else-if="(appointment.consultation.decision == 8 || appointment.issuer == null)" :appointment="appointment" />    
                            <div class="card" v-else><div class="card-header">Awaiting Report</div><div class="card-body"><p>The report for this applicant is still pending, you can call the </p></div></div>
                        </div>
                        <div class="tab-pane active" id="consent">
                            <EServiceDocConsentView :consent="appointment.consent" :appointment="appointment" :patient="appointment.patient" :consultation="appointment"/>
                        </div>
                        <div class="tab-pane" id="consultation">
                            <EServiceDocConsultationView  :consultation.sync="consultation" :appointment.sync="appointment" :patient.sync="appointment.patient"/>
                        </div>
                        <div class="tab-pane" id="laboratory">
                            <EServiceDocLaboratoryView :findings.sync="findings" :consultation.sync="appointment" :patient.sync="appointment.patient" :laboratory.sync="appointment.laboratory"/>
                        </div>
                        <div class="tab-pane" id="referral">
                            <div class="card" v-if="((appointment.referral == null) && (appointment.status == 10) && (appointment.issuer != null) && (appointment.consultation.summary != 'normal'))">
                                <div class="card-header">Issue Referral</div>
                                <div class="card-body">
                                    <EServiceDocFormReferral :appointment.sync="appointment" />
                                </div>
                            </div>
                            <div class="card" v-else-if="appointment.referral != null">
                                <div class="card-header">Referral</div>
                                <div class="card-body">
                                    <EServiceDetailReferral :appointment.sync="appointment" :referral.sync="appointment.referral" />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="report">
                            <EServiceDetailReport  :findings.sync="findings" :consultation.sync="appointment" :patient.sync="appointment.patient" :report.sync="appointment.report"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>                           
</div>
</template>

<script>
export default {
    data(){
        return  {

        }
    },
    mounted() {
    },
    methods:{
        
    },
    props:{
        consent: Object,
        consultation: Object,
        patient: Object,
        appointment: Object,
        editMode: Boolean,
        findings: Array,
    },
    watch:{

    }
}
</script>