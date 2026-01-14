<template>
    <div class="row">
        <div class="col-md-12">
            <label>{{ firstUp(type) }} Diagnosis</label>
            <multiselect v-model="form" tag-placeholder="Add this as new tag"
                placeholder="Search or add a tag" track-by="icd10_code" label="icd10_3_code_description" :options="icd_10_codes"
                :multiple="true" :taggable="true" @tag="addTag" />
        </div>
    </div>
</template>
<script>
export default {
    computed: {
        form: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },
    data() {
        return {
            
        }
    },
    emits: ['update:modelValue'],
    methods: {
        addTag(newTag) {
            const tag = {
                name: newTag,
                code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
            }
            this.options.push(tag)
            this.value.push(tag)
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
    },
    props: {
        icd_10_codes: Array,
        modelValue: {
            type: [Object, Array],
            default: () => ({}),
        },
        type: String,
    },
}
</script>
