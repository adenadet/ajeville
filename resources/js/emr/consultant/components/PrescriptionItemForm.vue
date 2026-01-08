<!--template>
<form class="row" @submit.prevent="submit">
    <div class="col-md-4">
        <label>Drug</label>
        <input type="text" v-model="form.drug_name" class="form-control" placeholder="Drug Name" @keyup="searchDrugs"/>
        <input type="hidden" v-model="form.drug_id" />

        <ul v-if="drugs.length" class="list-group position-absolute w-100" style="z-index:1050">
            <li v-for="drug in drugs" :key="drug.id" class="list-group-item list-group-item-action"@click="selectDrug(drug)">{{ drug.name }}</li>
        </ul>
    </div>

    <div class="col-md-4">
        <label>Specific Drug</label>
        <ModelListSelect :list="specific_drugs" v-model="form.specific_drug_id" option-value="id" option-text="name" required/>
    </div>

    <div class="col-md-2">
        <label>Quantity per Use</label>
        <input type="number" v-model.number="form.dose" class="form-control" required />
    </div>
    <div class="col-md-2">
        <label>Form</label>
        <select v-model.number="form.drug_form" class="form-control" required>
            <option value="">--Select Form--</option>
            <option v-for="drug_form in drug_forms" :value="drug_form.name">{{ drug_form.name }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label>Duration (days)</label>
        <input type="number" v-model.number="form.duration" class="form-control" required />
    </div>
    <div class="col-md-3">
        <label>Frequency</label>
        <select v-model="form.frequency" class="form-control" required>
            <option value="">-- Select --</option>
            <option v-for="f in frequencies" :key="f.code" :value="f.code">
                {{ f.name }}
            </option>
        </select>
    </div>
    <div class="col-md-3">
        <label>Route</label>
        <select v-model="form.route" class="form-control" required>
            <option value="">-- Select --</option>
            <option v-for="r in routes" :key="r.id" :value="r.name">
                {{ r.name }}
            </option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Total Quantity</label>
        <div class="form-control">{{ totalQuantity }}</div>
    </div>

    <div class="col-md-12">
        <label>Detail</label>
        <textarea v-model="form.detail" class="form-control"></textarea>
    </div>

    <div class="col-md-3 mt-2">
        <button class="btn btn-sm btn-dark">Add</button>
    </div>
</form>
</template>

<script>
export default {
    emits: ['add'],
    data() {
        return {
            drugs: [],
            drug_forms: [],
            specific_drugs: [],
            form: {
                detail: '',
                drug_id: null,
                drug_form: '',
                drug_name: '',
                specific_drug_id: null,
                specific_drug: null,
                dose: 0,
                duration: 0,
                frequency: '',
                route: '',
                total_quantity: 0,
            },
            frequencies: [],
            routes: [],
        };
    },
    computed: {
        selectedFrequency() {
            if (this.frequencies == null){
                return null;
            }
            else{
            return this.frequencies.find(f => f.code === this.form.frequency);}
        },
        totalQuantity() {
            const freq = this.selectedFrequency?.per_day || 0;
            return this.form.dose * this.form.duration * freq;
        },
    },
    created(){
        this.getInitials();
    },
    methods: {
        getInitials(){
            axios.get('/api/emr/hims/drugs/initials')
            .then((response) => {
                this.drug_forms = response.data.drug_forms;
                this.frequencies = response.data.frequencies;
                this.routes = response.data.routes;
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Consultation Form was not loaded successfully',
                })
            });
        },
        searchDrugs() {
            if (!this.form.drug_name) return;
            axios.get('/api/emr/hims/drugs/search?q=' + this.form.drug_name).then(res => this.drugs = res.data.drugs);
        },
        selectDrug(drug) {
            this.form.drug_id = drug.id;
            this.form.drug_name = drug.name;
            this.specific_drugs = drug.specific_drugs || [];
            this.drugs = [];
        },
        submit() {
            const specific = this.specific_drugs.find(
                s => s.id === this.form.specific_drug_id
            );
            this.form.total_quantity = this.totalQuantity;
            this.$emit('add', this.form);

            this.reset();
        },
        reset() {
            this.form = {
                drug_id: null,
                drug_name: '',
                specific_drug_id: null,
                specific_drug: null,
                dose: 0,
                duration: 0,
                frequency: '',
                route: '',
                detail: '',
            };
            this.specific_drugs = [];
        },
    },
};
</script-->
<template>
<form class="row" @submit.prevent="submit">
    <div class="col-md-4">
        <label>Drug</label>
        <input
            type="text"
            v-model="form.drug_name"
            class="form-control"
            placeholder="Drug Name"
            @keyup="searchDrugs"
        />

        <ul v-if="drugs.length" class="list-group position-absolute w-100" style="z-index:1050">
            <li
                v-for="drug in drugs"
                :key="drug.id"
                class="list-group-item list-group-item-action"
                @click="selectDrug(drug)"
            >
                {{ drug.name }}
            </li>
        </ul>
    </div>

    <div class="col-md-4">
        <label>Specific Drug</label>
        <ModelListSelect
            :list="specific_drugs"
            v-model="form.specific_drug_id"
            option-value="id"
            option-text="name"
            required
        />
    </div>

    <div class="col-md-2">
        <label>Dose</label>
        <input type="number" v-model.number="form.dose" class="form-control" required />
    </div>

    <div class="col-md-2">
        <label>Form</label>
        <select v-model="form.drug_form" class="form-control" required>
            <option value="">-- Select --</option>
            <option v-for="f in drug_forms" :key="f.id" :value="f.name">
                {{ f.name }}
            </option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Duration (days)</label>
        <input type="number" v-model.number="form.duration" class="form-control" required />
    </div>

    <div class="col-md-3">
        <label>Frequency</label>
        <select v-model="form.frequency" class="form-control" required>
            <option value="">-- Select --</option>
            <option v-for="f in frequencies" :key="f.code" :value="f.code">
                {{ f.name }}
            </option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Route</label>
        <select v-model="form.route" class="form-control" required>
            <option value="">-- Select --</option>
            <option v-for="r in routes" :key="r.id" :value="r.name">
                {{ r.name }}
            </option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Total Quantity</label>
        <div class="form-control">{{ totalQuantity }}</div>
    </div>

    <div class="col-md-12">
        <label>Detail</label>
        <textarea v-model="form.detail" class="form-control"></textarea>
    </div>

    <div class="col-md-3 mt-2">
        <button class="btn btn-sm btn-dark">Add</button>
    </div>
</form>
</template>

<script>
export default {
    emits: ['add'],
    data() {
        return {
            drugs: [],
            specific_drugs: [],
            drug_forms: [],
            frequencies: [],
            routes: [],
            form: this.emptyForm(),
        }
    },
    computed: {
        selectedFrequency() {
            return this.frequencies.find(f => f.code === this.form.frequency) || null;
        },
        totalQuantity() {
            const perDay = this.selectedFrequency?.per_day || 0;
            return this.form.dose * this.form.duration * perDay;
        },
    },
    created() {
        this.loadInitials();
    },
    methods: {
        emptyForm() {
            return {
                drug_id: null,
                drug_name: '',
                specific_drug_id: null,
                specific_drug: null,
                drug_form: '',
                dose: 0,
                duration: 0,
                frequency: '',
                route: '',
                detail: '',
                total_quantity: 0,
            };
        },
        loadInitials() {
            axios.get('/api/emr/hims/drugs/initials').then(res => {
                this.drug_forms = res.data.drug_forms;
                this.frequencies = res.data.frequencies;
                this.routes = res.data.routes;
            });
        },
        searchDrugs() {
            if (!this.form.drug_name) return;
            axios.get('/api/emr/hims/drugs/search?q=' + this.form.drug_name)
                .then(res => this.drugs = res.data.drugs);
        },
        selectDrug(drug) {
            this.form.drug_id = drug.id;
            this.form.drug_name = drug.name;
            this.specific_drugs = drug.specific_drugs || [];
            this.drugs = [];
        },
        submit() {
            const specific = this.specific_drugs.find(s => s.id === this.form.specific_drug_id);

            const payload = {
                ...this.form,
                specific_drug: specific || null,
                total_quantity: this.totalQuantity,
            };

            this.$emit('add', payload);
            this.form = this.emptyForm();
            this.specific_drugs = [];
        },
    },
};
</script>
