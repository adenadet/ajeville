<template>
<div class="row">
    <div class="col-md-6">
        <label class="form-label">Admission Type</label>
        <select v-model="model.type" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
        </select>
    </div>
</div>
</template>

<script>
export default {
    props: ['modelValue'],
    emits: ['update:modelValue'],

    data() {
        return {
            types: ['Accommodation', 'Meals', 'Sanitary', 'Others']
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
