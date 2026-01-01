<template>
<section class="content-header">
    <div class="container-fluid">
        <div class="modal fade" id="applicantModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-html="editMode ? 'Edit Patient' : 'Create Patient'"></h4>
                        <button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <HimsFormPatient :editMode="editMode" :nations="nations" :applicant="applicant" /> 
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Service Users</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-success" @click="addApplicant"><i class="fa fa-user-plus"></i> Create Servce User</button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Service User</th>
                                    <th>Date of Birth</th>
                                    <th>Sex</th>
                                    <th>Nationality</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="patients.data == null || patients == null">
                                <tr><td colspan="6" class="text-center">You have not made any patients yet</td></tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="patient in patients.data" :key="patient.id">
                                    <td>
                                        <div class="user-block">
                                            <img class="img-circle" :src="patient.image != null ? '/img/patients/'+patient.image: '/img/profile/default.png'">
                                            <span class="username">{{patient | patientName}}</span>
                                            <span class="description">Registered {{patient.created_at | excelDate}}</span>
                                        </div>
                                    </td>
                                    <td>{{patient.dob }} </td>
                                    <td>{{patient.sex}}</td>
                                    <td>{{patient.nationality ? patient.nationality.name : 'Not Defined'}}</td>
                                    <td>
                                        <div class="btn btn-group">
                                            <a href :href="'/hims/patients/'+patient.id"><button class="btn btn-success btn-sm"><i class="fa fa-eye"></i></button></a>
                                            <button class="btn btn-primary btn-sm" @click="editApplicant(patient)" title="Edit Applicant"><i class="fa fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="patients" @pagination-change-page="getApplicant">
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
        applicant: {},
        applicants: {},
        appointments: {},
        editMode: true,
        nations: [],
        patients: {},
        services: [],
        user: {},
    }
},
mounted() {
    this.getInitials();
    Fire.$on('refresh', response => {
        this.refreshAppointments(response);
        this.closeModal();
    });
    Fire.$on('searchInstance', ()=>{
        let query = this.$parent.search;
        axios.get('/api/hims/patients/search?q='+query)
        .then((response ) => {this.applicants = response.data.applicants;})
        .catch(()=>{});
    });
},
methods: {
    addApplicant(){
        this.$Progress.start();
        this.editMode = false;
        //this.applicant = {};
        Fire.$emit('ApplicantDataFill', {});
        $('#applicantModal').modal('show');
        this.$Progress.finish();
    },
    closeModal(){
        $('#applicantModal').modal('hide');
    },
    editApplicant(applicant){
        this.$Progress.start();
        this.editMode = true;
        this.applicant = applicant;
        Fire.$emit('ApplicantDataFill', applicant);
        $('#applicantModal').modal('show');
        this.$Progress.finish();
    },
    getApplicant(page=1){
        axios.get('/api/emr/hims/patients?page='+page)
        .then(response=>{
            this.refreshPatients(response); 
        });
    },
    getInitials() {
        axios.get('/api/emr/hims/patients')
        .then(response => {this.refreshPatients(response)})
        .catch(() => {
            this.$Progress.fail();
            toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
        });
    },
    refreshPatients(response) {
        this.nations = response.data.nations;
        this.patients = response.data.patients;
    }
},
props: {}
}
</script>