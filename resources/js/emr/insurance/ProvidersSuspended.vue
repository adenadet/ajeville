<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Suspended Providers</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-bordered">
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
                                            <router-link :to="'/insurance/providers/'+provider.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-info"></i> View Provider</router-link>
                                            <button class="btn btn-block dropdown-item" @click="editProvider(provider)"><i class="fas fa-edit mr-2 text-primary"></i> Update Provider</button>
                                            <button class="btn btn-block dropdown-item" @click="reactivateProvider(provider.id)"><i class="fas fa-power-off text-danger mr-2"></i> Reactivate Provider</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr><td colspan="6">No Provider has been created yet. <button @click="addNew()" class="btn btn-primary btn-xs">Create Provider</button></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="providers" @pagination-change-page="getAllInitials">
                            <span slot="prev-nav">&lt; Previous </span>
                            <span slot="next-nav">Next &gt;</span>
                        </pagination>
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
            editMode: false,
            form: new Form({}),
            loading: false,
            provider: {},
            providers: {},
            provider_types: [],
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('refreshProviders', response => {
            this.refresh(response);
            this.closeModal();
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
        editProvider(provider){
            this.editMode = true;
            Fire.$emit('providerDataFill', provider);
            $('#providerModal').modal('show');
        },
        getAllInitials(page = 1){
            this.$Progress.start();
            this.loading = true;
            axios.get('/api/emr/insurance/providers?q=inactive&page='+page).then(response =>{
                this.refresh(response);
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(()=>{
                this.loading = false;
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Visits were not loaded successfully',})
            });
        },
        reactivateProvider(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to deactivate this provider!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/emr/insurance/providers/'+id)
                    .then(response=>{
                        Swal.fire('Deactivated!', 'Provider has been deactivated.', 'success');
                        this.refresh(response);  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refresh(response){
            this.providers = response.data.providers;
            this.provider_types = response.data.provider_types;
        }
        
    },
}
</script>