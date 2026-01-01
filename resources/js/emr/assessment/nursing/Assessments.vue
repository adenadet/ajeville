<template>
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Assigned Assessment</h3>
                        <!--div class="card-tools">
                            <button class="btn btn-sm btn-primary" @click="addType"><i class="fa fa-plus"></i> Create </button>
                        </div-->
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                    <th>Assessment Group</th>
                                    <th>Accessment(s)</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="assessments.data == null || assessments == null">
                                <tr><td colspan="7" class="text-center">You do not have any assessment yet</td></tr>
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
                                    <td>{{assessment.assessment_group != null ? assessment.assessment_group.name : 'Custom'}}</td>
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
import AssessmentTypeDetail from '../assessment/TypeDetail.vue';
import AssessmentFormType from '../assessment/forms/AssessmentType.vue';
export default {
    components:{
        AssessmentTypeDetail, AssessmentFormType
    },
    data() {
        return {
            assessment_type: {},
            editMode: true,
            items: [],
            type: {},
            types: {},
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshResponse', response => {
            this.refreshAssessmentTypes(response);
            this.closeModal();
        });
        Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('api/emr/domiciliary/search?q='+query)
            .then((response ) => {this.applicants = response.data.applicants;})
            .catch(()=>{});
        });
    },
    methods: {
        addType(request){
            this.$Progress.start();
            this.assessment_type = request;
            this.editMode = false;
            Fire.$emit('TypeDataFill', {});
            $('#formModal').modal('show');
            this.$Progress.finish();
        },
        updateType(request){
            this.$Progress.start();
            this.assessment_type = request;
            Fire.$emit('TypeDataFill', request);
            $('#formModal').modal('show');
            this.$Progress.finish();
        },
        viewType(request){
            this.$Progress.start();
            this.assessment_type = request;
            Fire.$emit('assessRequestDataFill', this.request);
            $('#viewModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#formModal').modal('hide');
            $('#viewModal').modal('hide');
            $('#requestModal').modal('hide');
        },
        deleteType(id){
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
        editType(request){
            this.$Progress.start();
            this.editMode = true;
            this.accessment_type = request;
            this.patient = request.patient;
            Fire.$emit('requestDataFill', request);
            $('#requestModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            axios.get('/api/emr/assessments/types?page='+page)
            .then(response=>{
                this.refreshAssessmentTypes(response); 
            });
        },
        refreshAssessmentTypes(response) {
            this.items = response.data.items;
            this.types = response.data.types;
        }
    },
    props: {}
}
</script>