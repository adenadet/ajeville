<template>
<section>
    <form @submit.prevent="editMode ? updateContact() : createContact()">
        <alert-error :form="contactData"></alert-error> 
        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    <label for="vendor_id">Vendor:</label>
                    <div v-if="vendor != null" class="form-control" v-html="vendor.name"></div> 
                    <input v-if="vendor != null"  type="hidden" v-model="contactData.vendor_id" id="vendor_id" />
                    <select v-else class="form-control" id="vendor_id" name="vendor_id" v-model="contactData.vendor_id">
                        <option value="">--Select Vendor--</option>
                        <option v-for="vendor in vendors">{{ vendor.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select class="form-control" v-model="contactData.status" id="status">
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input class="form-control" type="text" v-model="contactData.title" id="title" />
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input class="form-control" type="text" v-model="contactData.first_name" id="first_name" />
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input class="form-control" type="text" v-model="contactData.last_name" id="last_name" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" v-model="contactData.email" id="email" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input class="form-control" type="text" v-model="contactData.phone" id="phone" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="alt_phone">Alt Phone:</label>
                    <input class="form-control" type="text" v-model="contactData.alt_phone" id="alt_phone" />
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</section>
</template>

<script>
export default {
    data() {
        return {
            contactData: new Form({
                alt_phone: '',
                email: '',
                first_name: '',
                id: '',
                last_name: '',
                phone: '',
                status: '',
                title: '',
                vendor_id: '',
            }),
            loading: false,
            vendors: [],
        };
    },
    emits:['vendorContactReload'],
    methods: {
        createContact() {
            this.loading = true;
            if ((this.contactData.vendor_id == null) || (this.contactData.vendor_id == '')){this.contactData.vendor_id = this.vendor.id;}
            this.contactData.post('/api/procurement/vendor_contacts')
            .then(response =>{
                this.loading = false;
                this.$emit('vendorContactReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor Contact has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/procurement/vendors/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendor Contact Form did not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.vendors = response.data.vendors
        },
        updateContact(){
            this.loading = true;
            this.contactData.put('/api/procurement/vendor_contacts/'+this.contactData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('vendorContactReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor Contact has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        }
    },
    mounted() {
        this.getAllInitials();
    },
    props:{
        contact: Object,
        editMode: Boolean,
        vendor: Object,
    },
    watch:{
        contact(){
            this.contactData.reset();
            if ((this.contact != null) && (this.contact.id != null)){
                this.contactData.fill(this.contact);
            }
            else if((this.vendor != null) && (this.vendor.id != null)){
                this.contactData.vendor_id = this.vendor.id
            }
            console.log(this.contactData.vendor_id);
        },
        vendor(){
            this.contactData.vendor_id = this.vendor.id;
        }
    }
};
</script>