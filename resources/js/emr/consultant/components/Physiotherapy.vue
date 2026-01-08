<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-6">
            <label>Physiotherapy Type</label>
            <select class="form-control" v-model="modelValue.type">
                <option>Haemodialysis</option>
                <option>Peritoneal Dialysis</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Number of Sessions</label>
            <input type="number" class="form-control" v-model="modelValue.sessions">
        </div>
    </div>
    <div class="mt-2">
        <label>Clinical Note</label>
        <textarea class="form-control" v-model="modelValue.note"></textarea>
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
            physiotherapy_types: [],
            loading: false,
        }
    },
    methods:{
        getAllInitials(){
            //Get the different types pf Physiotherapy officially
        },
    },
    mounted(){
        this.getAllInitials();
    },
    emits: ['update:modelValue'],
    props: ['modelValue'],
    
    
}
</script>
