<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-6">
            <label>Specialty </label>
            <select class="form-control" v-model="form.specialty_id">
                <option value="">--Select Specialty--</option>
                <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">{{ specialty.name }}</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Consultant </label>
            <select class="form-control" v-model="form.consultant_id">
                <option value="">--Select Consultant--</option>
                <option v-for="consultant in filtered_consultants" :key="consultant.id" :value="consultant.id">{{ consultant.name }}</option>
            </select>
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
    data() {
        return {
            specialties: [],
            consultants: [],
        }
    },
    emits: ['update:modelValue'],
    methods:{
        getAllInitials(){},
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