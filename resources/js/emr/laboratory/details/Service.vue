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
                    <InventoryFormItem :editMode.sync="editMode" :item.sync="item" @itemReload="getAllInitials()"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="serviceFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">{{editMode ? 'Edit ' : ''}} Laboratory Service</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormService :editMode.sync="editMode" :service.sync="service"  @refreshServiceForm="refreshPage"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Service Detail</h3>
            <div class="card-tools">
                <button class="btn btn-default btn-xs" @click="updateService" type="button"><i class="fa fa-edit"></i></button>
            </div>
        </div>
        <div class="card-body">
            <h3 class="text-primary"><i class="fas fa-flask"></i> {{ service.service != null && service.service.item != null ? service.service.item.name : 'Name not sorted' }}</h3>
            <p class="text-muted" v-html="service.service != null ? service.service.description : ''"></p>
            <br>
            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Category</b> <a class="float-right">{{ service.category != null ? service.category.name : '' }}</a></li>
                <li class="list-group-item"><b>Item</b> 
                    <a class="float-right" v-if="service.service != null && service.service.item != null">{{  service.service.item.name }} <button class="btn btn-xs btn-primary" @click="updateItem(service.service.item)"><i class="fa fa-edit"></i></button></a>
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
import EMRLaboratoryFormService from '@/emr/laboratory/forms/Service.vue';
import InventoryFormItem from '@/inventory/forms/Item.vue';
export default {
    components:{
        EMRLaboratoryFormService, InventoryFormItem
    },
    data() {
        return {
            bottle_types: [],
            categories: [],
            editMode: true,
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
            $('#serviceFormModal').modal('hide'); 
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
        refreshPage(){
            this.closeModals();
            this.$emit('refreshServiceDetail')
        },
        updateItem(item){
            this.loading = true;
            this.edit_mode = true;
            this.item = this.normalizeItem(item, this.service);
            $('#itemModal').modal('show');
            this.loading = false;  
        },
        updateService(){
            this.loading = true;
            this.editMode = true;
            alert(this.editMode ? 'Going fine': 'Something is wrong');
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {},
    props: {
        service: Object,
    }
}
</script>