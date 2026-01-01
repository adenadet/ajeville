<template>
<section class="content-header">
    <div class="container-fluid">
        <div class="modal fade" id="viewModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-html="'View Groupped Assessment Type: '+assessment_type.name"></h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <AssessmentTypeDetail :assessment_type="assessment_type" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="formModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-html="editMode ? 'Edit Grouped Assessments: '+assessment_type.name: 'Create Domiliciary Request'"></h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <AssessmentFormType :assessment_type="assessment_type" :editMode="editMode" :items="items" />
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div class="modal fade" id="formModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Assign Domiliciary Request</h4>
                        <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <AssessmentTypeDetail :assessment_type="assessment_type" />
                    </div>
                </div>
            </div>
        </div>-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Assessment Types</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-primary" @click="addType"><i class="fa fa-plus"></i> Create </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Assessments</th>
                                    <th>Created By</th>
                                    <th>Created</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="types.data == null || types == null">
                                <tr><td colspan="6" class="text-center">You do not have any assessment types yet</td></tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(assessment_type, index) in types.data" :key="assessment_type.id">
                                    <td>{{ index | addOne  }}</td>
                                    <td>{{ assessment_type.name }}</td>
                                    <td>{{ assessment_type.assessments != null ? assessment_type.assessments.length : 0 }}</td>
                                    <td>{{ assessment_type.creator | FullName}}</td>
                                    <td>{{assessment_type.created_at | excelDate}}</td>
                                    <td>
                                        <div class="btn btn-group">
                                            <button class="btn btn-default btn-sm" @click="viewType(assessment_type)" title="View Assessment"><i class="fa fa-eye"></i></button>
                                            <button class="btn btn-primary btn-sm" @click="updateType(assessment_type)" title="Modify Assessment"><i class="fa fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" @click="deleteType(assessment_type)" title="Delete Assessment"><i class="fa fa-trash"></i></button>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="types" @pagination-change-page="getInitials">
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