<template>
<section class="container-fluid">
    <form class="">
        <alert-error :form="VisitForm"></alert-error> 
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group" v-if="patient == null">
                    <label>Patient Name</label>
                    <model-list-select class="form-control" :list="patients" v-model="VisitForm.patient_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Search for patient" />
                    <has-error :form="VisitForm" field="patient_id"></has-error> 
                </div>
                <div class="form-group" v-else>
                    <label>Patient Name</label>
                    <input type="hidden" name="patient_id" id="patient_id" v-model="VisitForm.patient_id" />
                    <div class="form-control">{{ patientName(patient)  }}</div>
                    <has-error :form="VisitForm" field="patient_id"></has-error> 
                </div>
            </div>  
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Visit Type</label>
                    <select class="form-control" name="visit_type_id" id="visit_type_id" v-model="VisitForm.visit_type_id" @change="updatePatientInsurances()">
                        <option value="">--Select Visit Type--</option>
                        <option v-for="visit_type in visit_types" :key="visit_type.id" :value="visit_type.id">{{ visit_type.
                        name }}</option>
                    </select>
                    <has-error :form="VisitForm" field="visit_type_id"></has-error> 
                </div>
            </div>  
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Visit Date</label>
                    <input class="form-control" type="date" name="start_date" id="start_date" v-model="VisitForm.start_date" />
                    <has-error :form="VisitForm" field="start_date"></has-error> 
                </div>
            </div> 
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Insurance</label>
                    <select class="form-control" name="branch_id" id="branch_id" v-model="VisitForm.plan_id">
                        <option value="">--Select Branch--</option>
                        <option v-for="insurance in patient?.insurances" :key="insurance.id" :value="insurance.plan.id">{{ insurance.plan.
                        name }}</option>
                        <option value=0>Cash - No Insurance </option>
                    </select>
                    <has-error :form="VisitForm" field="care_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Branch</label>
                    <select class="form-control" name="branch_id" id="branch_id" v-model="VisitForm.branch_id">
                        <option value="">--Select Branch--</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                    </select>
                    <has-error :form="VisitForm" field="branch_id"></has-error> 
                </div>
            </div> 
        </div>
        <button @click.prevent="editMode ? updateVisit() : createVisit()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {
        ModelListSelect
    },
    data() {
        return {
            branches: [],
            VisitForm: new Form({
                patient_id: '',
                visit_type_id: '',
                start_date: '',
                end_date: '',
                branch_id: '',
                plan_id: '',
                id: '',
            }),
            patients: [],
            patient_insurances: [],
            visit_types: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        codeAndNameAndDesc (item) {
            return `${item.unique_id} | ${item.user.last_name}, ${item.user.first_name} ${item.user.middle_name != null ? item.user.middle_name : '' }`;
        },
        createVisit() {
            this.loading = true;
            this.VisitForm.post('/api/emr/hims/visits')
            .then(response => {
                
                if (response.data.status == 'Completed'){
                    this.$emit('reloadVisitForm');
                    this.$store.dispatch('setPatientCookie', response.data.patient);
                    this.$store.dispatch('setVisitCookie', response.data.visit);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'A Visit:'+ response.data.visit.unique_id +' has been created',
                        showConfirmButton: false,
                        timer: 5000,
                    });
                }
                else if (response.data.status == 'Error'){
                    if (response.data.message == 'Previous Visit not closed'){
                        this.$swal.fire({
                            icon: 'error',
                            title: 'Patient has '+response.data.count_visit+' outstanding visit(s)',
                            text: 'Kindly close the visit before opening a new visit',
                            footer: 'Visit Active Visits to close.'
                        });
                    }
                }
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/visits/'+this.$route.params.id+'/initials').then(response =>{
                this.refresh(response);
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            })
            .finally(() => {this.loading = true;});
        },
        refresh(response){
            this.branches = response.data.branches;
            this.visit_types = response.data.visit_types;
            this.patients = response.data.patients;
        },
        updatePatientInsurances(){
            var index = this.patients.findIndex(object => {
                return object.id ===  this.VisitForm.patient_id;
            });
            this.patient_insurances = this.patients[index].insurances;
        }
    },
    props: {
        patient: Object,
        editMode: Boolean,
    },
    watch:{
        patient(){
            this.VisitForm.patient_id = this.patient.id
        }
    }
}
</script>