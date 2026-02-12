<template>
    <div class="card">
        <div class="card-header bg-dark"><h4>Insurance</h4></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label>Provider Type</label>
                    <select class="form-control" v-model="insurance_type_id" @change="onTypeChange">
                        <option value="">Insurance Type</option>
                        <option v-for="t in providerTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Provider</label>
                    <select class="form-control" v-model="provider_id" @change="onProviderChange">
                        <option value="">Provider</option>
                        <option v-for="p in filteredProviders" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Plan</label>
                    <select class="form-control" v-model="plan">
                        <option value="">Plan</option>
                        <option v-for="p in filteredPlans" :key="p.id" :value="p">{{ p.name }}</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-sm btn-dark mt-2" type="button" @click="add">Add Insurance</button>

            <table class="table table-bordered mt-3" v-if="modelValue.length">
                <thead><tr><th>Plan</th><th>Enrollee ID</th><th>Expiry</th><th></th></tr></thead>
                <tbody>
                    <tr v-for="(i, idx) in modelValue" :key="idx">
                        <td>{{ i.name }}</td>
                        <td><input class="form-control" v-model="i.enrollee_id" /></td>
                        <td><input type="date" class="form-control" v-model="i.expiry_date" /></td>
                        <td><button class="btn btn-danger btn-xs" type="button" @click="remove(idx)"><i class="fa fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ['modelValue', 'providerTypes', 'providers', 'plans'],
    emits: ['update:modelValue'],
    data() {
        return { insurance_type_id: '', provider_id: '', plan: null }
    },
    computed: {
        filteredProviders() {
            const t = this.providerTypes.find(x => x.id === this.insurance_type_id)
            return t ? t.providers : []
        },
        filteredPlans() {
            const p = this.providers.find(x => x.id === this.provider_id)
            return p ? p.plans : []
        }
    },
    methods: {
        onTypeChange() { this.provider_id = ''; this.plan = null },
        onProviderChange() { this.plan = null },
        add() {
            if (!this.plan) return
            this.$emit('update:modelValue', [...this.modelValue, { ...this.plan, enrollee_id: '', expiry_date: '' }])
        },
        remove(i){
            const copy = [...this.modelValue]
            copy.splice(i, 1)
            this.$emit('update:modelValue', copy)
        }
    }
}
</script>