<template>
    <section>
        <div class="modal fade" id="insuranceModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Insurance' : 'Create Insurance'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body"><PatientFormInsurance :editMode="editMode" :insurance="insurance" /></div>
                </div>
            </div>
        </div>
        <div class="card-header bg-dark">
            Insurances 
            <div class="card-tools">
                <button  class="btn btn-sm btn-success">Add New</button>
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
                      <td>{{ insurance.plan.name }}</td>
                      <td>{{ insurance.plan.provider.name }}</td>
                      <td>{{ insurance.enrollee_number }}</td>
                      <td><span class="tag tag-success">{{ insurance.expiry_date }}</span></td>
                      <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
<script>
export default {
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
    mounted() {
        /*Fire.$on('Reload', response =>{this.refreshProfile(response);});
        Fire.$on('pageReload', () => {
            this.getInitials();
            this.closeModal();
        });
        Fire.$on('getPatient', patient_id => {
            this.getInitials(patient_id);
        });*/
        //Fire.$on('patientReset', () => {this.getInitials(this.patient.id);});
    },
    methods:{
        addInsurance(){
            this.loading = true;
            this.editMode = false;
            this.insurance = {};
            //Fire.$emit('InsuranceDataFill', {});
            $('#insuranceModal').modal('show');
            this.loading =false;
        },
        closeModal(){
            $('#allergyModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#contactModal').modal('hide');
        },
        /*editAllergy(allergy){
            this.loading = true;
            this.editMode = true;
            let details = {'allergy': allergy, 'patient':this.user};
            Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.loading =false;
        },
        editContact(contact){
            this.loading = true;
            this.editMode = true;
            let details = {'patient': this.patient, 'contact': contact};
            Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.loading =false;
        },*/
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
}
</script>