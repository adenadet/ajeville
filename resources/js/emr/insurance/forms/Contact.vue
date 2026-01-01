<template>
<section>
    <form>
        <alert-error :form="ContactData"></alert-error>
        <div class="row" v-if="provider == null">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Provider</label>
                    <div class="form-control" v-html="provider.name"></div>
                    <input type="hidden" name="provider_id" id="provider_id" v-model="ContactData.provider_id" />
                    <has-error :form="ContactData" field="provider_id"></has-error>
                </div>
            </div>
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name </label>
                    <input type="text" class="form-control" id="name" name="name" v-model="ContactData.name" />
                    <has-error :form="ContactData" field="name"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="email" name="email" v-model="ContactData.email" />
                    <has-error :form="ContactData" field="email"></has-error>
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" v-model="ContactData.phone" />
                    <has-error :form="ContactData" field="phone"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="ContactData.status">
                        <option value="">--Status--</option>
                        <option value=0>Inactive</option>
                        <option value=1>Active</option>
                    </select>
                    <has-error :form="ContactData" field="status"></has-error>
                </div>
            </div>
        </div>
       <button @click.prevent="editMode ? updateContact() : createContact()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            ContactData: new Form({
                id: '',
                name: '',
                provider_id: '',
                phone: '',
                email: '',
                status: ''
            }),
            loading: false,
        }
    },
    mounted() {
        /*Fire.$on('contactDataFill', contact=> {
            if (contact != null){this.ContactData.fill(contact);}
            if (this.provider != null){ this.ContactData.provider_id = this.provider.id}
        });
        Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('api/emr/domiciliary/search?q='+query)
            .then((response ) => {this.applicants = response.data.applicants;})
            .catch(()=>{});
        });*/
    },
    methods: {
        createContact(){
            this.loading = true;
            this.ContactData.provider_id = this.provider_id;
            this.ContactData.post('/api/emr/insurance/contacts')
            .then(response => {
                this.$Progress.finish();
                this.$emit('refreshProviderContact', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Contact has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        updateContact(){
            this.loading = true;
            this.ContactData.put('/api/emr/insurance/contacts/' + this.ContactData.id)
            .then(response => {
                this.$Progress.finish();
                this.$emit('refreshProviderContact', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Contact has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        }
    },
    props: {
        editMode: Boolean,
        provider: Object,
        provider_types: Array,
    }
}
</script>