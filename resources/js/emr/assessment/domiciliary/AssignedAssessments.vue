<template>
<section class="content-header">
    <div class="container-fluid">
        <!--
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
                        <DomFormAssignAssessment :assessment="request" :staffs="staffs" :request="request" :patient="request.patient"/>
                    </div>
                </div>
            </div>
        </div>-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Assigned Assessments</h3>
                        <!--<div class="card-tools">
                            <button class="btn btn-sm btn-success" @click="addRequest"><i class="fa fa-calendar-plus"></i> Create Booking</button>
                        </div>-->
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                    <th>Accessment(s)</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="assessments.data == null || assessments == null">
                                <tr><td colspan="6" class="text-center">You do not have any domiliciary assessment yet</td></tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="assessment in assessments.data" :key="assessment.id">
                                    <td>
                                        <div class="user-block">
                                            <img class="img-circle" :src="assessment.patient.image != null ? '/img/patients/'+assessment.patient.image: '/img/profile/default.png'">
                                            <span class="username">{{assessment.patient | patientName}}</span>
                                            <span class="description">Reg. {{assessment.patient.created_at | excelDate}} | Aged: {{assessment.patient.dob | age }} years</span>
                                        </div>
                                    </td>
                                    <td>{{assessment.assigned_date | excelDate}}</td>
                                    <td>{{assessment.assigned | FullName}}</td>
                                    <td>{{assessment.assessments != null ? assessment.accessments.length : 0 }}</td>
                                    <td>{{assessment.status == 0 ? 'Pending' : (assessment.status == 1 ? 'Submitted' : (assessment.status == 2 ? 'Approved' : 'Unconfirmed'))  }}</td>
                                    <td>
                                        <div class="btn btn-group">
                                            <router-link :to="'/domiciliary/assessments/assigned/'+assessment.id" class="btn btn-default btn-sm" title="View Request"><i class="fa fa-eye"></i></router-link>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="assessments" @pagination-change-page="getDomiciliaries">
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
        assessments: {},
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
        $('#assignModal').modal('hide');
    });
    Fire.$on('searchInstance', ()=>{
        let query = this.$parent.search;
        axios.get('api/emr/domiciliary/search?q='+query)
        .then((response ) => {this.applicants = response.data.applicants;})
        .catch(()=>{});
    });
},
methods: {
    confirmRequest(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm it!'
            })
        .then((result) => {
            //Send Confirm request
            if(result.value){
                this.form.put('/api/emr/domiciliary/requests/confirm/'+id)
                .then(response=>{
                    Swal.fire('Confirmed!', 'The Domiciliary Request has been confirmed.', 'success');
                    this.refreshDomiciliaries(response);   
                })
                .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                });
            }
        });
    },
    deleteRequest(id){
        Swal.fire({
            title: 'Are you sure, you want to delete this?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm it!'
            })
        .then((result) => {
            //Send Confirm request
            if(result.value){
                this.form.delete('/api/emr/domiciliary/requests/'+id)
                .then(response=>{
                    Swal.fire('Confirmed!', 'The Domiciliary Request has been deleted.', 'success');
                    this.refreshDomiciliaries(response);   
                })
                .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                });
            }
        });
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
        axios.get('/api/emr/assessments/assess/dom_assigned')
        .then(response => {this.refreshAssessments(response)})
        .catch(() => {
            this.$Progress.fail();
            toast.fire({icon: 'error', title: 'Your requests did not loaded successfully',})
        });
    },
    refreshAssessments(response) {
        this.assessments = response.data.assessments;
    }
},
props: {}
}
</script>