<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="serviceFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Service Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <OperationFormService :service.sync="service" :editMode="editMode" :source="source" @refreshServiceForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Unique ID</th>
                <th>Service Type</th>
                <th>Status</th>
                <th>Description</th>
                <th><button class="btn btn-xs btn-primary" @click="addService"><i class="fa fa-plus mr-1"></i> Add</button></th>
            </tr>
        </thead>
        <tbody v-if="services.length > 0">
            <tr v-for="s in services" :key="s.id">
                <td>{{ s.item != null ? s.item.name : 'Deactivated item' }}</td>
                <td>{{ s.unique_id }}</td>
                <td>{{ s.service_type != null ? s.service_type.name : 'No Service Type' }}</td>
                <td>{{ s.status == 1 ? 'Active' : 'Inactive' }}</td>
                <td v-html="s.description"></td>
                <td></td>
            </tr>        
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Services Found</td></tr>
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
            service:{},
        }
    },
    emits:['refreshPage'],
    mounted() {
    },
    methods: {
        addService(){
            this.loading = true;
            this.editMode = false;
            this.service = {
                item: {
                    name: '',
                    description: '',
                    status: 1,
                    service_type_id: '',
                },
                service: {},
            };
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#serviceFormModal').modal('hide');
        },
        refreshPage() {
            this.closeModals();
            this.$emit('refreshPage');
        },
        updateService(service){
            this.loading = true;
            this.editMode = true;
            this.service = service;
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
    },
    props: {
        services: Array,
        source: String,
    },
    watch:{}
}
</script>