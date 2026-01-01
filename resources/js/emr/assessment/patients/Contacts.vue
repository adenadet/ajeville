<template>
<div class="card card-primary">
    <div class="modal fade" id="contactModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Contact' : 'New Contact'"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HimsPatientFormContact :patient="patient" :editMode="editMode" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header">
        <h3 class="card-title">List of Contacts</h3>
        <div class="card-tools"><button type="button" @click="addContact()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-hover text-nowrap">
        <thead><tr><th>Name</th><th>Address</th><th>Phone Number</th><th>Email</th><th></th></tr></thead>
        <tbody v-if="contacts != null && contacts.data != null">
            <tr v-for="contact in contacts.data" :key="contact.id" >
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
        <tbody v-else>
            <tr><td colspan="5">No contact has been added for this patient</td></tr>
        </tbody>
        </table>
    </div>
    <div class="card-footer">
        <pagination :data="contacts" @pagination-change-page="getInitials()">
            <span slot="prev-nav">&lt; Previous </span>
            <span slot="next-nav">Next &gt;</span>
        </pagination>
    </div>
</div>
</template>
<script>
export default {
    data() {
        return {
            contact: {},
            editMode: true,
            patient: {},
            contacts: {},
        }
    },
    mounted() {  
        Fire.$on('refreshPatientContacts', patient => {
            this.patient = patient;
            this.getInitials();
            this.closeModal();
        });  
    },
    methods: {
        addContact(){
            this.$Progress.start();
            this.editMode = false;
            let details = {'contact': {}, 'patient':this.patient};
            Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#contactModal').modal('hide');
        },
        deleteContact(id){
            Swal.fire({
                title: 'Are you sure, you want to delete this?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/emr/nursing/patient_tasks/'+id)
                    .then(response=>{
                        Swal.fire('Confirmed!', 'The Patient Task has been deleted.', 'success');
                        this.refreshDomiciliaries(response);   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editContact(contact){
            this.$Progress.start();
            this.editMode = true;
            this.contact = contact;
            let details = {'contact': contact, 'patient':this.patient};
            Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            if (this.patient != null){
                axios.get('/api/emr/hims/contacts/'+this.patient.id+'?page='+page).then(response =>{
                    this.$Progress.finish();
                    this.contacts = response.data.contacts;
                })
                .catch(()=>{
                    this.$Progress.fail();
                    toast.fire({icon: 'error', title: 'Contacts failed to load successfully',});
                });
            }
            else{
                this.allergies = [];
            }
        },
    },
    props: {
        //patient: Object,
    }
}
</script>