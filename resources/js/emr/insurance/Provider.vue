<template>
<section class="container-fluid">
    <div class="modal fade" id="providerModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Provider' : 'Create Provider'"></h4>
                    <button type="button" class="close" @click="closeModal()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body bg-white">
                    <InsuranceFormProvider :provider.sync="provider" :editMode="editMode" @refreshProviders="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Provider Info.</h4>
                    <div class="card-tools"><button @click="updateProvider()" class=" btn btn-primary btn-xs"><i class="fa fa-edit mr-1"></i> Update Details</button></div>
                </div>
                <div class="card-body p-0 overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <div class="table-responsive border rounded p-0">
                        <table class="table">
                            <tbody>
                                <tr><td>Name</td><td><strong v-html="provider.name"></strong></td></tr>
                                <tr><td>Type</td><td><strong v-html="provider.insurance_type != null ? provider.insurance_type.name: 'Not Specified'"></strong></td></tr>
                                <tr><td>Website</td><td><strong v-html="provider.website"></strong></td></tr>
                                <tr><td>Portal</td><td><strong v-html="provider.portal"></strong></td></tr>
                                <tr><td>Email</td><td><strong v-html="provider.email"></strong></td></tr>
                                <tr><td>Phone</td><td><strong v-html="provider.phone"></strong></td></tr>
                                <tr><td>Status</td><td>{{ provider.status == 1 ? 'Active' : 'Inactive' }}</td></tr>
                                <tr><td>Description</td><td v-html="provider.description"></td></tr>
                                <tr><td>Created By</td><td>{{ FullName(provider.creator) }}</td></tr>
                                <tr><td>Created At</td><td>{{ ExcelDate(provider.created_at) }}</td></tr>
                                <tr><td>Updated By</td><td>{{ FullName(provider.updator) }}</td></tr>
                                <tr><td>Created At</td><td>{{ ExcelDate(provider.updated_at) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="row">
                <InsuranceDetailProviderPlans :provider_id.sync="provider.id" />
                <InsuranceDetailProviderContacts :provider_id.sync="provider.id" />
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            contacts: [],
            editMode: false,
            loading: true,
            provider: {},
            provider_types: [],
            plans: [], 
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addContact(){
            this.editMode = false;
            this.loading = true;
            this.contact = {};
            $('#contactModal').modal('show');
            this.loading = false;
        },
        addPlan(){
            this.editMode = false;
            this.loading = true;
            this.plan = {};
            $('#planModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#contactModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/insurance/providers/'+this.$route.params.id).then(response =>{
                this.refresh(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Provider was not loaded successfully',
                })
            });
        },
        refresh(response){
            this.contacts = response.data.contacts;
            this.provider = response.data.provider;
            this.provider_types = response.data.provider_types;
            this.plans = response.data.plans;
        },
        updateProvider(){
            this.loading = true;
            this.editMode = true;
            $('#providerModal').modal('show');
            this.loading = false;
        },
    },
}
</script>