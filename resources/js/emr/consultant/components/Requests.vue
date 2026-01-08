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

/**
 * Lazy-loaded request modules
 * Heavy components should always be async in EMR systems
 */
const Prescription = defineAsyncComponent(() =>
    import('./Prescription.vue')
)
const Laboratory = defineAsyncComponent(() =>
    import('./Laboratory.vue')
)
const Radiology = defineAsyncComponent(() =>
    import('./Radiology.vue')
)
const Physiotherapy = defineAsyncComponent(() =>
    import('./Physiotherapy.vue')
)
const Dialysis = defineAsyncComponent(() =>
    import('./Dialysis.vue')
)
const Admission = defineAsyncComponent(() =>
    import('./Admission.vue')
)
const Referral = defineAsyncComponent(() =>
    import('./Referral.vue')
)

export default {
    name: 'Requests',

    props: {
        modelValue: {
            type: Object,
            required: true,
        },
    },

    emits: ['update:modelValue'],

    data() {
        return {
            active: 'prescription',

            /**
             * Tab registry
             * Components are explicitly marked as raw
             */
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

    computed: {
        /**
         * Currently active component
         */
        activeComponent() {
            return this.tabs.find(tab => tab.key === this.active)?.component || null
        },

        localValue() {
            return this.modelValue[this.active] ?? this.defaultValue(this.active)
        },
    },

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
}
</script>
