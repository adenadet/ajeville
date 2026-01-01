<template>
    <section class="container-fluid">
        <div class="modal fade" id="contactModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Update Contact' : 'Add Contact'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body"><HimsPatientFormContact :editMode="editMode" :contact="contact" /></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-header bg-dark">
                    <h3 class="card-title">List of Contact(s)</h3>
                    <div class="card-tools"><button type="button" @click="addContact()" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></button></div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover text-nowrap">
                    <thead><tr><th>Name</th><th>Address</th><th>Phone Number</th><th>Email</th><th></th></tr></thead>
                    <tbody>
                        <tr v-for="contact in patient.contacts" :key="contact.id" >
                        <td>{{contact.name}}</td>
                        <td v-html="contact.address"></td>
                        <td>{{contact.phone+(contact.alt_phone != null ? ', '+contact.alt_phone : '')}}</td>
                        <td>{{contact.email_address}}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary" @click="editContact(contact)"><i class="fa fa-edit"></i></button>
                            </div>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
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
    /*Fire.$on('refresh', response => {
        this.refreshAppointments(response);
        this.closeModal();
    });
    Fire.$on('searchInstance', ()=>{
        let query = this.$parent.search;
        axios.get('/api/hims/patients/search?q='+query)
        .then((response ) => {this.applicants = response.data.applicants;})
        .catch(()=>{});
    });*/
},
methods: {
    closeModal(){
        $('#applicantModal').modal('hide');
    },
    editApplicant(applicant){
        this.loading = true;
        this.editMode = true;
        this.applicant = applicant;
        //Fire.$emit('ApplicantDataFill', applicant);
        $('#applicantModal').modal('show');
        this.loading = false;
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