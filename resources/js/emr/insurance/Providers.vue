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
                    <InsuranceFormProvider :provider.sync="provider" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-tabs">
        <div class="card-header bg-dark">
            <h3 class="card-title">Active Providers</h3>
            <div class="card-tools">
                <button @click="addNew()" class="btn btn-sm btn-primary" >Create New</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="tabContent">
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
                                    <button class="btn btn-block dropdown-item" @click="deactivateProvider(provider.id)"><i class="fas fa-power-off text-danger mr-2"></i> Deactivate Provider</button>
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
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="providers.per_page != null ? providers.per_page : 52" :records="providers.total != null ? providers.total : 550" >
            </pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            provider: {},
            providers: {},
            provider_types: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addNew(){
            this.editMode = false;
            this.loading = true;
            this.provider = {};
            $('#providerModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#providerModal').modal('hide');
        },
        deactivateProvider(id){
            this.$swal.fire({
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
                        this.$swal.fire('Deactivated!', 'Provider has been deactivated.', 'success');
                        this.refresh(response);  
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editProvider(provider){
            this.loading = true;
            this.editMode = true;
            this.provider = provider;
            $('#providerModal').modal('show');
            this.loading = false;
        },
        getAllInitials(page = 1){
            this.loading = true;
            axios.get('/api/emr/insurance/providers?page='+page).then(response =>{
                this.refresh(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Providers were not loaded successfully',})
            });
        },
        refresh(response){
            this.providers = response.data.providers;
            this.provider_types = response.data.provider_types;
        }
        
    },
}
</script>