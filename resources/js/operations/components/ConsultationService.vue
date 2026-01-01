<template>
    <div class="row">
        <div class="col-md-6">
            <label class="form-label">Specialty</label>
            <select v-model="model.specialty_id" class="form-control">
                <option value="">-- Select --</option>
                <option v-for="s in specialties" :key="s.id" :value="s.id">
                    {{ s.name }}
                </option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Consultant</label>
            <select v-model="model.consultant_id" class="form-control">
                <option value="">-- Optional --</option>
                <option v-for="c in consultants" :key="c.id" :value="c.id">
                    {{ c.name }}
                </option>
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
            specialties: [],
            consultants: []
        }
    },

    computed: {
        model: {
            get() {
                return this.modelValue
            },
            set(val) {
                this.$emit('update:modelValue', val)
            }
        }
    },

    mounted() {
        axios.get('/api/emr/specialties').then(r => this.specialties = r.data)
        axios.get('/api/emr/consultants').then(r => this.consultants = r.data)
    }
}
</script>
