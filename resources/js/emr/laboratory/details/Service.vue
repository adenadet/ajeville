<template>
<section>
    <div class="modal fade" id="itemModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Item: {{item.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormItem :editMode.sync="edit_mode" :item.sync="item" @itemReload="getAllInitials()"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Service Detail</h3>
        </div>
        <div class="card-body">
            <h3 class="text-primary"><i class="fas fa-flask"></i> {{ service.service != null && service.service.item != null ? service.service.item.name : 'Name not sorted' }}</h3>
            <p class="text-muted" v-html="service.service != null ? service.service.description : ''"></p>
            <br>
            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Category</b> <a class="float-right">{{ service.category != null ? service.category.name : '' }}</a></li>
                <li class="list-group-item"><b>Item</b> 
                    <a class="float-right" v-if="service.service != null && service.service.item != null">
                        {{  service.service.item.name }} 
                        <button class="btn btn-xs btn-primary" @click="updateItem(service.service.item)"><i class="fa fa-edit"></i></button>
                    </a>
                </li>
                <li class="list-group-item"><b>Bottle Type</b> <a class="float-right">{{ service.bottle_type != null ? service.bottle_type.name : '' }}</a></li>
                <li class="list-group-item"><b>Specimen Type</b> <a class="float-right">{{ service.specimen_type != null ? service.specimen_type.name : '' }}</a></li>
                <li class="list-group-item"><b>Result Template</b> <a class="float-right">{{ service.result_template != null ? service.result_template.name : '' }}</a></li>
                
            </ul>
            <div class="text-muted">
                <p class="text-sm">Creator <b class="d-block">{{ FullName(service.creator) }}</b></p>
                <p class="text-sm">Last Updater <b class="d-block">{{ FullName(service.updater) }}</b></p>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            bottle_types: [],
            categories: [],
            edit_mode: false,
            item: {},
            loading: false,
            result_templates: [],
            specimen_types: [],
            serviceForm: new Form({
                bottle_type_id: '',
                category_id: '',
                description: '',
                name: '',
                result_template_id: '',
                specimen_type_id: '',
                id: '',
            }),
        }
    },
    emits:['refreshServiceDetail'],
    methods: {
        closeModals(){
            $('#itemModal').modal('hide'); 
        },
        createService(){
            this.loading = true;
            this.serviceForm.post('/api/emr/laboratory/services')
            .then(response => {
                this.$emit('refreshServiceForm');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Service form did not load successfully',});
            })
            .finally(()=> {
                this.loading = false;
            });
        },
        getAllInitials() {
            this.closeModals();
            this.$emit('refreshServiceDetail');
        },
        normalizeItem(rawItem = null, laboratoryService = null) {
            return {
                id: rawItem?.id || null,
                name: rawItem?.name || '',
                barcode: rawItem?.barcode || '',
                brand_id: rawItem?.brand_id || '',
                category_id: rawItem?.category_id || '',
                description: rawItem?.description || '',
                last_landing_cost: rawItem?.last_landing_cost || 0.00,
                status: rawItem?.status || 1,
                type_id: 2, // SERVICE
                service: {
                    service_type_id: 6, // LABORATORY
                    reference: {
                        bottle_type_id: laboratoryService?.bottle_type_id ?? rawItem?.service?.reference?.bottle_type_id ?? '',
                        specimen_type_id: laboratoryService?.specimen_type_id ?? rawItem?.service?.reference?.specimen_type_id ?? '',
                        result_template_id: laboratoryService?.result_template_id ?? rawItem?.service?.reference?.result_template_id ?? '',
                        category_id: laboratoryService?.category_id ?? rawItem?.service?.reference?.category_id ?? '',
                        service_id: laboratoryService?.service_id ??rawItem?.service?.reference?.service_id ?? '',
                    }
                }
            }
        },
        normalizeService(service){

        },
        refreshPage(response) {
            this.bottle_types = response.data.bottle_types;
            this.categories = response.data.categories;
            this.result_templates = response.data.result_templates;
            this.specimen_types = response.data.specimen_types;
        },
        updateItem(item){
            this.loading = true;
            this.edit_mode = true;
            this.item = this.normalizeItem(item, this.service);
            $('#itemModal').modal('show');
            this.loading = false;  
        },
        updateService(){
            this.request = request;
        },

    },
    mounted() {
        //this.getInitials();
    },
    props: {
        editMode: Boolean,
        service: Object,
    }
}
</script>