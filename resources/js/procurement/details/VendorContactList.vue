<template>
<div class="card">
    <div class="modal fade" id="contactFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update' : 'Create'}} Contact</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormVendorContact :contact="contact" :editMode.sync="editMode" :vendor="vendor" @vendorContactReload="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-navy">
        <h3 class="card-title">Contact Persons of {{ vendor.name }}</h3>
        <div class="card-tools">
            <div class="input-group input-group" style="width: 350px;">
                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="search" @change="getInitials">
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary mr-1" @click="getInitials" title="Search Vendor"><i class="fas fa-search"></i></button>
                    <button type="button" class="btn btn-primary ml-1" @click="addVendorContact" title="Add New Vendor"><i class="fa fa-user-plus"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0" style="height: 300px;">
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone no</th>
                    <th>Alt. Phone no.</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="contact in contacts">
                    <td>{{ contact.title }} {{ contact.first_name }} {{ contact.last_name }}</td>
                    <td>{{ contact.email }}</td>
                    <td>{{ contact.phone }}</td>
                    <td>{{ contact.alt_phone }}</td>
                    <td>{{ contact.status == 1 ? 'Active' : 'Inactive'}}</td>
                    <td>
                        <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu">
                            <button class="btn btn-block dropdown-item" @click="updateVendorContact(contact)"><i class="fa fa-edit mr-1"></i> Edit </button>
                            <button class="btn btn-block dropdown-item" @click="deleteVendorContact(contact.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete </button>
                        </div>
                    </td>
                </tr>        
            </tbody>
        </table>
    </div>
</div>
</template>
<script>
export default {
    data(){
        return  {
            contact: {},
            contacts: {},
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            search: '',
        }
    },
    methods:{
        addVendorContact(){
            this.loading = true;
            this.editMode = false;
            this.contact = {};
            $('#contactFormModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#contactFormModal').modal('hide');
            $('#contactModal').modal('hide');
        },
        deleteVendorContact(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/procurement/vendor_contacts/'+id)
                    .then(response=>{
                        this.$emit('storeReload', response);  
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                        this.$wal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href="#">Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/vendor_contacts/vendor/'+this.$route.params.id)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Vendors loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendors not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.contacts = response.data.contacts;
        },
        updateVendorContact(contact){
            this.loading = true;
            this.editMode = true;
            this.contact = contact;
            $('#contactFormModal').modal('show');
            this.loading = false;         
        },
    },
    mounted() {
        this.getInitials();
    },
    props:{
        vendor: Object,
    }
}
</script>