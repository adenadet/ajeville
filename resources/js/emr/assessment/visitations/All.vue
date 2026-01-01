<template>
<section class="container-fluid">
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="tab" role="tablist">
                <li class="pt-2 px-3"><h3 class="card-title">Visitations</h3></li>
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="pill" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Consultation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="messages-tab" data-toggle="pill" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Investigations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pharmacies-tab" data-toggle="pill" href="#pharmacies" role="tab" aria-controls="pharmacies" aria-selected="false">Pharmacy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Other Services</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="tabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Patient</th>
                                <th>Consultant</th>
                                <th>Status</th>
                                <th>Booked</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="visits != null && visits.data != null">
                            <tr v-for="visit in visits.data" :key="visit.id">
                                <td>{{ visit.start_date }}</td>
                                <td>{{ visit.visit_type.name }}</td>
                                <td>{{ visit.patient | patientName }}</td>
                                <td>{{ visit.consultant | FullName }}</td>
                                <td>{{ visit.status }}</td>
                                <td>{{ visit.created_at }}</td>
                                <td>
                                    <a class="nav-link" data-toggle="dropdown" href="#">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <span class="dropdown-header">15 Notifications</span>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">
                                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                                        <span class="float-right text-muted text-sm">3 mins</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">
                                        <i class="fas fa-users mr-2"></i> 8 friend requests
                                        <span class="float-right text-muted text-sm">12 hours</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">
                                        <i class="fas fa-file mr-2"></i> 3 new reports
                                        <span class="float-right text-muted text-sm">2 days</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr><td colspan="7">No Visit has been created yet. <router-link to="/hims/visits/create" class="btn btn-primary btn-xs">Create Visit</router-link></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Who to See</th>
                                <th>Status</th>
                                <th>Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="consultation in consultations.data">
                                <th>{{consultation.date}}</th>
                                <th>{{consultation.patient | patientFullName}}</th>
                                <th>{{consultation.consultant | FullName }}</th>
                                <th>{{ consultation.status }}</th>
                                <th>{{ consultation.created_at }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Investigation Type</th>
                                <th>Investigation Details</th>
                                <th>Status</th>
                                <th>Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="investigation in investigations.data">
                                <th>{{investigation.date}}</th>
                                <th>{{investigation.patient | patientFullName}}</th>
                                <th>{{investigation.consultant | FullName }}</th>
                                <th>{{ investigation.status }}</th>
                                <th>{{ investigation.created_at }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Investigation Type</th>
                                <th>Investigation Details</th>
                                <th>Status</th>
                                <th>Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="service in services.data">
                                <th>{{service.date}}</th>
                                <th>{{service.patient | patientFullName}}</th>
                                <th>{{service.consultant | FullName }}</th>
                                <th>{{ service.status }}</th>
                                <th>{{ service.created_at }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="pharmacies" role="tabpanel" aria-labelledby="pharmacies-tab">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Patient Type</th>
                                <th>Status</th>
                                <th>Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pharmacy in pharmacies.data">
                                <th>{{pharmacy.date}}</th>
                                <th>{{pharmacy.patient | patientFullName}}</th>
                                <th>{{pharmacy.patient_type }}</th>
                                <th>{{ pharmacy.status }}</th>
                                <th>{{ pharmacy.created_at }}</th>
                            </tr>
                        </tbody>
                    </table>
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
            visits: {},
            consultations: {},
            investigations: {},
            services: {},
            pharmacies: {},
            VisitForm: new Form({
                patient_id: '',
                visit_type_id: '',
                start_date: '',
                end_date: '',
                id: '',
            }),
            patient: {},
            patients: [],
            visit_types: {},
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('AssessmentTypeDataFill', request => {
            if (request != null) {
                this.VisitForm.name = request.name;
                this.VisitForm.description = request.description;    
                this.VisitForm.id = request.id;
                this.VisitForm.assessments = [];
                for (let i = 0; i < request.assessments.length; i++) {
                    this.VisitForm.assessments.push(request.assessments[i].id);
                }  
            }
            else { this.VisitForm.reset(); }
        });
    },
    methods: {
        createVisit() {
            this.$Progress.start();
            this.VisitForm.put('/api/emr/visits')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'A Visit has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/hims/visits').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.visits = response.data.visits;
        }
        
    },
}
</script>