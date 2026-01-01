<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">                    
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">New Consultation</h3>
                    </div>
                    <form class="card-body">
                        <alert-error :form="ConsultationForm"></alert-error>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Specialty</label>
                                    <select class="form-control" name="specialty_id" id="specialty_id" v-model="ConsultationForm.specialty_id" @change="sortStaff()">
                                        <option value="">--Specialty--</option>
                                        <option v-for="(specialty, index,) in specialties" :key="specialty.id" :value="index">{{specialty.name }}</option>
                                    </select>
                                    <has-error :form="ConsultationForm" field="specialty_id"></has-error>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>To See</label>
                                    <select class="form-control" name="consultant_id" id="whom_to_see" v-model="ConsultationForm.whom_to_see">
                                        <option value="">--Whom To See--</option>
                                        <option value="group">Medical Group</option>
                                        <option value="consultant">Consultant</option>
                                    </select>
                                    <has-error :form="ConsultationForm" field="consultant_id"></has-error>
                                </div>
                            </div>
                            <div class="col-sm-6" v-if="ConsultationForm.whom_to_see != 'group'">
                                <div class="form-group">
                                    <label>Consultant</label>
                                    <select class="form-control" name="consultant_id" id="consultant_id" v-model="ConsultationForm.consultant_id">
                                        <option value="">--Select Consultant--</option>
                                        <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">{{doctor.user | FullName }}</option>
                                    </select>
                                    <has-error :form="ConsultationForm" field="consultant_id"></has-error>
                                </div>
                            </div>
                            <div class="col-sm-6" v-if="ConsultationForm.whom_to_see == 'group'">
                                <div class="form-group">
                                    <label>Group</label>
                                    <select class="form-control" name="consultant_id" id="consultant_id" v-model="ConsultationForm.consultant_id">
                                        <option value="">--Select Group--</option>
                                        <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">{{specialty.name }}</option>
                                    </select>
                                    <has-error :form="ConsultationForm" field="consultant_id"></has-error>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Consultant Fee Type</label>
                                    <select class="form-control" name="consultation_type_id" id="consultation_type_id" v-model="ConsultationForm.consultation_type_id">
                                        <option value="">--Select Consultation Fee--</option>
                                        <option v-for="service in services" :key="service.id" :value="service.id">{{service.name }}</option>
                                    </select>
                                    <has-error :form="ConsultationForm" field="consultation_type_id"></has-error>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="requires_vitals" name="requires_vitals" v-model="ConsultationForm.requires_vitals">
                                        <label class="form-check-label">Requires Vitals</label>
                                    </div>
                                    <has-error :form="ConsultationForm" field="consultation_type_id"></has-error>
                                </div>
                            </div>
                        </div>
                        <button @click.prevent="editMode ? updateConsultation() : createConsultation()" type="submit" name="submit"
                            class="submit btn btn-primary">Submit</button>
                    </form>
                </div>
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
    data() {
        return {
            ConsultationForm: new Form({
                branch_id: '',
                consultant_id: '',
                consultation_type_id: '',
                id: '',
                group_id: '',
                patient_id: '',
                visit_id: '',
                whom_to_see: '',
                specialty_id: '',
                requires_vitals: true,
            }),
            doctors: [],
            groups: [],
            services: [],
            specialties: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createConsultation(){
            this.$Progress.start();
            this.ConsultationForm.visit_id = this.visit.id;
            this.ConsultationForm.patient_id = this.visit.patient_id;
            this.ConsultationForm.branch_id = this.visit.branch_id;
            this.ConsultationForm.post('/api/emr/hims/consultations')
            .then(response =>{
                Swal.fire({
                    icon: 'success',
                    title: 'Done',
                    text: 'A visit has been created successfully',
                });
                this.$Progress.fail();
                this.$router.push('/hims/visits');
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            })
        },
        getAllInitials() {
            this.$Progress.start();
            axios.get('/api/emr/hims/consultations/begins').then(response => {
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        sortStaff(){},
        refresh(response) {
            this.doctors = response.data.doctors;
            this.groups = response.data.groups;
            this.services = response.data.services;
            this.specialties = response.data.specialties;
            this.filtered_doctors = response.data.doctors;
            this.filtered_groups = response.data.groups;
        },
    },
    props: {
        editMode: Boolean,
    }
}
</script>