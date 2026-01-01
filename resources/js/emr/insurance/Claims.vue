<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Parameters</h3>
                    </div>
                    <div class="card-body">
                        <form>
                            <!--// Items in the query 1. Patient ID 2. Provider ID 3. Plan ID 4. Start Date 5. End Date 6. List of Visits 7. Report Type 8. Report Format
                        -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Report Type</label>
                                        <select  v-model="ClaimsData.report_type" name="report_type" id="report_type">
                                        </select>
                                        <has-error :form="ClaimsData" field="report_type"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="ClaimsData.report_type == 'patient'">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Report Type</label>
                                        <wysiwyg v-model="ClaimsData.description" rows="4" name="description" id="description"></wysiwyg>
                                        <has-error :form="ClaimsData" field="description"></has-error>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark">
                        <h4 class="card-title">Claims Report</h4>
                        <div class="card-tools">

                        </div>
                    </div>
                    <div class="card-body">
                        Put a table here for each completed visit.
                    </div>
                    <div class="card-footer">
                        <!--pagination /-->
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            ClaimsData: new Form({
                patients: [],
                transactions: [],
                visits: [],
                provider_id: '',
                plan_id: '',
                phone: '',
                email: '',
                description:'',
            }),
            editMode: false,
            loading: false,
        }
    },
    mounted(){
        this.getAllInitials();
        Fire.$on('updateTransactionList', transactions => {
            this.loading = true;
            if (this.source == 'uncovered'){

            }
            this.transactions = transactions;
            this.loading = false;
        });
    },
    methods: {
        createAuthCodeForm(){
            this.$Progress.start();
            this.AuthCodeData.transactions = this.transactions;
            this.AuthCodeData.post('/api/emr/insurance/auth_codes')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshProviders', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Provider has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        getAllInitials(page=1){
            this.loading = true;
            this.$Progress.start();
            axios.get('/api/emr/insurance/dashboard')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(() => {
                this.loading = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        }
    },
    props:{
        source: String,
    },
}
</script>