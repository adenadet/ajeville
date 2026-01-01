<template>
<section>
    <form>
        <alert-error :form="AuthRequestFormData"></alert-error>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Transaction ID </label>
                    <div class="bordered">
                        <p v-for="transaction in transactions" :key="transaction.id">
                            {{ transaction.visit.patient | patientName}} for {{ transaction.item_name }} at {{ transaction.item_total }}
                        </p>
                    </div>
                    <has-error :form="AuthRequestFormData" field="name"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person" v-model="AuthRequestFormData.contact_person" />
                    <has-error :form="AuthRequestFormData" field="contact_person"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Provider</label>
                    <select class="form-control" id="request_method" name="request_method" v-model="AuthRequestFormData.plan_id" required>
                        <option value="">--Select Request Type--</option>
                        <option value="email">Email</option>
                        <option value="call">Call</option>
                        <option value="portal">Portal</option>
                        <option value="sms">SMS</option>
                    </select>
                    <has-error :form="AuthRequestFormData" field="request_method"></has-error>
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Request Via</label>
                    <select class="form-control" id="request_method" name="request_method" v-model="AuthRequestFormData.request_method" required>
                        <option value="">--Select Request Type--</option>
                        <option value="email">Email</option>
                        <option value="call">Call</option>
                        <option value="portal">Portal</option>
                        <option value="sms">SMS</option>
                    </select>
                    <has-error :form="AuthRequestFormData" field="request_method"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" v-model="AuthRequestFormData.phone" />
                    <has-error :form="AuthRequestFormData" field="phone"></has-error>
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="email" name="email" v-model="AuthRequestFormData.email" />
                    <has-error :form="AuthRequestFormData" field="email"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <wysiwyg v-model="AuthRequestFormData.description" rows="4" name="description" id="description"></wysiwyg>
                    <has-error :form="AuthRequestFormData" field="description"></has-error>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updateAuthRequestForm() : createAuthRequestForm()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            AuthRequestFormData: new Form({
                transactions: [],
                request_method: '',
                contact_person: '',
                plan_id: '',
                phone: '',
                email: '',
                description:'',
            }),
            editMode: false,
        }
    },
    mounted(){
        //this.getAllInitials();
    },
    methods: {
        createAuthRequestForm(){
            this.$Progress.start();
            this.AuthRequestFormData.post()
        }
    },
    props:{
        transactions: Array,
    },
}
</script>