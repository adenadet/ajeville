<template>
    <section class="container-fluid overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="serviceFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="overlay" v-if="loading">
                        <i class="fas fa-2x fa-sync fa-spin"></i>
                    </div>
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title">{{ editMode ? 'Edit Service' : 'New Service' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <EMRLaboratoryFormService :editMode.sync="editMode" :service.sync="service" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Laboratory Services</h3>
                        <div class="card-tools">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" name="table_search" v-model="query" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default mr-1">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <select class="form-control mr-1" v-model="status">
                                        <option value="">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select> 
                                    <button type="submit" class="btn bg-dark" @click="addService()"><i class="fas fa-plus mr-1"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height:600px;">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Bottle</th>
                                    <th>Result Template</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="services.total > 0">
                                <tr v-for="(service, index) in services.data" :key="service.id"  :class="service.special != null ? 'bg-danger' : ''">
                                    <td>{{ addOne(index) }}</td>
                                    <td>{{ service.emr_service?.item?.name || 'Name not sorted' }}</td>
                                    <td>{{ service.category != null ? service.category.name : 'No Category Yet' }}</td>
                                    <td>{{ service.bottle_type != null ? service.bottle_type.name : 'No Bottle Selected' }}</td>
                                    <td>{{ service.template != null ? service.template.name : 'No Template Selected' }}</td>
                                    <td v-html="readMore(service.description, 50, '...')" :title="service.description"></td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <router-link :to="'/emr/laboratory/settings/services/'+service.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-1"></i>View Service </router-link>
                                            <button class="btn btn-block dropdown-item" @click="updateService(service)"><i class="fas fa-edit mr-1 text-primary"></i>Modify Service</button>
                                            <button class="btn btn-block dropdown-item" @click="reactivateService(service.id)"><i class="fas fa-power-off mr-1 text-danger"></i>{{ service.status == 1 ? 'Deactivate Service' : 'Reactivate Service' }}</button>
                                        </div>
                                    </td>  
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="7">No Services meets your requirements.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="col-12">
                            <pagination v-model="current_page" @paginate="getInitials" :per-page="services.per_page != null ? services.per_page : 52" :records="services.total != null ? services.total : 550" ></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRLaboratoryFormService from '@/emr/laboratory/forms/Service.vue';
import InventoryFormItem from '@/inventory/forms/Item.vue';
export default {
    components:{
        EMRLaboratoryFormService, InventoryFormItem
    },
    data() {
        return {
            current_page: 1,
            editMode: true,
            loading: false,
            query: '',
            service: {},
            services: {data:[], total:0,},
            status: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addService(){
            this.loading = true;
            this.editMode = false;
            this.service = {};
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#serviceFormModal').modal('hide');
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/services?page='+page)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Services did not load successfully',
                })
            });
        },
        refreshQueue(response) {
            this.services = response.data.services;
        },
        updateService(service){
            this.loading = true;
            this.editMode = true;
            this.service = service;
            $('#serviceFormModal').modal('show');
            this.loading = false;
        }
    },
}
</script>