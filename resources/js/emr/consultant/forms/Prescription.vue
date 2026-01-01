<template>
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" @submit.prevent="addDrug()">
                        <div class="col-md-3">
                            <label>Drug</label>
                            <input type="text" v-model="itemForm.drug_name" class="form-control" id="drugName" name="drugName" placeholder="Drug Name" autocomplete="off" @keyup="searchDrugs" @focus="modal = true" />
                            <input type="hidden" v-model="itemForm.drug_id" name="drug_id" id="drug_id" autocomplete="off" />
                            <div class="row" style="z-index: 1070; position:absolute;">
                                <ul class="col-md-12 text-white" style="background: grey;">
                                    <li v-for="drug in drugs" @click="setDrug(drug)" class="border-bottom p-2" style="cursor: pointer">{{ drug.name }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Specific Drug</label>
                            <ModelListSelect :list="specific_drugs" v-model="itemForm.specific_drug_id" option-value="id" option-text="name" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Dose</label>
                            <input type="text" v-model="itemForm.dose" class="form-control" required id="dose" name="dose" placeholder="Drug Dose" autocomplete="off" />
                        </div>
                        <div class="col-md-3">
                            <label>Form</label>
                            <select v-model="itemForm.form" class="form-control" id="form" name="form" autocomplete="off" required>
                                <option value="">--Select Form--</option>
                                <option v-for="form in drugForms" :key="form.name" :value="form.name">{{form.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Duration (in days)</label>
                            <input type="text" required v-model="itemForm.duration" class="form-control" id="duration" name="duration" placeholder="Duration" autocomplete="off" />
                        </div>
                        <div class="col-md-3">
                            <label>Frequency</label>
                            <select v-model="itemForm.frequency" class="form-control" id="frequency" name="frequency" autocomplete="off" required>
                                <option value="">--Select Frequency--</option>
                                <option v-for="frequency in frequencies" :key="frequency.name" :value="frequency.name">{{frequency.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Route</label>
                            <select v-model="itemForm.route" class="form-control" id="route" name="route" autocomplete="off" required>
                                <option value="">--Select Route--</option>
                                <option v-for="route in routes" :key="route.id" :value="route.name">{{route.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Quantity</label>
                            <div class="form-control" v-html="prescriptionQuantity"></div>
                        </div>
                        <div class="col-md-12">
                            <label>Detail</label>
                            <textarea v-model="itemForm.detail" class="form-control" id="details" name="details" placeholder="Details" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-3">
                        <button class="mt-2 btn btn-sm btn-dark" type="submit" >Add</button></div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug</th>
                                <th>Specific Drug</th>
                                <th>Dose</th>
                                <th>Form</th>
                                <th>Quantity</th>
                                <th>Duration</th>
                                <th>Frequency</th>
                                <th>Route</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(drug, index) in prescriptionForm.drugs" :key="drug.id">
                                <td>{{ index |addOne }}</td>
                                <td>{{ drug.drug_name }}</td>
                                <td>{{ drug.specific_drug != null ? drug.specific_drug.name : '' }}</td>
                                <td>{{ drug.dose }}</td>
                                <td>{{ drug.form }}</td>
                                <td>{{ drug.quantity }}</td>
                                <td>{{ drug.duration }}</td>
                                <td>{{ drug.frequency }}</td>
                                <td>{{ drug.route }}</td>
                                <td><div class="btn-group"><button class="btn btn-sm btn-default" @click=removeDrug(drug)><i class="fa fa-trash"></i></button></div></td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="mt-2 btn btn-sm btn-dark" type="button" @click="savePrescription()">Save</button>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import _ from 'lodash'
import { MultiSelect } from 'vue-search-select'
import { ModelListSelect } from 'vue-search-select'

export default {
    components: {
        MultiSelect, ModelListSelect
    },
    computed:{
        prescriptionQuantity(){
            let duration = this.itemForm.duration;
            let dose = this.itemForm.dose;
            var freq;
            switch(this.itemForm.frequency) {
                case 'Daily':
                    freq = 1;
                    break;
                case 'Weekly':
                    freq = 1/7;
                    break;
                case 'Monthly':
                    freq = 1/30;
                    break;
                case 'Twice Daily (bd)':
                    freq = 2;
                    break;
                case 'Hourly':
                    freq = 24;
                    break;
                case 'Thrice Daily':
                    freq = 3;
                    break;
                case 'Every 6 hours':
                    freq = 4;
                    break;
                }
            return Number(duration * dose * freq);
        },
    },
    data() {
        return {
            doctors: [],
            doctor: {},
            drugName: '',
            drugId: '',
            drugs: '',
            specific_drugs: [],
            modal: false,
            drugForms: [],
            routes: [], 
            frequencies:[],
            itemForm: new Form({
                detail: '',
                dose: '',
                drug_id: '',
                drug: '',
                drug_name: '',
                duration: '',
                form: '',
                frequency: '',
                route: '',
                specific_drug: '',
                quantity: '',
            }),
            stringItem: '',
            prescriptionForm: new Form({
                id: '',
                consultation_id: '',
                visit_id: '',
                patient_id: '',
                drugs: [],
            }),
        }
    },
    methods: {
        addDrug(){
            this.updateSpecificDrug();
            let drug = {
                drug_id: this.itemForm.drug_id,
                drug_name: this.itemForm.drug_name,
                specific_drug: this.itemForm.specific_drug,
                detail: this.itemForm.detail,
                dose: this.itemForm.dose,
                duration: this.itemForm.duration,
                form: this.itemForm.form,
                form_id: this.itemForm.form_id,
                frequency: this.itemForm.frequency,
                route: this.itemForm.route,
                quantity: this.prescriptionQuantity,
            }; 
            this.prescriptionForm.drugs.push(drug);
            this.itemForm.reset();
        },
        createPrescription() {
            this.$Progress.start();
            this.prescriptionForm.post('/api/emr/hims/prescriptions')
            .then(response => {
                this.$Progress.finish();
                Swal.fire({ icon: 'success', title: 'The Contact details has been created', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
            });
        },
        getInitials(){
            axios.get('/api/emr/hims/prescriptions/initials')
            .then((response) => { 
                this.drugForms = response.data.drug_forms; 
                this.frequencies = response.data.frequencies;
                this.routes = response.data.drug_routes; 
                this.specific_drugs = response.data.specific_drugs;
            })
            .catch(() => { }); 
        },
        removeDrug(drug){this.prescriptionForm.drugs.pop(drug);},
        savePrescription(){

            this.prescriptionForm.post('/api/emr/hims/prescriptions')
            .then(response => {

            })
            .catch(()=>{

            });
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
            console.log(item);
            this.itemForm.specific_drug = item;
        },
        updatePrescription() {
            this.$Progress.start();
            this.prescriptionForm.put('/api/emr/hims/prescriptions/' + this.prescriptionForm.id)
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('pageReload');
                Swal.fire({ icon: 'success', title: 'The Contact details has been updated', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
            });
        },
    },
    mounted() {
        this.getInitials();
        Fire.$on('PrescriptionDataFill', details => {
            this.prescriptionForm.patient_id = details.patient.id;
            if (details.prescription.id != null){
                this.prescriptionForm.id = details.prescription.id;
                this.prescriptionForm.doctor_id = details.prescription.doctor_id;
                this.prescriptionForm.doctor_name = details.prescription.doctor_name;
                this.prescriptionForm.start_date = details.prescription.start_date;
                this.prescriptionForm.end_date = details.prescription.end_date;
                this.prescriptionForm.drugs = details.prescription.drugs;
            }
            else{
                this.prescriptionForm.id = '';
                this.prescriptionForm.doctor_id = '';
                this.prescriptionForm.doctor_name = '';
                this.prescriptionForm.start_date = '';
                this.prescriptionForm.end_date = '';
                this.prescriptionForm.drugs = [];
            }
            alert(this.prescriptionForm.drugs)
        });
    },
    props: {
        'appointment': Object,
        'consultant': Object,
        'contact': Object,
        'editMode': Boolean,
    },
}
</script>