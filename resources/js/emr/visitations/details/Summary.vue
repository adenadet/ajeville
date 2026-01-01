<template>
    <div class="modal fade" id="patientModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Patient Detail {{ source }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRPatientDetailFull :source.sync="source"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card overlay-wrapper">
        <div v-if="source != 'consultation'" class="card-header bg-dark"><h3 class="card-title">Visit Detail</h3></div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card-body" v-if="visit != null && visit.id != null">
            <div class="row">
                <div class="col-9">
                    <h2 class="lead"><b>{{ visit.patient | patientName }}</b></h2>
                    
                    <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: {{ patientAddress(visit.patient)}}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: {{ visit.patient.user.phone }}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-money-bill"></i></span> Balance: {{ currency(visit.patient.balance)}}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-stopwatch"></i></span> Started: {{ visit.start_timestamp }}</li>
                        <li class="small" v-if="visit.end_timestamp != null"><span class="fa-li"><i class="fas fa-lg fa-clock"></i></span> Duration: {{ visitDuration(visit)}}</li>
                        <li class="small" v-else><span class="fa-li"></span><p class="text-danger">Still Ongoing</p></li>
                    </ul>
                </div>
                <div class="col-3 text-center">
                    <img :src="visit.patient.user.image != null ? visit.patient.user.image :'/img/profile/default.png'" alt="user-avatar" class="img-circle img-fluid">
                </div>
                <div class="col-12">
                    <p class="text-muted text-sm">Type: <b>{{ visit.visit_type.name }}</b><br >Payment Method: 
                        <b v-if="patient.insurances == null || patient.insurances.length == 0">Cash</b>
                        <div v-if="patient.insurances != null && patient.insurances.length != 0" class="table-responsive p-0">
                            <table class="table table-hover text-nowrap table-striped">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>S/N</th>
                                        <th>Plan</th>
                                        <th>Provider</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(insurance, index) in patient.insurances" :class="insurance.status == 0 ? 'text-danger' : ''">
                                        <td>{{ addOne(index) }}</td>
                                        <td>{{ insurance.plan.name }}</td>
                                        <td>{{ insurance.plan.provider.name }}</td>
                                        <td>{{ insurance.expiry_date }}</td>
                                        <td>{{ insurance.status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="card-body" v-else-if="patient != null && patient.id != null">
            <div class="row">
                <div class="col-9">
                    <h2 class="lead"><b>{{ patientName(patient)}}</b></h2>
                    
                    <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: {{ patientAddress(patient)}}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: </li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-money-bill"></i></span> Balance: {{ currency(patient.balance)}}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-stopwatch"></i></span> Started: New</li>
                        <li class="small" v-if="visit.end_timestamp != null"><span class="fa-li"><i class="fas fa-lg fa-clock"></i></span> Duration: {{ visit | visitDuration }}</li>
                        <li class="small" v-else><span class="fa-li"></span><p class="text-danger">Still Ongoing</p></li>
                    </ul>
                </div>
                <div class="col-3 text-center">
                    <img :src="'/img/profile/default.png'" alt="user-avatar" class="img-circle img-fluid">
                </div>
                <div class="col-12">
                    <p class="text-muted text-sm">Type: <b></b><br >Payment Method: 
                        <b v-if="patient.insurances == null || patient.insurances.length == 0">Cash</b>
                        <div v-if="patient.insurances != null && patient.insurances.length != 0" class="table-responsive p-0">
                            <table class="table table-hover text-nowrap table-striped">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>S/N</th>
                                        <th>Plan</th>
                                        <th>Provider</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(insurance, index) in patient.insurances" :class="insurance.status == 0 ? 'text-danger' : ''">
                                        <td>{{ addOne(index) }}</td>
                                        <td>{{ insurance.plan.name }}</td>
                                        <td>{{ insurance.plan.provider.name }}</td>
                                        <td>{{ insurance.expiry_date }}</td>
                                        <td>{{ insurance.status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="card-body" v-else height="500">
            
            
        </div>
        <div class="card-footer">
            <div class="text-right">
                <button class="btn btn-sm btn-primary" @click=viewPatient(patient) v-if="(patient != null && patient.id != null) || (visit != null && visit.id != null)" :href="'/emr/hims/patients/'+((visit != null && visit.id != null) ? (visit.patient != null ? visit.patient.unique_id : patient.unique_id) : patient.unique_id )"><i class="fas fa-user"></i> View Patient Details</button>
                <button v-if="source != 'consulatiton'"class="btn btn-sm btn-danger" @click="endVisit(visit)"><i class="fas fa-power-off"></i></button>
                
            </div>
        </div>
    </div>
</template>
<script>
export default {
    components: {},
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        today(){
            return new Date();
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data(){
        return {
            loading: false,
        }
    },
    methods:{
        endVisit(visit){
            if (visit.transactions_sum_item_total > 0){
                this.$swal.fire({
                    icon: 'error',
                    title: 'Can not end visit',
                    text: 'Patient owes '+ visit.transactions_sum_item_total,
                    footer: 'Please try again later!'
                });
            }
            else{
                this.$swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, end visit!'
                    })
                .then((result) => {
                    //Send End Visit request
                    if(result.value){
                        this.$Progress.start();
                        this.form.put('/api/emr/hims/visits/end/'+visit.id)
                        .then(response=>{
                            this.$swal.fire('Deleted!', 'Visit has been ended.', 'success');
                            this.refresh(response);
                            this.$Progress.finish(); 
                        })
                        .catch(()=>{
                            this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                        });
                    }
                });
            }
        },
        refresh(response) {
            this.$store.dispatch('setPatientCookie', response.data.patient);
            this.$store.dispatch('setVisitCookie', response.data.visit);
        },
        viewPatient(){
            $('#patientModal').modal('show');
        }
    },  
    mounted(){
        
    },
    props:{
        source: String,
    },
    watch:{
        
    },
}
</script>