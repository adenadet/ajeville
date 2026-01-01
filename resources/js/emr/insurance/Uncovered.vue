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
        <div class="card card-tabs">
            <div class="card-header bg-dark">
                <h3 class="card-title">List of Pending Uncovered Transactions</h3>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="tabContent">
                    <table class="table table-striped tanullble-bordered">
                        <thead class="th-dark">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Active Plans</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="providers != null && providers.data != null && providers.data.length != 0">
                            <tr v-for="provider in providers.data" :key="provider.id">
                                <td>{{ provider.name }}</td>
                                <td>{{ provider.insurance_type != null ? provider.insurance_type.name : 'Provider Type is invalid/NA' }}</td>
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
            transactions: {},
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
            Fire.$emit('providerDataFill', {});
            $('#providerModal').modal('show');
        },
        closeModal(){
            $('#providerModal').modal('hide');
        },
        createVisit() {
            this.$Progress.start();
            this.VisitForm.put('/api/emr/insurance/transactions/uncoveredDesktop/P')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'A Visit has been created',
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
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/insurance/transactions/uncovered').then(response =>{
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
            this.providers = response.data.providers;
            this.provider_types = response.data.provider_types;
        }
        
    },
}
</script>