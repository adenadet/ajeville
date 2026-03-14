<template>
    <div class="card mt-3">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title">Further Actions</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                    <select type="text" name="table_search" class="form-control float-right" placeholder="Search">
                        <option value="">--Select Request Templates--</option>
                        <option v-for="template in templates" :value="template.id">{{ template.name }}</option>
                    </select>
                    <div class="input-group-append">
                        <button type="button" title="Apply" class="btn btn-default" @click="applyTemplate($event.target.value)"><i class="fas fa-search"></i> Apply</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-3">
                <ul class="nav nav-pills flex-column">
                    <li v-for="tab in tabs" :key="tab.key" class="nav-item">
                        <a href="#" class="nav-link" :class="{ active: active === tab.key }" @click.prevent="setActive(tab.key)">
                            {{ tab.label }}  <span v-if="hasData(tab.key)" class="badge bg-light text-dark float-end">✓</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <component v-if="activeComponent" :is="activeComponent" :model-value="localValue" @update:modelValue="updateActive" :key="active"/>
            </div>
        </div>
    </div>
</template>

<script>
import { markRaw, defineAsyncComponent } from 'vue'
const Admission = defineAsyncComponent(() =>import('./Admission.vue'))
const Dialysis = defineAsyncComponent(() =>import('./Dialysis.vue'))
const Laboratory = defineAsyncComponent(() =>import('./Laboratory.vue'))
const Physiotherapy = defineAsyncComponent(() =>import('./Physiotherapy.vue'))
const Prescription = defineAsyncComponent(() =>import('./Prescription.vue'))
const Radiology = defineAsyncComponent(() =>import('./Radiology.vue'))
const Referral = defineAsyncComponent(() =>import('./Referral.vue'))

export default {
    name: 'Requests',

    computed: {
        activeComponent() {
            return this.tabs.find(tab => tab.key === this.active)?.component || null
        },
        localValue() {
            if (!(this.active in this.modelValue)) {
                this.$emit('update:modelValue', {
                    ...this.modelValue,
                    [this.active]: this.defaultValue(this.active),
                });
            }
            return this.modelValue[this.active];
        },
    },    
    data() {
        return {
            active: 'prescription',
            tabs: [
                { key: 'prescription', label: 'Prescription', component: markRaw(Prescription) },
                { key: 'laboratory', label: 'Laboratory', component: markRaw(Laboratory) },
                { key: 'radiology', label: 'Radiology', component: markRaw(Radiology) },
                { key: 'physiotherapy', label: 'Physiotherapy', component: markRaw(Physiotherapy) },
                { key: 'dialysis', label: 'Dialysis', component: markRaw(Dialysis) },
                { key: 'admission', label: 'Admission', component: markRaw(Admission) },
                { key: 'referral', label: 'Referral', component: markRaw(Referral) },
            ],
            templates:[],
        }
    },
    emits: ['update:modelValue'],
    methods: {
        applyTemplate(templateId) {
            if (!templateId) return;
            const template = this.templates.find(t => t.id == templateId);
            if (!template) return;
            const confirmReplace = confirm("Replace existing items or merge?");
            let newValue;
            if (confirmReplace) {newValue = template.payload;} 
            else {
                newValue = [
                    ...(this.modelValue[this.active] || []),
                    ...template.payload,
                ];
            }
            this.$emit('update:modelValue', {...this.modelValue, [this.active]: newValue,});
        },
        defaultValue(key) {
            const defaults = {
                prescription: [],
                laboratory: [],
                radiology: [],
                physiotherapy: [],
                dialysis: {},
                admission: null,
                referral: null,
            }
            return defaults[key]
        },
        hasData(key) {
            const val = this.modelValue[key]
            if (Array.isArray(val)) return val.length > 0
            if (val && typeof val === 'object') return Object.keys(val).length > 0
            return false
        },
        setActive(key) {
            this.active = key
        },
        updateActive(payload) {
            this.$emit('update:modelValue', {
                ...this.modelValue,
                [this.active]: payload,
            })
        },
    },
    mounted() {
        axios.get('/api/emr/consultants/request_templates?type=mine')
        .then(res => {
            this.templates = res.data.templates;
        });
    },
    props: {modelValue: {type: Object, required: true,},},
}
</script>
