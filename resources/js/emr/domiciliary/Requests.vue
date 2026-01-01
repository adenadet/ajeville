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
                                    <th>Contact</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                    <th>Payment Type</th>
                                    <th>Assessment</th>
                                    <th>Request</th>
                                    <th>Tasks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="domiciliaries.data == null || domiciliaries == null">
                                <tr><td colspan="9" class="text-center">You have not made any domiciliary services yet</td></tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="domiciliary in domiciliaries.data" :key="domiciliary.id">
                                    <td>
                                        <div class="user-block">
                                            <img class="img-circle" :src="domiciliary.patient.image != null ? '/img/patients/'+domiciliary.patient.image: '/img/profile/default.png'">
                                            <span class="username">{{domiciliary.patient | patientName}}</span>
                                            <span class="description">Reg. {{domiciliary.patient.created_at | excelDate}} | Aged: {{domiciliary.patient.dob | age }} years</span>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fa fa-phone"></i>{{ domiciliary.patient.phone }} <br />
                                        <i class="fa fa-envelope"></i>{{ domiciliary.patient.email }}
                                    </td>
                                    <td>{{domiciliary.start_date | excelDate}}</td>
                                    <td>{{ domiciliary.status }}</td>
                                    <td>{{domiciliary.payment_type}}</td>
                                    <td>{{domiciliary.assessment != null ? 'Completed' : (domiciliary.assessor != null ? 'Ongoing': 'Not Assigned')}}<br />
                                        <small>{{domiciliary.assessor!= null && domiciliary.assessor.user != null ? domiciliary.assessor.unique_id+' | '+domiciliary.assessor.user.first_name+' '+domiciliary.assessor.user.last_name : '' }}</small>
                                    </td>
                                    <td><span>{{domiciliary.hca_daily != 0 ? domiciliary.hca_daily+' HCA' : ''}}</span>
                                        <span>{{domiciliary.rn_daily != 0 ? domiciliary.rn_daily+' RN' : ''}}</span>
                                        <span>{{domiciliary.bsc_daily != 0 ? domiciliary.bsc_daily+' BSC' : ''}}</span>
                                    </td>
                                    <td>{{domiciliary.tasks != null ? domiciliary.tasks.length() : 'Awaiting'}}</td>
                                    <td class="navbar-nav">
                                        <li class="nav-item dropdown">
                                            <a class="nav-link" data-toggle="dropdown" href="#">
                                                <i class="fa fa-ellipsis-v text-default"></i>
                                            </a>
                                            <div class="dropdown-menu border-0 shadow">
                                                <router-link :to="'/domiciliary/request/'+domiciliary.id" class="btn btn-default btn-sm dropdown-item" title="View Request"><i class="fa fa-file mr-1"></i> View File</router-link>
                                                <router-link :to="'/domiciliary/request/'+domiciliary.id" class="btn btn-default btn-sm dropdown-item" title="View Request"><i class="fa fa-envelope mr-1"></i>Send Message</router-link>
                                                <button class="btn btn-sm dropdown-item" @click="assignRequest(domiciliary)" title="Assign for Assessment"><i class="fa fa-user"></i> Assign Staff</button>
                                                <button class="btn btn-sm dropdown-item" @click="editRequest(domiciliary)" title="Edit Request"><i class="fa fa-edit mr-1 text-primary"></i>Edit Reques</button>
                                                <button class="btn btn-sm dropdown-item" @click="confirmRequest(domiciliary.id)" title="Confirm Request"><i class="fa fa-check mr-1 text-warning"></i> Confirm Request</button>
                                                <button class="btn btn-sm dropdown-item" @click="deleteRequest(domiciliary.id)" title="Delete Request"><i class="mr-1 fa fa-trash text-danger nr-1"></i> Delete Request</button>
                                            </div>
                                        </li>
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
        getDomiciliaries(page=1){
            axios.get('/api/emr/domiciliary/requests?page='+page)
            .then(response=>{
                this.refreshDomiliciaries(response); 
            });
        },
        getInitials() {
            axios.get('/api/emr/domiciliary/requests')
            .then(response => {this.refreshDomiciliaries(response)})
            .catch(() => {
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Your requests did not loaded successfully',})
            });
        },
        refreshDomiciliaries(response) {
            this.nations = response.data.nations;
            this.domiciliaries = response.data.domiciliaries;
            this.patients = response.data.patients;
            this.staffs = response.data.employees;
        }
    },
    props: {}
}
</script>