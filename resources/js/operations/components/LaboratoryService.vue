<template>
<div class="border-top pt-3 mt-3">
    <div class="row">
        <div class="col-md-4">
            <label>Type</label>
            <select v-model="model.type" class="form-control">
                <option v-for="t in labTypes" :key="t" :value="t">{{ t }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Bottle Type</label>
            <select v-model="model.bottle_type" class="form-control">
                <option v-for="b in bottleTypes" :key="b" :value="b">{{ b }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Specimen Type</label>
            <select v-model="model.specimen_type" class="form-control">
                <option v-for="s in specimenTypes" :key="s" :value="s">{{ s }}</option>
            </select>
        </div>
    </div>
</div>
</template>
<script>
export default {
    props: ['modelValue'],
    emits: ['update:modelValue'],

    data() {
        return {
            types: ['Haemodialysis', 'Peritoneal Dialysis', 'Others']
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