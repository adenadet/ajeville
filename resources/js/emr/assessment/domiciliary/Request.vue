<template>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">Patient Detail</div>
                <div class="card-body">
                    <div class="text-center user-info">
                        <img class="img-fluid" :src="(patient.image) ? '/img/profile/'+patient.image : ''" width="300" height="auto" alt="avatar">
                        <p class=""></p>
                    </div>
                    <div class="user-info-list">
                        <div class="">
                            <ul class="contacts-block list-unstyled">
                                <li class="contacts-block__item">
                                    <i class="fa fa-user mr-1" width="24" height="24"></i> {{patient | patientName}} 
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-calendar mr-1" width="24" height="24"></i> {{patient.dob | ExcelDate}}
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-map-marker mr-1" width="24" height="24"></i><span v-html="patient.uk_address"></span></li>
                                <li class="contacts-block__item">
                                    <a :href="'mailto:'+patient.email"><i class="fa fa-envelope mr-1" width="24" height="24"></i> {{patient.email}}</a>
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-phone mr-1" width="24" height="24"></i> {{patient.phone}} {{patient.alt_phone ? ', '+patient.alt_phone: ''}} 
                                </li>
                            </ul>
                        </div>                                    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-7 col-sm-12">
            <div class="row">
                <div class="col-md-2">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item"><a class="nav-link active" href="#tasks" data-toggle="tab">Tasks</a></li>
                        <li class="nav-item"><a class="nav-link" href="#allergies" data-toggle="tab">Allergies</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contacts" data-toggle="tab">Contacts</a></li>
                        <li class="nav-item"><a class="nav-link" href="#prescriptions" data-toggle="tab">Prescriptions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#vitals" data-toggle="tab">Vitals</a></li>

                        <!--<li class="nav-item"><a class="nav-link" href="#bio-data" data-toggle="tab">Bio Data</a></li>
                        <li class="nav-item"><a class="nav-link" href="#next-of-kin" data-toggle="tab">Next of Kin</a></li>
                        
                        <li class="nav-item"><a class="nav-link" href="#administrations" data-toggle="tab">Drug Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tasks" data-toggle="tab">Tasks</a></li>
                        <li class="nav-item"><a class="nav-link" href="#plans" data-toggle="tab">Plans</a></li>
                        <li class="nav-item"><a class="nav-link" href="#fluid" data-toggle="tab">Fluid Chart</a></li>-->
                    </ul>
                </div>
                <div class="col-md-10">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tasks">
                            <HimsPatientTasks />
                        </div>
                        <div class="tab-pane" id="allergies">
                            <HimsPatientAllergies />
                        </div>
                        <div class="tab-pane" id="contacts">
                            <HimsPatientContacts />
                        </div>
                        <div class="tab-pane" id="prescriptions">
                            <HimsPatientPrescriptions /> 
                        </div>
                        <div class="tab-pane" id="vitals">
                            <HimsPatientVitals /> 
                        </div>
                        <!--<div class="tab-pane" id="administrations">
                            <PatientChartAdmin />
                        </div>
                        
                        <div class="tab-pane" id="plans">
                            <PatientChartPlan />
                        </div>
                        <div class="tab-pane" id="fluid">
                            <PatientChartFluid />
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>                           
</div>
</template>
<script>
import HimsPatientAllergies from '../patients/Allergies.vue';
import HimsPatientContacts from '../patients/Contacts.vue';
import HimsPatientPrescriptions from '../patients/Prescriptions.vue';
import HimsPatientTasks from '../patients/Tasks.vue';
import HimsPatientVitals from '../patients/Vitals.vue';
export default {
    components:{
        HimsPatientContacts, HimsPatientAllergies, HimsPatientPrescriptions, HimsPatientTasks, HimsPatientVitals, 
    },
    data() {
        return {
            domiciliaries: {},
            domiciliary: {},
            nations: [],
            patient: {},
            patient_tasks: [],
            request: {},
            services: [],
            staffs: [],
            user: {},
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshDomiciliaryPatient', () => { this.getInitials();});
        Fire.$on('refreshResponse', response => {
            this.refreshDomiciliaries(response);
            $('#paymentModal').modal('hide');
            $('#patientModal').modal('hide');
            $('#appointmentModal').modal('hide');
            $('#requestModal').modal('hide');
        });
        Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('api/emr/domiciliary/search?q='+query)
            .then((response ) => {this.applicants = response.data.applicants;})
            .catch(()=>{});
        });
    },
    methods: {
        addRequest(request){
            this.$Progress.start();
            this.request = request;
            Fire.$emit('requestDataFill', {});
            $('#requestModal').modal('show');
            this.$Progress.finish();
        },
        assignRequest(request){
            this.$Progress.start();
            this.request = request;
            Fire.$emit('assessRequestDataFill', this.request);
            $('#assignModal').modal('show');
            this.$Progress.finish();
        },
        closeRequest(){
            $('#requestModal').modal('hide');
        },
        editRequest(request){
            this.$Progress.start();
            this.editMode = true;
            this.request = request;
            this.patient = request.patient;
            Fire.$emit('requestDataFill', request);
            $('#requestModal').modal('show');
            this.$Progress.finish();
        },
        addAppointment(){
            this.$Progress.start();
            this.editMode = false;
            this.appointment = {};
            Fire.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        getInitials() {
            axios.get('/api/emr/domiciliary/requests/'+this.$route.params.id)
            .then(response => {this.refreshDomiciliaries(response)})
            .catch(() => {
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Your requests did not loaded successfully',})
            });
        },
        refreshDomiciliaries(response) {
            this.nations = response.data.nations;
            this.domiciliaries = response.data.domiciliaries;
            this.domiciliary = response.data.domiciliary;
            this.patients = response.data.patients;
            this.staffs = response.data.employees;
            this.patient = response.data.patient;
            this.patient_tasks = response.data.patient_tasks; 
            Fire.$emit('refreshPatientAllergies', this.patient);
            Fire.$emit('refreshPatientContacts', this.patient);
            Fire.$emit('refreshPatientPrescriptions', this.patient);
            Fire.$emit('refreshPatientTasks', this.patient); 
            Fire.$emit('refreshPatientVitals', this.patient); 
        }
    },
    props: {}
}
</script>