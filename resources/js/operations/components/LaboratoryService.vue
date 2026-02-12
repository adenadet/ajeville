<template>
<div class="border-top pt-3 mt-3">
    <div class="row">
        <div class="col-md-6">
            <label>Category</label>
            <select v-model="model.category_id" class="form-control">
                <option v-for="t in categories" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Result Template</label>
            <select v-model="model.result_template_id" class="form-control">
                <option v-for="rt in result_templates" :key="rt.id" :value="rt.id">{{ rt.name }}</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Bottle Type</label>
            <select v-model="model.bottle_type_id" class="form-control">
                <option v-for="b in bottle_types" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Specimen Type</label>
            <select v-model="model.specimen_type_id" class="form-control">
                <option v-for="s in specimen_types" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
        </div>
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
    created(){
        this.getInitials();
    },
    data() {
        return {
            bottle_types: [],
            categories: [],
            result_templates: [],
            specimen_types: [],
            types: ['Haemodialysis', 'Peritoneal Dialysis', 'Others']
        }
    },
    emits: ['update:modelValue'],
    methods:{
        getInitials() {
            axios.get('/api/emr/laboratory/services/initials')
            .then(response => {
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Service form did not load successfully',});
            });
        },
        refreshPage(response) {
            this.bottle_types = response.data.bottle_types;
            this.categories = response.data.categories;
            this.result_templates = response.data.result_templates;
            this.specimen_types = response.data.specimen_types;
        },
    },
    props: ['modelValue'],
}
</script>