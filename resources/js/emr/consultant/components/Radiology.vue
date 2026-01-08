<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Radiology Service</label>
                <model-list-select class="form-control" :list="radiology_services" v-model="radiology_investigation" option-value="id" option-text="name" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="text-white">Note</label>
                <QuillEditor v-model:content="note" theme="snow" content-type="html" class="form-control"/>
            </div>
        </div>
        <button class="btn btn-success btn-sm" type="button" @click="addRadiologyItem()" :disabled="radiology_investigation == ''">Add</button>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name </th>
                        <th>Quantity </th>
                        <th>Note</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in modelValue"
                        :key="item.id">
                        <td>{{ item.name }}</td>
                        <td><input class="form-control" type="number" v-model="modelValue[index].quantity" min="1"/></td>
                        <td><QuillEditor v-model:content="modelValue[index].note" theme="snow" content-type="html" class="form-control"/></td>
                        <td><button class="btn btn-xs btn-danger" type="button" @click="removeRadiologyItem(index)"><i class="fa fa-trash"></i></button> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    computed: {
        form: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },
    data() {
        return {
            radiology_services: [],
            radiology_investigation: '',
        }
    },
    emits: ['update:modelValue'],
    methods:{
        addRadiologyItem(){
            var item = this.radiology_services.find(item => item.id === this.radiology_investigation);
            var index = this.modelValue.map(function(o) { return o.id; }).indexOf(this.radiology_investigation);
            if (index < 0){
                this.modelValue.push({id: item.id, category_id:item.category_id, description: '', name: item.name, quantity: 1, service_id:item.service_id,})
            }
            else{
                this.modelValue[index].quantity++;
            }
            this.radiology_investigation = '';
        },
        removeLaboratoryItem(index) {
            this.modelValue.splice(index, 1);
        },
    },
    props: { 
        modelValue: {
            type: [Object, Array],
            default: () => ({}),
        },
    },    
} 
</script>