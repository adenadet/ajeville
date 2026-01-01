<template>
    <section>
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Consultation Queue</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-primary float-sm-right" @click="addVisitation()">Add New Visitation <i class="fa fa-user-add"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Visit Type</th>
                                <th>Doctor To See</th>
                                <th>Arrived At</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="leaves.data == null || leaves.data.length == 0">
                            <tr><td colspan="6" class="text-center">You do not have any visit request</td></tr>
                        </tbody>
                        <tbody v-else-if="leaves.data != null">
                            <tr v-for="(leave, index) in leaves.data" :key="leave.id">
                                <td>{{ index | addOne }}</td>
                                <td>{{ visit.patient | FullName }}</td>
                                <td>{{ visit.type }}</td>
                                <td>{{ visit.consultant | FullNameStaff }}</td>
                                <td>{{ visit.arrived_at | excelDate }}</td>
                                <td>{{ visit.status | capitalize}}</td>
                                <td><i class="fas fa-ellipsis-v"></i></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr><td colspan="6">You do not have any visit request</td></tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-6 d-flex align-items-stretch" v-for="visitation in visitations.data" :key="visitation.id">
                            <div class="card bg-light">
                                <div class="card-header text-muted border-bottom-0">&nbsp;</div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="lead"><b>{{visitation.user.first_name}} {{visitation.user.middle_name}} {{visitation.user.last_name}}</b></h2>
                                        </div>
                                        <div class="col-5 text-center">
                                            <img style="height: 100px;" :src="(visitation.user.image) ? '/img/profile/'+visitation.user.image : '/img/profile/default.png'" alt="" class="img-circle img-fluid">
                                        </div>
                                        <div class="col-12">
                                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> Email: {{visitation.official_email}} | {{visitation.user.email}}</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Roles: {{(visitation.user.roles != null && (typeof (visitation.user.roles) != 'undefined')) ? ', Patient' : 'Patient only' }}</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: {{visitation.user.phone}}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <!--<div class="text-right">
                                        <button class="btn btn-sm btn-success" @click="setUserRole(visitation.user)" title="Set Staff Role"><i class="fa fa-user-cog"></i></button>
                                        <button class="btn btn-sm btn-primary" @click="editUser(visitation)" title="Edit Staff"><i class="fa fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" @click="deleteUser(visitation.id)" title="Delete Staff"><i class="fa fa-trash"></i></button>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card-footer">
                        <pagination :data="visitations" @pagination-change-page="getInitials">
                            <span slot="prev-nav">&lt; Previous </span>
                            <span slot="next-nav">Next &gt;</span>
                        </pagination>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                Basic summary 
            </div>
        </div>
    </section>
</template>
<script>
export default {
data() {
    return {
        visitations: {},
        visitation: {},
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
    addVisitation(){
        this.$Progress.start();
        this.editMode = false;
        //this.applicant = {};
        Fire.$emit('ApplicantDataFill', {});
        $('#visitModal').modal('show');
        this.$Progress.finish();
    },
    closeModal(){
        $('#visitModal').modal('hide');
    },
    editApplicant(visitation){
        this.$Progress.start();
        this.editMode = true;
        this.visitation = visitation;
        Fire.$emit('VisitDataFill', visitation);
        $('#visitModal').modal('show');
        this.$Progress.finish();
    },
    getInitials(page=1){
        axios.get('/api/emr/hims/visitations?page='+page)
        .then(response=>{
            this.refreshPatients(response); 
        })
        .catch(() => {
            this.$Progress.fail();
            toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
        });
    },
    /*getInitials() {
        axios.get('/api/emr/hims/patients')
        .then(response => {this.refreshPatients(response)})
        .catch(() => {
            this.$Progress.fail();
            toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
        });
    },*/
    refreshPatients(response) {
        this.nations = response.data.nations;
        this.patients = response.data.patients;
    }
},
props: {}
}
</script>