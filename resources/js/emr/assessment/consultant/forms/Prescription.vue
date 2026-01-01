<template>
<section>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <form id="prescription_form">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Doctor</label>
                                    <select type="text" class="form-control" id="doctor_id" name="doctor_id" required v-model="prescriptionForm.doctor_id" :class="{'is-invalid': prescriptionForm.errors.has('doctor_id')}">
                                        <option value="">---Select Doctor---</option>
                                        <option value="0">Unknown Doctor</option>
                                        <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">{{doctor.full_name}}</option>
                                    </select>
                                </div>
                                <div class="form-group" v-show="prescriptionForm.doctor_id == 0 && prescriptionForm.doctor_id != ''">
                                    <label>Doctor's Name</label>
                                    <input type="text" class="form-control" id="doctor_name" name="doctor_name"
                                        placeholder="Enter Address *" required v-model="prescriptionForm.doctor_name" :class="{'is-invalid': prescriptionForm.errors.has('doctor_name')}">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        placeholder="Enter Address *" required v-model="prescriptionForm.start_date" :class="{'is-invalid': prescriptionForm.errors.has('start_date')}">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        placeholder="Enter Address *" required v-model="prescriptionForm.end_date" :class="{'is-invalid': prescriptionForm.errors.has('end_date')}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <button @click.prevent="editMode ? updatePrescription() : createPrescription()" type="submit" name="submit" class="submit btn btn-success">Submit </button>
            </form>
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Drug</label>
                            <input type="text" v-model="itemForm.drug_name" class="form-control" id="drugName" name="drugName" placeholder="Drug Name" autocomplete="off" @keyup="searchDrugs" @focus="modal = true" />
                            <input type="hidden" v-model="itemForm.drug_id" name="drug_id" id="drug_id" autocomplete="off" />
                            <div class="row" v-if="drugs != null && modal == true" style="z-index: 1070; position:absolute;">
                                <ul class="col-md-10 offset-1 bg-gray  text-white">
                                    <li v-for="drug in drugs" @click="setDrug(drug)" class="border-bottom p-2" style="cursor: pointer">{{ drug.name }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Dose</label>
                            <input type="text" v-model="itemForm.dose" class="form-control" id="dose" name="dose" placeholder="Drug Dose" autocomplete="off"/>
                        </div>
                        <div class="col-md-3">
                            <label>Form</label>
                            <select v-model="itemForm.form" class="form-control" id="form" name="form" autocomplete="off">
                                <option value="">--Select Form--</option>
                                <option v-for="form in drugForms" :key="form.name" :value="form.name">{{form.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Duration</label>
                            <input type="text" v-model="itemForm.duration" class="form-control" id="duration" name="duration" placeholder="Duration" autocomplete="off" />
                        </div>
                        <div class="col-md-3">
                            <label>Frequency</label>
                            <select v-model="itemForm.frequency" class="form-control" id="frequency" name="frequency" autocomplete="off">
                                <option value="">--Select Frequency--</option>
                                <option v-for="frequency in frequencies" :key="frequency.name" :value="frequency.name">{{frequency.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Route</label>
                            <select v-model="itemForm.route" class="form-control" id="route" name="route" autocomplete="off">
                                <option value="">--Select Route--</option>
                                <option v-for="route in routes" :key="route.id" :value="route.id">{{route.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Quantity</label>
                            <input type="text" v-model="itemForm.quantity" class="form-control" id="quantity" name="quantity" placeholder="Quantity" autocomplete="off"/>
                        </div>
                        <div class="col-md-3">
                            <label>Detail</label>
                            <textarea v-model="itemForm.detail" class="form-control" id="details" name="details" placeholder="Details" autocomplete="off"/>
                        </div>
                    </div>
                    <button class="mt-5 btn btn-sm btn-primary" type="button" @click="addDrug()">Add</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug</th>
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
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            doctors: [],
            doctor: {},
            drugName: '',
            drugId: '',
            drugs: '',
            modal: false,
            drugForms: [],
            routes: [], 
            frequencies:[],
            itemForm: new Form({
                detail: '',
                dose: '',
                drug_id: '',
                drug_name: '',
                duration: '',
                form: '',
                frequency: '',
                route: '',
                quantity: '',
            }),
            prescriptionForm: new Form({
                id: '',
                doctor_id: '',
                doctor_name: '',
                start_date: '',
                end_date: '',
                patient_id: '',
                drugs: [],
            }),
        }
    },
    methods: {
        addDrug(){
            let drug = {
                drug_id: this.itemForm.drug_id,
                drug_name: this.itemForm.drug_name,
                detail: this.itemForm.detail,
                dose: this.itemForm.dose,
                duration: this.itemForm.duration,
                form: this.itemForm.form,
                form_id: this.itemForm.form_id,
                frequency: this.itemForm.frequency,
                route: this.itemForm.route,
                quantity: this.itemForm.quantity,
            }; 
            this.prescriptionForm.drugs.push(drug);
            this.itemForm.reset();
            console.log(this.prescriptionForm.drugs);
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
            })
            .catch(() => { }); 
        },
        removeDrug(drug){this.prescriptionForm.drugs.pop(drug);},
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