<template>
<section class="container-fluid">
    <EMRPatientDetailFull />
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
            allergy: {},
            contact: {},
            editMode: true, 
            nok:{},
            nations: [],  
            user:{}, 
        }
    },
    mounted() {
        this.getInitials();
        /*/this.$on('Reload', response =>{this.refreshProfile(response);});
        this.$on('pageReload', () => {
            this.getInitials();
            this.closeModal();
        });*/
    },
    methods:{
        addAllergy(){
            this.loading = true;
            this.editMode = false;
            let details = {'allergy': {}, 'patient':this.user};
            Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.loading = false;
        },
        addContact(){
            this.loading = true;
            this.editMode = false;
            let details = {'patient': this.patient, 'contact': {}};
            //Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#allergyModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#contactModal').modal('hide');
        },
        editAllergy(allergy){
            this.loading = true;
            this.editMode = true;
            let details = {'allergy': allergy, 'patient':this.user};
            //Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.loading = false;
        },
        editContact(contact){
            this.loading = true;
            this.editMode = true;
            let details = {'patient': this.patient, 'contact': contact};
            //Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.loading = false;
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/patients/'+this.$route.params.id).then(response =>{
                this.loading = false;
                this.reloadPatient(response);
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Profile not loaded successfully',});
            });
        },
        reloadPatient(response){
            this.$store.dispatch('setPatientCookie', response.data.patient);
        },
    }
}
</script>