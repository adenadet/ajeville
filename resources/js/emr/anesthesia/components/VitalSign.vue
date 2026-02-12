<template>
<div class="row">
    <div class="col-md-2">
        <label class="form-label">Time</label>
        <input v-model="model.time" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Drug</label>
        <select v-model="model.drug_id" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in drugs" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Route</label>
        <select v-model="model.route_id" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in routes" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
    </div>
    <div class="col-md-1">
        <label class="form-label">Dose</label>
        <input v-model="model.dose" class="form-control">
    </div>
    <div class="col-md-1">
        <label class="form-label">Quantity</label>
        <input v-model="model.quantity" class="form-control">
    </div>
    <div class="col-md-2">
        <label class="form-label">Form</label>
        <select v-model="model.form_id" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in forms" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
    </div>
    <div class="col-md-12">
        <label>Remarks</label>
        <QuillEditor theme="snow" v-model:content="model.remarks" content-type="html" />
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
            drugs: [],
            forms: [],
            routes: [],
        }
    },
    emits: ['update:modelValue'],
    methods:{
        getInitials() {
            axios.get('/api/emr/anaesthesia/vital_sign/initials')
            .then(response => {
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vital Sign form did not load successfully',});
            });
        },
        refreshPage(response) {
            this.drugs = response.data.drugs;
            this.forms = response.data.forms;
            this.routes = response.data.routes;
        },
    },
    mounted(){
        this.getInitials();
    },
    props:{
        modelValue:{
            type: Object,
            default: () => ({})
        }
    },
}
</script>