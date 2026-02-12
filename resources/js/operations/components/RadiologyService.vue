<template>
<div class="row">
    <div class="col-md-6">
        <label class="form-label">Type</label>
        <select v-model="model.investigation_type_id" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in radiology_types" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Body Part</label>
        <select v-model="model.location_id" class="form-control">
            <option value="">-- Select --</option>
            <option v-for="t in locations" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
    </div>
</div>
</template>

<script>
export default {
    emits: ['update:modelValue'],

    data() {
        return {
            locations: [],
            radiology_types: [],
        }
    },

    computed: {
        model: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },
    methods:{
        getInitials() {
            axios.get('/api/emr/radiology/services/initials')
            .then(response => {
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Service form did not load successfully',});
            });
        },
        refreshPage(response) {
            this.locations          = response.data.locations;
            this.radiology_types    = response.data.radiology_types;
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
