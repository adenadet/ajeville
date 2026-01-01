<template>
<section class="container-fluid">
    <div class="card">
        <div class="card-body">
    <form>
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
                    <div class="form-control">{{ patient | patientName }}</div>
                    <has-error :form="VisitForm" field="patient_id"></has-error> 
                </div>
            </div>  
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Visit Type</label>
                    <select class="form-control" name="visit_type_id" id="visit_type_id" v-model="VisitForm.visit_type_id">
                        <option value="">--Select Visit Type--</option>
                        <option v-for="visit_type in visit_types" :key="visit_type.id" :value="visit_type.id">{{ visit_type.
                        name }}</option>
                    </select>
                    <has-error :form="VisitForm" field="visit_type_id"></has-error> 
                </div>
            </div>  
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Visit Date</label>
                    <input class="form-control" type="date" name="start_date" id="start_date" v-model="VisitForm.start_date" />
                    <has-error :form="VisitForm" field="start_date"></has-error> 
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Branch</label>
                    <select class="form-control" name="branch_id" id="branch_id" v-model="VisitForm.branch_id">
                        <option value="">--Select Branch--</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.
                        name }}</option>
                    </select>
                    <has-error :form="VisitForm" field="branch_id"></has-error> 
                </div>
            </div> 
        </div>
        <button @click.prevent="editMode ? updateVisit() : createVisit()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
    </div>
    </div>
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
                id: '',
            }),
            patients: [],
            visit_types: {},
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('VisitResponse', request => {
            if (request != null) {
                this.VisitForm.patient_id = request.patient != null ? request.patient.id : '';
                this.patient = request.patient != null ? request.patient.id : '';
                this.VisitForm.start_date = request.id;
                this.VisitForm.assessments = [];
                for (let i = 0; i < request.assessments.length; i++) {
                    this.VisitForm.assessments.push(request.assessments[i].id);
                }  
            }
            else { this.VisitForm.reset(); }
        });
    },
    methods: {
        codeAndNameAndDesc (item) {
            return `${item.unique_id} | ${item.last_name}, ${item.first_name} ${item.middle_name}`;
        },
        createVisit() {
            this.$Progress.start();
            this.VisitForm.post('/api/emr/hims/visits')
            .then(response => {
                this.$Progress.finish();
                if (response.data.status == 'Completed'){
                    Fire.$emit('visitResponse', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'A Visit:'+ response.data.visit.unique_id +' has been created',
                        showConfirmButton: false,
                        timer: 5000,
                    });

                    if(this.VisitForm.visit_type_id == 1){
                        this.$router.push('/hims/visits/consultation/create?v_id='+response.data.visit.id);
                    }
                    if(this.VisitForm.visit_type_id == 2){
                        this.$router.push('/hims/visits/admission/create?v_id='+response.data.visit.id);
                    }
                    
                }
                else if (response.data.status == 'Error'){
                    if (response.data.message == 'Previous Visit not closed'){
                        Swal.fire({
                            icon: 'error',
                            title: 'Patient has '+response.data.count_visit+' outstanding visit(s)',
                            text: 'Kindly close the visit before opening a new visit',
                            footer: 'Visit Active Visits to close.'
                        });
                    }
                }
                
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
            axios.get('/api/emr/hims/visits/initials').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        refresh(response){
            this.branches = response.data.branches;
            this.visit_types = response.data.visit_types;
            this.patients = response.data.patients;
        },
    },
    props: {
        patient: Object,
        editMode: Boolean,
    }
}
</script>