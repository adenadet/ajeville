<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-6">
            <label>Prescription Type</label>
            <select class="form-control" v-model="form.type">
                <option>Regular</option>
                <option>Controlled</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Duration (Days)</label>
            <input type="number" class="form-control" v-model="form.duration">
        </div>
    </div>
    <div class="mt-2">
        <label>Clinical Note</label>
        <textarea class="form-control" v-model="form.note"></textarea>
    </div>
</section>
</template>
<script>
export default {
    computed: {
        form: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: [Object, Array],
            default: () => ({}),
        },
    },    
}
</script>