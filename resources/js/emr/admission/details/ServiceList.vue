<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="serviceFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Service Details {{ service.name }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormService :service.sync="service" :editMode="editMode" @refreshServiceForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="serviceViewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Service Details {{ service.name }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionDetailService :service.sync="service" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-primary btn-xs" @click="addService"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="services.length > 0">
            <tr v-for="(service, index) in services" :key="service.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ service.emr_service?.item?.name || ''}}</td>
                <td>{{ service.category?.name || 'Unassigned Category'}}</td>
                <td :title="service.emr_service?.item?.description" v-html="readMore(service.emr_service?.item?.description, 50, '...')"></td>
                <td>
                    <span v-if="service.status == 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewService(service)"><i class="fa fa-eye mr-1 text-primary"></i> View Service </button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateService(service)"><i class="fa fa-edit mr-1 text-warning"></i> Update Service </button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateService(service)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Service </button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5" class="text-center">No Service Found</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            service: {},
        }
    },
    emits:['refreshServiceList'],
    methods: {
        addService(){
            this.loading = true;
            this.editMode = false;
            this.service = {};
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#serviceFormModal').modal('show');
            
        },
        deactivateService(service){
            this.$swal.fire({
                title: 'Are you sure?',
                text: 'You are about to deactivate this service type!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Deactivate it!',
                cancelButtonText: 'Cancel'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    this.loading = true;
                    axios.delete('/api/emr/admission/services/'+service.id)
                    .then((response)=>{
                        this.$swal.fire(
                            'Deactivated!',
                            'Service  has been deactivated.',
                            'success'
                        );
                        this.$emit('refreshServiceList');
                        this.loading = false;
                    })
                    .catch((error)=>{
                        this.loading = false;
                        this.$swal.fire(
                            'Error!',
                            'An error occurred while deactivating service type.',
                            'error'
                        );
                    });
                }
            });
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshServiceList');
        },
        updateService(service){
            this.loading = true;
            this.editMode = true;
            this.service = service;
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
        viewService(service){
            this.loading = true;
            this.service = service;
            $('#serviceViewModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        
    },
    props:{
        services: Array,
        ward: Object,
    }
}
</script>