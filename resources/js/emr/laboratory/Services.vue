<template>
    <section class="container-fluid">
        <div class="modal fade" id="serviceModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="overlay" v-if="loading">
                        <i class="fas fa-2x fa-sync fa-spin"></i>
                    </div>
                    <div class="modal-header">
                        <h4 class="modal-title">{{ editMode ? 'Edit Service' :'New Service' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <LaboratoryFormService :editMode="editMode" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laboratory Services</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn bg-dark" @click="addService()"><i class="fas fa-plus mr-1"></i> Create New</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Bottle</th>
                                    <th>Result Template</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(service, index) in services.data" :key="service.id"  :class="service.special != null ? 'bg-danger' : ''" @click="updateRequest(service)">
                                    <td>{{ index | addOne }}</td>
                                    <td>{{ service.name }}</td>
                                    <td>{{ service.category != null ? service.category.name : 'No Category Yet' }}</td>
                                    <td>{{ service.bottle != null ? service.bottle.name : 'No Bottle Selected' }}</td>
                                    <td>{{ service.template != null ? service.template.name : 'No Template Selected' }}</td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <button class="btn btn-block dropdown-item" @class="modify(service)"><i class="fas fa-edit mr-2 text-primary"></i> Modify Service</button>
                                            <button class="btn btn-block dropdown-item" @class="reactivateService(service.id)"><i class="fas fa-power-off mr-2"></i>{{ service.status == true ? 'Deactivate Service' : 'Reactivate Service' }} </button>
                                            <button class="btn btn-block dropdown-item" @class="reactivateService(service.id)"><i class="fas fa-power-off mr-2 text-danger"></i>{{ service.status == true ? 'Deactivate Service' : 'Reactivate Service' }} </button>
                                        </div>
                                    </td>  
                                </tr>
                            </tbody>
                        </table>
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
            services: {},
            editMode: true,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addService(){
            this.$Progress.start();
            this.editMode = false;
            Fire.$emit('ServiceDataFill', {});
            $('#serviceModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#serviceModal').modal('hide');
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/services?page='+page)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshQueue(response) {
            this.services = response.data.services;
        },
        updateRequest(request){
            this.request = request;
        }
    },
    props: {}
}
</script>