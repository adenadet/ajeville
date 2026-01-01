<template>
<section class="content-header">
    <div class="container-fluid">
        <div class="modal fade" id="requestModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-html="editMode ? 'Edit Domiciliary Request' : 'Create Domiliciary Request'"></h4>
                        <button type="button" class="close" @click="closeRequest()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <DomFormRequest :patient="patient" :patients="patients" :request="request" :editMode="editMode"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="assignModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Assign Domiliciary Request</h4>
                        <button type="button" class="close" @click="closeRequest()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <DomFormAssignAssessment :domiliciary="request" :staffs="staffs" :request="request" :patient="request.patient"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Domiliciary Services</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-success" @click="addRequest"><i class="fa fa-calendar-plus"></i> Create Booking</button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Start Date</th>
                                    <th>Payment Type</th>
                                    <th>Accessment</th>
                                    <th>Request</th>
                                    <th>Tasks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="domiliciaries.data == null || domiliciaries == null">
                                <tr><td colspan="6" class="text-center">You have not made any domiliciary services yet</td></tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="domiliciary in domiliciaries.data" :key="domiliciary.id">
                                    <td>
                                        <div class="user-block">
                                            <img class="img-circle" :src="domiliciary.patient.image != null ? '/img/patients/'+domiliciary.patient.image: '/img/profile/default.png'">
                                            <span class="username">{{domiliciary.patient.first_name+' '+domiliciary.patient.middle_name+' '+domiliciary.patient.last_name}}</span>
                                            <span class="description">Reg. {{domiliciary.patient.created_at | excelDate}} | Aged: {{domiliciary.patient.dob | age }} years</span>
                                        </div>
                                    </td>
                                    <td>{{domiliciary.start_date | excelDate}}</td>
                                    <td>{{domiliciary.payment_type}}</td>
                                    <td>{{domiliciary.accessment != null ? 'Completed' : (domiliciary.assessor != null ? 'Ongoing': 'Not Assigned')}}<br />
                                        <small>{{domiliciary.assessor!= null && domiliciary.assessor.user != null ? domiliciary.assessor.unique_id+' | '+domiliciary.assessor.user.first_name+' '+domiliciary.assessor.user.last_name : '' }}</small>
                                    </td>
                                    <td><span>{{domiliciary.hca_daily != 0 ? domiliciary.hca_daily+' HCA' : ''}}</span>
                                        <span>{{domiliciary.rn_daily != 0 ? domiliciary.rn_daily+' RN' : ''}}</span>
                                        <span>{{domiliciary.bsc_daily != 0 ? domiliciary.bsc_daily+' BSC' : ''}}</span>
                                    </td>
                                    <td>{{domiliciary.tasks != null ? domiliciary.tasks.length() : 'Awaiting'}}</td>
                                    <td>
                                        <div class="btn btn-group">
                                            <button class="btn btn-success btn-sm" @click="assignRequest(domiliciary)" title="Assign for Assessment"><i class="fa fa-user"></i></button>
                                            <button class="btn btn-primary btn-sm" @click="editRequest(domiliciary)" title="Edit Request"><i class="fa fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" title="Delete Request"><i class="fa fa-trash"></i></button>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="domiciliaries" @pagination-change-page="getDomiciliaries">
                            <span slot="prev-nav">&lt; Previous </span>
                            <span slot="next-nav">Next &gt;</span>
                        </pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            domiciliaries: {},
            applicant: {},
            applicants: {},
            appointments: {},
            editMode: true,
            nations: [],
            patients: [],
            patient: {},
            request: {},
            services: [],
            staffs: [],
            user: {},
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshResponse', response => {
            this.refreshDomiliciaries(response);
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
        getDomiliciaries(page=1){
            axios.get('/api/emr/domiciliary/requests/pending?page='+page)
            .then(response=>{
                this.refreshDomiliciaries(response); 
            });
        },
        getInitials() {
            axios.get('/api/emr/domiciliary/requests/pending')
            .then(response => {this.refreshDomiliciaries(response)})
            .catch(() => {
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Your requests did not loaded successfully',})
            });
        },
        refreshDomiliciaries(response) {
            this.nations = response.data.nations;
            this.domiciliaries = response.data.domiciliaries;
            this.patients = response.data.patients;
            this.staffs = response.data.employees;
        }
    },
    props: {}
}
</script>