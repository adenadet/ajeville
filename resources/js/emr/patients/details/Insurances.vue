<template>
    <section>
        <div class="modal fade" id="insuranceModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Insurance' : 'Create Insurance'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body"><EMRPatientFormInsurance :editMode="editMode" :insurance.sync="insurance" :patient="patient" /></div>
                </div>
            </div>
        </div>
        <div class="card-header bg-dark">
            Insurances 
            <div class="card-tools">
                <button  class="btn btn-sm btn-success" @click="addInsurance">Add New</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Provider Name</th>
                        <th>Enrollee Number</th>
                        <th>Expiry Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="insurance in patient.insurances">
                        <td>{{ insurance.plan?.name }}</td>
                        <td>{{ insurance.plan?.provider?.name }}</td>
                        <td>{{ insurance.enrollee_id }}</td>
                        <td><span class="tag tag-success">{{ insurance.expiry_date }}</span></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary" @click="editInsurance(insurance)"><i class="fa fa-edit"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
<script>
import EMRPatientFormInsurance from '@/emr/patients/forms/Insurance.vue';
export default {
    components:{EMRPatientFormInsurance},
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data(){
        return  {
            editMode: false,
            insurance: {},
            insurances: [], 
            loading: false,
        }
    },
    methods:{
        addInsurance(){
            this.loading = true;
            this.editMode = false;
            this.insurance = {};
            $('#insuranceModal').modal('show');
            this.loading =false;
        },
        closeModal(){
            $('#allergyModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#insuranceModal').modal('hide');
        },
        editInsurance(insurance){
            this.loading = true;
            this.editMode = true;
            this.insurance = insurance;
            $('#insuranceModal').modal('show');
            this.loading =false;
        },
        getInitials(id){
            this.loading = true;
            axios.get('/api/emr/hims/patients/'+id+'/insurances').then(response =>{
                this.loading = false;
                this.reloadPatient(response);
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Profile not loaded successfully',});
            });
        },
        reloadPatient(response){
            this.insurances = response.data.insurances; 
        },
    },
    mounted() {},
}
</script>