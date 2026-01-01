<template>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form>
            <alert-error :form="AuthCodeData"></alert-error>
            <div class="row" >
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Transactions </label>
                        <div class="border border-1 rounded p-2">
                            <p v-for="transaction in transactions" :key="transaction.id">
                                {{ transaction.visit.patient | patientName}} for {{ transaction.item_name }} at {{ transaction.item_total | currency}}
                            </p>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <div class="form-group">
                        <label>Auth Code</label>
                        
                        <input type="text" class="form-control" id="auth_code" name="auth_code" v-model="AuthCodeData.auth_code" required />
                        <has-error :form="AuthCodeData" field="auth_code"></has-error>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label>Provider / Plan</label>
                        <input type="text" class="form-control" id="auth_code" name="auth_code" v-model="AuthCodeData.auth_code" required />
                        <has-error :form="AuthCodeData" field="auth_code"></has-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" v-model="AuthCodeData.contact_person" />
                        <has-error :form="AuthCodeData" field="contact_person"></has-error>
                    </div>
                </div> 
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Provided Via</label>
                        <select class="form-control" id="request_method" name="request_method" v-model="AuthCodeData.plan_id" required>
                            <option value="">--Select Request Type--</option>
                            <option value="email">Email</option>
                            <option value="call">Call</option>
                            <option value="portal">Portal</option>
                            <option value="sms">SMS</option>
                        </select>
                        <has-error :form="AuthCodeData" field="request_method"></has-error>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Description</label>
                        <wysiwyg v-model="AuthCodeData.description" rows="4" name="description" id="description"></wysiwyg>
                        <has-error :form="AuthCodeData" field="description"></has-error>
                    </div>
                </div>
            </div>
            <button @click.prevent="editMode ? updateAuthCodeForm() : createAuthCodeForm()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            AuthCodeData: new Form({
                transactions: [],
                auth_code: '',
                request_method: '',
                contact_person: '',
                plan_id: '',
                phone: '',
                email: '',
                description:'',
            }),
            editMode: false,
            loading: false,
            transactions: [],
        }
    },
    emits:['updateTransactionList', 'refreshTransactionList'],
    mounted(){
        //this.getAllInitials();
        /*Fire.$on('updateTransactionList', transactions => {
            this.loading = true;
            if (this.source == 'uncovered'){

            }
            this.transactions = transactions;
            this.loading = false;
        });*/
    },
    methods: {
        createAuthCodeForm(){
            //this.$Progress.start();
            this.loading = true;
            this.AuthCodeData.transactions = this.transactions;
            this.AuthCodeData.post('/api/emr/insurance/auth_codes')
            .then(response => {
                this.loading = false;
                //this.$Progress.finish();
                Fire.$emit('refreshProviders', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Transactions have been covered',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                //this.$Progress.fail();
            });
        }
    },
    props:{
        source: String,
    },
}
</script>