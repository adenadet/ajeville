<template>
    <section class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title" v-if="source == 'my_past_consultations'">My Previous Consultations</h4>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="search_query">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-striped table-hover text-nowrap">
                <thead>
                    <tr>
                        <th> Date</th>
                        <th> Patient</th>
                        <th> Type</th>
                        <th> Clinic/Specialty</th>
                        <th> Whom To See </th>
                        <th v-if="source == 'my_previous_consultations'"> Consultant Seen </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="consultations.data != null && consultations.data.length > 0">
                    <tr v-for="(consultation, index) in consultations.data">
                        <td> {{ consultation.transaction.date }}</td>
                        <td> {{ consultation.patient | patientName }} </td>
                        <td> {{ consultation.consultation_type != null ? consultation.consultation_type.name : 'Consultation' }}</td>
                        <td> {{ consultation.specialty != null ? consultation.specialty.name : 'No Specialty Consultation' }}</td>
                        <td> {{ consultation.whom_to_see != 'group' ? (consultation.consultant != null ? (consultation.consultant | staffName) : 'No Chosen Consultant')  : (consultation.group != null ? (consultation.group.name | staffName) : 'No Group Chosen') }}</td>
                        <td v-if="source == 'my_previous_consultations'"> 
                            <span v-if="consultation.consultant_seen != null">{{ consultation.consultant_seen | fullName  }}</span>
                            <span v-else>No Consultant Seen</span>
                        </td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button v-if="consultation.status == 2" class="btn btn-block dropdown-item" @click="callPatient(consultation)"><i class="fas fa-phone-volume mr-2"></i> Call Patient</button>
                                <router-link v-if="consultation.status == 2" :to="'/consultations/start/'+consultation.id" class="btn btn-block dropdown-item"><i class="fas fa-file mr-2"></i> Start Consultation</router-link>
                                <router-link v-if="consultation.status == 4" :to="'/consultations/detailed/'+consultation.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2"></i> View Consultation</router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td v-if="source == 'my_previous_consultations'" colspan="7">No Consultation meets your query</td>
                        <td v-else colspan="6">No Consultation meets your query</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <pagination :data="consultations" @pagination-change-page="getInitials">
                <span slot="prev-nav">&lt; Previous </span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            consultation: {},
            consultations: {},
            editMode: true,
            nations: [],
            areas: [],
            search_query: '',
            states: [],
            user: {}
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshAppointment', response => {
            this.refreshAppointments(response);
        });
        Fire.$on('refreshPayment', response => {
            this.refreshAppointments(response);
            $('#paymentModal').modal('hide');
        });
    },
    methods: {
        callPatient(consultation){
            Swal.fire({
                title: 'Are you sure?',
                text: "A notification would be sent to the patient",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, call Patient!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.get('/api/emr/consultations/consultants/call_patient/'+id)
                    .then(response=>{
                    Swal.fire('Notified!', 'Patient has been notified.', 'success');
                    //Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1) {
            var new_route;
            if (this.source == 'my_past_consultations'){
                new_route = 'my_past_consultations';
            }
            axios.get('/api/emr/consultations/consultants/'+new_route+'?page='+page+'&query='+this.search_query)
            .then(response => {
                this.refreshAppointments(response);        
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your Consultations did not loaded successfully',
                })
            });
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
            this.consultations = response.data.consultations;
            this.nations = response.data.nations;
            this.states =  response.data.states;
            this.areas = response.data.areas;
        }
    },
    props: {
        source: String,
    }
}
</script>