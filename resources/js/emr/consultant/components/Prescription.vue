<!--template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-sm-12">
            <PrescriptionItemForm @add="addDrug"/>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug</th>
                                <th>Specific Drug</th>
                                <th>Dose</th>
                                <th>Total Quantity</th>
                                <th>Drug Form</th>
                                <th>Duration</th>
                                <th>Freq.</th>
                                <th>Route</th>
                                <th>Details</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(drug, index) in modelValue" :key="drug.id">
                                <td>{{ addOne(index)  }}</td>
                                <td>{{ drug.drug_name }}</td>
                                <td>{{ drug.specific_drug != null ? drug.specific_drug.name : '' }}</td>
                                <td>{{ drug.dose }}</td>
                                <td>{{ drug.total_quantity }}</td>
                                <td>{{ drug.drug_form }}</td>
                                <td>{{ drug.duration }}</td>
                                <td>{{ drug.frequency }}</td>
                                <td>{{ drug.route }}</td>
                                <td v-html="readMore(drug.detail, 25, '...')"></td>
                                <td><div class="btn-group"><button class="btn btn-sm btn-default" @click=removeDrug(index)><i class="fa fa-trash"></i></button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import PrescriptionItemForm from './PrescriptionItemForm.vue'
export default {
    components:{
        PrescriptionItemForm
    },
    computed: {
        model: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        },
    },
    data() {
        return {}
    },
    emits: ['update:modelValue'],
    methods:{
        addDrug(drug){
            console.log(drug);
            this.model.push(drug);
        },
        removeDrug(index){
            this.model.splice(index, 1);
        },
        searchDrugs() {
            axios.get('/api/emr/hims/drugs/search?q=' + this.itemForm.drug_name)
            .then((response) => { this.drugs = response.data.drugs; })
            .catch(() => { });
        },
        setDrug(drug) {
            this.itemForm.drug_name = drug.name;
            this.itemForm.drug_id = drug.id;
            this.drugs = [];
            this.modal = false;
            this.specific_drugs = drug.specific_drugs;
        },
        updateSpecificDrug(){
            var spec_id = this.itemForm.specific_drug_id
            var item = this.specific_drugs.find(item => item.id === spec_id);
            this.itemForm.specific_drug = item;
            this.itemForm.specific_drug_id = item ? item.id : null;
            this.itemForm.specific_drug_name = item ? item.name: null;
        },

    },
    props:{ 
        modelValue: {
            type: [Array],
            default: () => ({}),
        },
    },
}
</script-->
<template>
<section class="overlay-wrapper p-0">
    <PrescriptionItemForm @add="addDrug" />

    <div class="table-responsive mt-3">
        <table class="table table-striped text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Drug</th>
                    <th>Specific Drug</th>
                    <th>Dose</th>
                    <th>Total Qty</th>
                    <th>Form</th>
                    <th>Duration</th>
                    <th>Freq</th>
                    <th>Route</th>
                    <th>Detail</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(drug, index) in model" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ drug.drug_name }}</td>
                    <td>{{ drug.specific_drug?.name || '' }}</td>
                    <td>{{ drug.dose }}</td>
                    <td>{{ drug.total_quantity }}</td>
                    <td>{{ drug.drug_form }}</td>
                    <td>{{ drug.duration }}</td>
                    <td>{{ drug.frequency }}</td>
                    <td>{{ drug.route }}</td>
                    <td v-html="drug.detail"></td>
                    <td>
                        <button class="btn btn-sm btn-danger" @click="removeDrug(index)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>

<script>
import PrescriptionItemForm from './PrescriptionItemForm.vue';

export default {
    components: { PrescriptionItemForm },
    props: {
        modelValue: {
            type: Array,
            default: () => [],
        },
    },
    emits: ['update:modelValue'],
    computed: {
        model: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            },
        },
    },
    methods: {
        addDrug(drug) {
            this.model = [...this.model, drug];
        },
        removeDrug(index) {
            const items = [...this.model];
            items.splice(index, 1);
            this.model = items;
        },
    },
};
</script>
