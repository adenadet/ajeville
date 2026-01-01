<template>
<section class="container-fluid">
    <div class="modal fade" id="contactModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Contact Person' : 'Create Contact Person'"></h4>
                    <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body bg-white">
                    <InsuranceFormContact :contact.sync="contact" :provider.sync="provider" :editMode="editMode" @refreshProvideContact="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title">Contacts</h4>
            <div class="card-tools"><button @click="newContact()" class=" btn btn-primary btn-xs"><i class="fa fa-user-plus mr-1"></i> New Contact Person</button></div>
        </div>
        <div class="card-body p-0">
            <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 300px;">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                    <tr>
                        <th class="font-weight-bold">Name</th>
                        <th class="font-weight-bold">Email</th>
                        <th class="font-weight-bold">Phone Number</th>
                        <th class="font-weight-bold">Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody v-if="contacts != null && contacts.data != null && contacts.data.length != 0">
                        <tr v-for="contact in contacts.data" :key="contact.id">
                            <td>{{ contact.name }}</td>
                            <td>{{ contact.email }}</td>
                            <td>{{ contact.phone }}</td>
                            <td>{{ contact.status == 1 ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <span class="nav-link" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <button class="btn btn-block dropdown-item" @click="updateContact(contact)"><i class="fas fa-edit mr-2"></i> Update Contact</button>
                                    <button class="btn btn-block dropdown-item" @click="deleteContact(contact.id)"><i class="fas fa-power-off mr-2"></i> Deactivate Contact</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr><td colspan="5">No Contact Person has been created yet. <button @click="newContact()" class="btn btn-primary btn-xs">Create Contact Person</button></td></tr>
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
            contacts: {},
            editMode: false,
            form: new Form({}),
            loading: false,
            plans: {},
            provider: {},
            provider_types: [],
        }
    },
    mounted() {
        this.getAllInitials();   
    },
    methods: {
        closeModal(){
            $('#contactModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            var route_id = this.provider_id != null ? this.provider_id : this.$route.params.id;
            axios.get('/api/emr/insurance/contacts/provider/'+route_id).then(response =>{
                this.refresh(response);
                this.loading = false;
                this.closeModal();
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Provider Contacts were not loaded successfully',
                })
            });
        },
        newContact(){
            this.loading = true;
            this.editMode = false;
            this.contact = {};
            $('#contactModal').modal('show');
            this.loading = false;
        },
        refresh(response){
            this.contacts = response.data.contacts;
            this.providers = response.data.providers;
        },
        deleteContact(id){
            this.$swal.fire({
                title: 'Are you sure, you want to suspend this plan?',
                text: "You will be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/emr/insurance/contacts/'+id)
                    .then(response=>{
                        this.$swal.fire('Confirmed!', 'The Provider Plan has been suspended.', 'success');
                        this.refresh(response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        updateContact(contact){
            this.editMode = true;
            Fire.$emit('contactDataFill', contact);
            $('#contactModal').modal('show');
        },  
    },
    props: {
        provider_id: Number,
    },
}
</script>