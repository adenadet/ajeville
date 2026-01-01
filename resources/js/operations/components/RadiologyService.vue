<template>
<div class="row">
    <div class="col-md-6">
        <label class="form-label">Type</label>
        <select v-model="model.type" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Body Part</label>
        <select v-model="model.body_part" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
        </select>
    </div>
</div>
</template>

<script>
export default {
    props:{
        modelValue:{
            type: Object,
            default: () => ({})
        }},
    emits: ['update:modelValue'],

    data() {
        return {
            types: ['CT-Scan', 'X-ray', 'Ultrasound', 'Others']
        }
    },

    computed: {
        model: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    }
}
</script>
