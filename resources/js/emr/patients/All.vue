<template>
<section>
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-navy">
                        <h3 class="card-title">All Patients</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 350px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    <select class="form-control form-control-sm ml-1" id="status" name="status" v-model="status">
                                        <option value="">--Sort By Type--</option>
                                        <option value="in_visit">In Visit</option>
                                        <option value="deceased">Deceased</option>
                                    </select>
                                    <!--button class="nav-link btn btn-sm btn-primary" type="button" @click="addPatient">
                                        <i class="fa fa-plus"></i>
                                    </button-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 500px;">
                        <EMRPatientDetailPatientList :patients="patients.data" source="front_office" @reloadPatientList="getInitials"/>
                    </div>
                    <div class="card-footer"><pagination v-model="current_page" @paginate="getInitials" :per-page="patients.per_page != null ? patients.per_page : 52" :records="patients.total != null ? patients.total : 550" ></pagination></div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRPatientDetailPatientList from '@/emr/patients/details/PatientList.vue';
import EMRPatientFormRegistration from '@/emr/patients/forms/Registration.vue';
import EMRPatientFormRegister from '@/emr/patients/forms/Register.vue';
export default {
    components:{
        EMRPatientDetailPatientList, EMRPatientFormRegister, EMRPatientFormRegistration
    },
    data() {
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            patient: {},
            patients: {data: [], total: 0,},
            query: '',
            services: [],
            status: '',
            user: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addPatient(){
            this.loading = true;
            this.editMode = false;
            this.patient = {};
            $('#patientModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#patientModal').modal('hide');
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/emr/hims/patients?query='+this.query+'&status='+this.status)
            .then(response => {this.loading = false; this.refreshPatients(response)})
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Patients were not loaded successfully',})
            });
        },
        refreshPatients(response) {
            this.patients = response.data.patients;
        }
    },
    props: {}
}
</script>