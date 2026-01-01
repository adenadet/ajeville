<template>
<section class="container-fluid">
    <div class="modal fade" id="providerModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Provider' : 'Create Provider'"></h4>
                    <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body bg-white">
                    <InsuranceFormProvider :provider="provider" :provider_types="provider_types" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1" >
            <div class="d-sm-flex align-items-baseline report-summary-header">
                <h3 class="card-title pl-3">Active Providers</h3>
                <div class="card-tools ml-auto pr-3">
                    <button @click="addNew()" class="btn btn-sm btn-primary" >Create New</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="tabContent">
                <table class="table table-striped tanullble-bordered">
                    <thead class="th-dark">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact Person</th>
                            <th>Active Plans</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="providers != null && providers.data != null && providers.data.length != 0">
                        <tr v-for="provider in providers.data" :key="provider.id">
                            <td>{{ provider.name }}</td>
                            <td>{{ provider.insurance_type != null ? provider.insurance_type.name : 'Provider Type is invalid/NA' }}</td>
                            <td>{{ provider.contact_person }}</td>
                            <td>{{ provider.plans.length }}</td>
                            <td>{{ provider.status == 0 ? 'Inactive' : 'Active'}}</td>
                            <td>
                                <span class="nav-link" data-toggle="dropdown" href="#">
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <router-link :to="'/insurance/providers/'+provider.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2"></i> View Provider</router-link>
                                    <button class="btn btn-block dropdown-item"><i class="fas fa-cc mr-2"></i> Update Provider</button>
                                    <button class="btn btn-block dropdown-item"><i class="fas fa-file mr-2"></i> Deactivate Provider</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr><td colspan="6">No Provider has been created yet. <button @click="addNew()" class="btn btn-primary btn-xs">Create Provider</button></td></tr>
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
            editMode: false,
            provider: {},
            providers: {},
            provider_types: [],
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('AssessmentTypeDataFill', request => {
            if (request != null) {
                this.VisitForm.name = request.name;
                this.VisitForm.description = request.description;    
                this.VisitForm.id = request.id;
                this.VisitForm.assessments = [];
                for (let i = 0; i < request.assessments.length; i++) {
                    this.VisitForm.assessments.push(request.assessments[i].id);
                }  
            }
            else { this.VisitForm.reset(); }
        });
    },
    methods: {
        addNew(){
            this.editMode = false;
            Fire.$emit('planDataFill', {});
            $('#planModal').modal('show');
        },
        closeModal(){
            $('#planModal').modal('hide');
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/insurance/plans?').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.plans = response.data.plans;
        }
        
    },
}
</script>