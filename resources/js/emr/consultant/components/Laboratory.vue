<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Laboratory Service</label>
                <model-list-select class="form-control"
                    :list="laboratory_services"
                    v-model="laboratory_investigation" option-value="id"
                    option-text="name" />
            </div>
        </div>
        <div class="col-md-6">
            <label class="text-white">Add</label><br />
            <button class="btn btn-success btn-sm" type="button" @click="addLaboratoryItem()" :disabled="laboratory_investigation == ''">Add</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name </th>
                        <th>Category </th>
                        <th>Quantity </th>
                        <th>Emergency</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in modelValue"
                        :key="item.id">
                        <td>{{ item.name }}</td>
                        <td>{{ item.category != null ? item.category.name : 'Not applicable' }}</td>
                        <td><input class="form-control" type="number" v-model="modelValue[index].quantity" /></td>
                        <td>
                            <select class="form-control" type="number" v-model="modelValue[index].special">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </td>
                        <td><textarea class="form-control" v-model="modelValue[index].description"></textarea></td>
                        <td><button class="btn btn-xs btn-danger" type="button" @click="removeLaboratoryItem(index)"><i class="fa fa-trash"></i></button> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
import { get } from 'lodash';

export default {
    computed: {
        form: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },
    data() {
        return {
            laboratory_services: [],
            laboratory_investigation: '',
        }
    },
    emits: ['update:modelValue'],
    methods:{
        addLaboratoryItem() {
            var item = this.laboratory_services.find(item => item.id === this.laboratory_investigation);
            var index = this.modelValue.map(function (o) { return o.id; }).indexOf(this.laboratory_investigation);
            if (index < 0) {
                this.modelValue.push({ id: item.id, category_id: item.category_id, description: '', name: item.name, quantity: 1, service_id: item.service_id, })
            }
            else {
                this.modelValue[index].quantity++;
            }
            this.laboratory_investigation = '';
        },
        getAllInitials() {
            axios.get('/emr/consultant/laboratory-initials')
                .then(response => {
                    this.laboratory_services = get(response.data, 'laboratory_services', []);
                })
                .catch(error => {
                    console.error('There was an error fetching the laboratory services!', error);
                });
        },
        removeLaboratoryItem(index) {
            this.modelValue.splice(index, 1);
        },
    },
    mounted(){
        this.getAllInitials();
    },
    props: {
        modelValue: {
            type: [Object, Array],
            default: () => ({}),
        },
    },    
} 
</script>