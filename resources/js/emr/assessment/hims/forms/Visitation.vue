<template>
<section>
    <div class="row">
        
        <div class="col-md-5">
            Select Visitation Type
            1. Registered patient visit
            2. Non-registered patient visit
            3. Registered investigation visit
            4. Non-registered investigation visit
            5. Antenatal visit (registered patient's only)
            6. Pharmacy sale (not a visit)
            7. Registered patient Vaccination
            8. Non-registered patient vaccination
            9. Registered patient Physiotheraphy session 
        </div>

        <div class="col-md-12">
            Put the list of Visitations that have been created, start with Today's visitation only showing
        </div>

        <div class="col-md-12">
            Put the list of Visitations that have been created, start with Today's visitation only showing
        </div>
    </div>
</section>
</template>
<script>
export default {
data() {
    return {
        applicant: {},
        applicants: {},
        appointments: {},
        editMode: true,
        nations: [],
        patients: {},
        services: [],
        user: {},
    }
},
mounted() {
    this.getInitials();
    Fire.$on('refresh', response => {
        this.refreshAppointments(response);
        this.closeModal();
    });
    Fire.$on('searchInstance', ()=>{
        let query = this.$parent.search;
        axios.get('/api/hims/patients/search?q='+query)
        .then((response ) => {this.applicants = response.data.applicants;})
        .catch(()=>{});
    });
},
methods: {
    addApplicant(){
        this.$Progress.start();
        this.editMode = false;
        //this.applicant = {};
        Fire.$emit('ApplicantDataFill', {});
        $('#applicantModal').modal('show');
        this.$Progress.finish();
    },
    closeModal(){
        $('#applicantModal').modal('hide');
    },
    editApplicant(applicant){
        this.$Progress.start();
        this.editMode = true;
        this.applicant = applicant;
        Fire.$emit('ApplicantDataFill', applicant);
        $('#applicantModal').modal('show');
        this.$Progress.finish();
    },
    getApplicant(page=1){
        axios.get('/api/emr/hims/patients?page='+page)
        .then(response=>{
            this.refreshPatients(response); 
        });
    },
    getInitials() {
        axios.get('/api/emr/hims/patients')
        .then(response => {this.refreshPatients(response)})
        .catch(() => {
            this.$Progress.fail();
            toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
        });
    },
    refreshPatients(response) {
        this.nations = response.data.nations;
        this.patients = response.data.patients;
    }
},
props: {}
}
</script>