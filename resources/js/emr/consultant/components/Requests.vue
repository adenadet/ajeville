<template>
    <div class="card mt-3">
        <div class="card-header bg-dark text-white">
            Further Actions
        </div>

        <div class="card-body row">
            <!-- Tabs -->
            <div class="col-md-3">
                <ul class="nav nav-pills flex-column">
                    <li v-for="tab in tabs" :key="tab.key" class="nav-item">
                        <a href="#" class="nav-link" :class="{ active: active === tab.key }" @click.prevent="setActive(tab.key)">
                            {{ tab.label }}
                            <span v-if="hasData(tab.key)" class="badge bg-light text-dark float-end">✓</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Active Request Component -->
            <div class="col-md-9">
                <component
                    v-if="activeComponent"
                    :is="activeComponent"
                    :model-value="localValue"
                    @update:modelValue="updateActive"
                    :key="active"
                />
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
        /**
         * Currently active component
         */
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
        }
    },
    emits: ['update:modelValue'],

    
    methods: {
        setActive(key) {
            this.active = key
        },

        updateActive(payload) {
            this.$emit('update:modelValue', {
                ...this.modelValue,
                [this.active]: payload,
            })
        },

        hasData(key) {
            const val = this.modelValue[key]
            if (Array.isArray(val)) return val.length > 0
            if (val && typeof val === 'object') return Object.keys(val).length > 0
            return false
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
    },
    props: {modelValue: {type: Object, required: true,},},
}
</script>
