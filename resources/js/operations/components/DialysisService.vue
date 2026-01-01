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
        <label class="form-label">Sessions</label>
        <input v-model="model.sessions" class="form-control" type="number" min=1>
    </div>
</div>
</template>

<script>
export default {
    computed: {
        model: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },    
    data() {
        return {
            types: ['Haemodialysis', 'Peritoneal Dialysis', 'Others']
        }
    },
    emits: ['update:modelValue'],
    mounted(){},
    props: ['modelValue'],
}
</script>
