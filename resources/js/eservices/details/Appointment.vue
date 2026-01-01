<template>
    <div class="modal fade" id="applicantModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Patient' : 'Create Patient'"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EServiceFormPatient :editMode="editMode" :applicant="applicant" @refreshPatient="refreshAppointment"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="arrivalModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title" v-html="'Confirm Arrival'"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <EServiceFormArrival :appointment.sync="appointment" @refreshAppointment="refreshAppointment"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Make Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <EServiceFormPayment :appointment.sync="appointment" :paySpecific.sync="paySpecific" @refreshPayment="refreshAppointment"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reportModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title" v-show="editMode">Edit Report</h4>
                    <h4 class="modal-title" v-show="!editMode">New Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EServiceFormReport :editMode="editMode" :report.sync="appointment.report" :appointment.sync="appointment" @refreshAppointment="refreshAppointment"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card-header bg-navy">
            <h3 class="card-title">Appointment Detail</h3>
            <div class="card-tools" v-if="source == 'front_office' && appointment != null">
                <button v-if="appointment.payment == null" type="button" class="btn btn-xs btn-success" title="Make Payment" @click="makePayment(appointment)"><i class="fas fa-credit-card"></i></button>
                <button v-else-if="appointment.status < 4" @click="to_doctor(appointment)" type="button" class="btn btn-xs btn-primary" title="Process"><i class="fas fa-check"></i></button>
            </div>
            <div class="card-tools" v-if="source == 'radiologist' && appointment != null">
                <button v-if="appointment.report == null" class="btn btn-xs btn-primary" @click="addReport"><i class="fa fa-edit"></i> Write Report</button>
                <button v-else="appointment.report != null" class="btn btn-xs btn-warning" @click="editReport(appointment.report)"><i class="fa fa-edit"></i> Update Report</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1" v-if="appointment != null && appointment.service != null">
                    <div class="row" v-if="source == 'front_office'">
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Estimated Cost</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{appointment.payment != null ? currency(appointment.payment.amount): currency(100000)}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light bg-primary">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Status</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{appointment.payment == null ? 'Unpaid' : (appointment == 9 ? 'Cancelled' : 'Paid')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Estimated duration</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{appointment.service.duration != null ? appointment.service.duration+' mins': 'Not Defined'}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h4>Recent Activity</h4>
                            <div class="timeline">
                                <div v-if="appointment.radiologist != null && appointment.radiologist_id != null">
                                    <i class="fas fa-file bg-purple"></i>
                                    <div class="timeline-item">
                                        <span class="time text-white"><i class="fas fa-clock"></i> {{ExcelDate(appointment.report.created_at) }}</span>
                                        <h3 class="timeline-header bg-purple"><b>Radiologist</b></h3>
                                        <div class="timeline-body row">
                                            <div class="user-block col-md-12">
                                                <img class="img-circle img-bordered-sm" :src="appointment.radiologist != null ? './img/profile/'+appointment.radiologist.image : './img/profile/default.png'" :title="appointment.radiologist.first_name+' '+appointment.radiologist.last_name">
                                                <span class="username"><a href="#">{{FullName(appointment.radiologist)}}</a>
                                                </span>
                                                <span class="description">Posted: {{ExcelDate(appointment.report.created_at)}}</span>
                                            </div>
                                        </div>
                                        <div class="timeline-body row">
                                            <p class="col-md-12"><strong>{{ firstUp(appointment.report.summary)}}</strong></p>
                                            <p class="col-md-12" v-html="appointment.report.details"></p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="appointment.medical_officer != null && appointment.doctor_id != null && appointment.consultation != null">
                                    <i class="fas fa-user-md bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time text-white"><i class="fas fa-clock"></i> {{ ExcelDate(appointment.consultation.created_at) }}</span>
                                        <h3 class="timeline-header bg-green"><b>Medical Officer</b></h3>
                                        <div class="timeline-body row">
                                            <div class="user-block col-md-12">
                                                <img class="img-circle img-bordered-sm" :src="appointment.medical_officer != null && appointment.medical_officer.image != null ? '/img/profile/'+appointment.medical_officer.image : '/img/profile/default.png'" alt="user image">
                                                <span class="username"><a href="#">{{FullName(appointment.medical_officer)}}</a>
                                                </span>
                                                <span class="description">Posted: {{ExcelDate(appointment.consultation.created_at)}}</span>
                                            </div>
                                            <br />
                                            <p class="col-md-12"><b>{{ appointment.consultation.decision == 6 ? "Send to Xray for CXR": (appointment.consultation.decision == 7 ? "Send to Lab for Sputum": (appointment.consultation.decision == 8 ? "Kid under 11years": (appointment.consultation.decision == 10 ? "Patient Psotpoed": "Patient Cancelled"))) }}</b></p>
                                            <p class="col-md-12" v-html="appointment.consultation.remarks"></p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="appointment.xray_officer_id != null && appointment.xray_officer != null">
                                    <i class="fas fa-x-ray bg-navy"></i>
                                    <div class="timeline-item">
                                        <span class="time text-white"><i class="fas fa-clock"></i> {{ExcelDate(appointment.consultation.created_at) }}</span>
                                        <h3 class="timeline-header bg-navy"><b>Xray</b></h3>
                                        <div class="timeline-body row">
                                            <div class="user-block col-md-12">
                                                <img class="img-circle img-bordered-sm" :src="appointment.xray_officer != null && appointment.xray_officer.image != null ? '/img/profile/'+appointment.xray_officer.image : '/img/profile/default.png'" alt="user image">
                                                <span class="username"><a href="#">{{FullName(appointment.xray_officer) }}</a>
                                                </span>
                                                <span class="description">Posted: {{ExcelDate(appointment.xray_at) }}</span>
                                            </div>
                                            <br />
                                        </div>
                                    </div>
                                </div>
                                <div  v-if="appointment.front_office_id != null">
                                    <i class="fas fa-hospital-user bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time text-white"><i class="fas fa-clock"></i> {{ExcelDate(appointment.arrived_at)}}</span>
                                        <h3 class="timeline-header bg-blue"><b>Front Officer</b></h3>
                                        <div class="timeline-body row">
                                            <div class="user-block col-md-12">
                                                <img class="img-circle img-bordered-sm" :src="appointment.front_officer != null ? '/img/profile/'+appointment.front_officer.image : '/img/profile/default.png'" :title="appointment.front_officer != null ? appointment.front_officer.first_name+' '+appointment.front_officer.last_name : 'Undefined Officer'">
                                                <span class="username"><a href="#">{{FullName(appointment.front_officer)}}</a>
                                                </span>
                                                <span class="description">Posted: {{ExcelDate(appointment.arrived_at) }}</span>
                                            </div>
                                            <br /><br />
                                            <p class="col-md-12" v-html="appointment.front_office_remark"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2 ">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h6 class="card-title"><i class="fas fa-user"></i> Applicant Detail</h6>
                            <div class="card-tools" v-if="source == 'front_office' && applicant != null && appointment.status < 10">
                                <button type="button" class="btn btn-sm btn-primary" title="Edit Details" v-if="appointment.status != 10" @click="editApplicant(applicant)"><i class="fas fa-edit"></i></button>
                            </div>
                            <div class="card-tools" v-if="source == 'front_admin' && applicant != null">
                                <button type="button" class="btn btn-sm btn-primary" title="Edit Details" @click="editApplicant(applicant)"><i class="fas fa-edit"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-muted">
                                <img :src="(applicant != null && applicant.image != null) ? '/img/applicants/'+applicant.image : '/img/profile/default.png'" class="img-fluid" :title="FullName(applicant)" />
                                <p class="text-sm">Surname | First Name Other Name: <b class="d-block">{{FullName(applicant)}}</b></p>
                                <p class="text-sm">Sex | Age: <b class="d-block">{{applicant.sex}} | {{age(applicant.dob) }} years</b></p>
                                <p class="text-sm">Nationality: <b class="d-block">{{applicant.nationality != null && applicant.nationality_id != null ? applicant.nationality.name : 'None Given'}}</b></p>
                                <p class="text-sm">Passport No. | Visa Type: <b class="d-block">{{applicant.passport_no != null ? applicant.passport_no :'Request Passport' }} | {{applicant.visa_type != null ? applicant.visa_type :'Request Visa Type' }}</b></p>
                                <p class="text-sm">Registered at: <b class="d-block">{{ExcelDate(applicant.created_at) }}</b></p>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script> 
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
export default {
    data() {
        return {
            applicant: {},
            editMode: false,
            loading: false,
            nations: [],
            payment: {},
            paySpecific: false,
            report: {},
        }
    },
    emits: ['refreshAppointment'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        addPayment(){
            this.loading = true;
            this.paySpecific = false;
            $('#paymentModal').modal('show');
            this.loading = false;
        },
        addReport(){
            this.editMode = false;
            $('#reportModal').modal('show');
        },
        closeModals(){
            $('#applicantModal').modal('hide');
            $('#paymentModal').modal('hide');
            $('#reportModal').modal('hide');
        },
        editApplicant(patient){
            this.loading = true;
            this.editMode = true;
            this.applicant = patient;
            $('#applicantModal').modal('show');
            this.loading = false;
        },
        editReport(report){
            this.loading = true;
            this.editMode = true;
            this.report = report;
            $('#reportModal').modal('show');
            this.loading = false;
        },
        makePayment(appointment){
            this.loading = true;
            this.paySpecific = true;
            $('#paymentModal').modal('show');
            this.loading = false;
        },
        refreshAppointment(response) {
            this.closeModals();
            this.$emit('refreshAppointment', response)
        },
        to_doctor(appointment){
            //this.loading = true;
            //this.appointment = appointment;
            $('#arrivalModal').modal('show');
            this.loading = false;
        },
    },
    props:{
        appointment: Object,
        source: String,
    },
    watch:{
        appointment(){
            if (this.appointment != null){
                this.applicant = this.appointment.patient != null ? this.appointment.patient : {};
                this.payment = this.appointment.payment != null ? this.appointment.payment : {};
            }
        }
    }
}
</script>