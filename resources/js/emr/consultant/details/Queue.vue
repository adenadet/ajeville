<template>
    <section>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">{{firstUp(type)}} Queue</h3>
        </div>
        <div class="card-body p-0 table-responsive overlay-wrapper" style="height: 400px;">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <table class="table table-bordered table-striped table-hover table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th> Date</th>
                        <th> Patient</th>
                        <th> Type</th>
                        <th> Clinic/Specialty</th>
                        <th> Whom To See </th>
                        <th v-if="source == 'consultant'"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(consultation, index) in consultations.data" :key="index">
                        <td> {{ ExcelDate(consultation.transaction != null ? consultation.transaction.date : consultation.created_at)}}</td>
                        <td v-if="consultation.patient != null"> {{ patientName(consultation.patient)}} </td>
                        <td v-else>{{ consultation.patient_id }}</td>
                        <td> {{ consultation.consultation_type != null ? consultation.consultation_type.name : 'Consultation' }}</td>
                        <td> {{ consultation.specialty != null ? consultation.specialty.name : 'No Specialty Consultation' }}</td>
                        <td> </td>
                        <td v-if="source == 'consultant'">
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button class="btn btn-block dropdown-item" @click="callPatient(consultation)"><i class="fas fa-phone-volume mr-2"></i> Call Patient</button>
                                <router-link :to="'/emr/consultations/start/'+consultation.id" class="btn btn-block dropdown-item"><i class="fas fa-file mr-2"></i> Start Consultation</router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>                
        </div>
    </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            consultations: {},
            editMode: true,
            form: new Form({}),
            loading: false,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        callPatient(consultation){
            this.$swal.fire({
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
                    this.form.get('/api/emr/consultations/consultants/call_patient/'+consultation.id)
                    .then(response=>{
                        this.$swal.fire('Notified!', 'Patient has been notified.', 'success');
                        //Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page = 1) {
            this.loading = true;
            axios.get('/api/emr/consultations/consultants?type='+this.type+'&view='+this.view+'&page='+page)
            .then(response => {
                this.refreshDashboard(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshDashboard(response) {
            this.consultations = response.data.consultations;
        },
        startConsultation(consultation){
            this.$swal.fire({
                title: (consultation.transaction != null && consultation.transaction.status == 0) ? 'Debit the Customer?' : 'Are you sure?',
                text: (consultation.transaction != null && consultation.transaction.status == 0) ? "This patient would be debited for the transaction!": "Start the consultation",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: (consultation.transaction != null && consultation.transaction.status == 0) ? 'Yes, debit and start it!' : 'Yes, start it!',
            })
            .then((result) => {
                if (result.value){
                    this.form.get('/api/emr/consultations/consultants/start/'+consultation.id)
                    .then(response=>{
                        if (response.data.status == 'Unpaid'){
                            this.$swal.fire(response.data.status, response.data.message, 'warning');
                        }
                        else {
                            this.$router.push('/consultations/start/'+consultation.id);
                        }   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            })

        }
    },
    props: {
        source: String,
        view: String,
        type: String,
    }
}
</script>