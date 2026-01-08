<template>
    <section class="container-fluid">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">New Consultation</h3>
            </div>
            <div class="card-body overlay-wrapper" v-if="!review">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card p-0">
                            <div class="card-header bg-dark">
                                <h3 class="card-title">Complaining Conditions</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;"
                                        v-if="!final_complaining_history">
                                        <select id="final_complaining" name="final_complaining" v-model="final_complaining"
                                            class="form-control float-right" @change="showStat()">
                                            <option value="assisted">Assisted</option>
                                            <option value="unassisted">Unassisted</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body row" v-if="final_complaining == 'assisted'">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Complaints</label>
                                        <multiselect v-model="complaintForm.symptoms" tag-placeholder="Add this as new tag"
                                            placeholder="Search or add a tag" label="name" track-by="code"
                                            :options="symptoms" :multiple="true" :taggable="true" @tag="addTag">
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Symptom</th>
                                                <th colspan="2">Duration</th>
                                                <th>Intensity <br />(On a scale of 1 - 10)</th>
                                                <th colspan="2">Experience Changes with time</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(symptom, index) in complaintForm.symptoms" :key="symptom.id">
                                                <td>{{ index | addOne }}</td>
                                                <td>{{ symptom.name }}</td>
                                                <td><input type="number" class="form-control" id="complaintForm_number"
                                                        name="complaintForm_number"
                                                        v-model="complaintForm.symptoms[index].number" /></td>
                                                <td>
                                                    <select class="form-control" id="complaintForm_duration"
                                                        name="complaintForm_duration"
                                                        v-model="complaintForm.symptoms[index].duration">
                                                        <option value="">--Duration --</option>
                                                        <option v-for="duration in durations" :key="duration.id"
                                                            :value="duration.name">{{ duration.name }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" id="complaintForm_number"
                                                        name="complaintForm_number"
                                                        v-model="complaintForm.symptoms[index].pain_level" />
                                                </td>
                                                <td>
                                                    <select class="form-control" id="complaintForm_experience_changes"
                                                        name="complaintForm_experience_changes"
                                                        v-model="complaintForm.symptoms[index].experience_changes">
                                                        <option value="">--Changes--</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                        v-model="complaintForm.symptoms[index].experience_change_character"
                                                        :disabled="complaintForm.symptoms[index].experience_changes != 'yes'"></textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button class="btn btn-success btn-sm float-right" @click="generateComplaintNote()"
                                        :disabled="complaintForm.symptoms.length == 0">Generate</button>
                                </div>
                            </div>
                            <div class="card-body p-0" v-else>
                                <div class="row p-0">
                                    <div class="col-md-12">
                                        <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="consultationForm.complaint" name="complaint" id="complaint" :class="{'is-invalid' : consultationForm.errors.has('description') }"></QuillEditor>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card p-0">
                            <div class="card-header bg-dark">
                                <h3 class="card-title">History</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <select href="#" class="text-dark ml-auto mb-3 mb-sm-0 form-control"
                                            name="history_type" id="history_type" v-model="history_type"
                                            v-show="!(final_complaining_history)">
                                            <option value="assisted">Assisted</option>
                                            <option value="unassisted">Unassisted</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" v-if="history_type == 'assisted'">
                                <EMRConsultantFormHistory :patient="patient" :consultation="consultation"
                                    :visit="consultation.visit" />
                            </div>
                            <div class="card-body p-0" v-else>
                                <div class="row p-0">
                                    <div class="col-md-12">
                                        <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="consultationForm.history" name="history" id="history" :class="{'is-invalid' : consultationForm.errors.has('history') }"></QuillEditor>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Initial Diagnosis</label>
                        <multiselect v-model="consultationForm.initial_icd_10" tag-placeholder="Add this as new tag"
                            placeholder="Search or add a tag" label="name" track-by="name" :options="icd_10_codes"
                            :multiple="true" :taggable="true" @tag="addTag" />
                    </div>
                    <div class="col-md-6">
                        <label>Final Diagnosis</label>
                        <multiselect v-model="consultationForm.final_icd_10" tag-placeholder="Add this as new tag"
                            placeholder="Search or add a tag" label="name" track-by="name" :options="icd_10_codes"
                            :multiple="true" :taggable="true" @tag="addTag" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Plan</label>
                        <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="consultationForm.action_plan" name="action_plan" id="action_plan" :class="{'is-invalid' : consultationForm.errors.has('action_plan') }"></QuillEditor>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card p-0">
                            <div class="card-header bg-dark">Further Actions</div>
                            <div class="card-body row">
                                <div class="col-5 col-sm-3">
                                    <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                                        <a class="nav-link active" id="prescription-tab" data-toggle="pill" href="#prescription" role="tab" aria-controls="prescription" aria-selected="true">Prescription</a>
                                        <a class="nav-link" id="lab-tab" data-toggle="pill" href="#lab" role="tab" aria-controls="lab" aria-selected="false">Lab Investigations</a>
                                        <a class="nav-link" id="radiology-tab" data-toggle="pill" href="#radiology" role="tab" aria-controls="radiology" aria-selected="false">Radiology</a>
                                        <a class="nav-link" id="physiotherapy-tab" data-toggle="pill" href="#physiotherapy" role="tab" aria-controls="physiotherapy" aria-selected="false">Physiotherapy</a>
                                        <a class="nav-link" id="admission-tab" data-toggle="pill" href="#admission" role="tab" aria-controls="admission" aria-selected="true">Admission</a>
                                    </div>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="tab-content" id="vert-tabs-tab">
                                        <div class="tab-pane text-left fade show active" id="prescription" role="tabpanel" aria-labelledby="prescription-tab">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <form class="row" @submit.prevent="addDrug()">
                                                                    <div class="col-md-4">
                                                                        <label>Drug</label>
                                                                        <input type="text" v-model="itemForm.drug_name" class="form-control" id="drugName" name="drugName" placeholder="Drug Name" autocomplete="off" @keyup="searchDrugs" @focus="modal = true" />
                                                                        <input type="hidden" v-model="itemForm.drug_id" name="drug_id" id="drug_id" autocomplete="off" />
                                                                        <div class="row" style="z-index: 1070; position:absolute;">
                                                                            <ul class="col-md-12 text-white" style="background: grey;">
                                                                                <li v-for="drug in drugs" @click="setDrug(drug)" class="border-bottom p-2" style="cursor: pointer" :key="drug.id">{{ drug.name }}</li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label>Specific Drug</label>
                                                                        <ModelListSelect :list="specific_drugs" v-model="itemForm.specific_drug_id" option-value="id" option-text="name" required/>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label>Quantity per Use</label>
                                                                        <input type="text" v-model="itemForm.dose" class="form-control" required id="dose" name="dose" placeholder="Drug Dose" autocomplete="off" />
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
                                                                        <label>Total Quantity</label>
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
                                                                            <th>Qty per Use</th>
                                                                            <th>Quantity</th>
                                                                            <th>Duration</th>
                                                                            <th>Frequency</th>
                                                                            <th>Route</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-for="(drug, index) in consultationForm.prescriptions" :key="drug.id">
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="lab" role="tabpanel" aria-labelledby="lab-tab">
                                            <section class="container-fluid border-1">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Laboratory Service</label>
                                                                <model-list-select class="form-control"
                                                                    :list="laboratory_services"
                                                                    v-model="laboratory_investigation" option-value="id"
                                                                    option-text="name" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="text-white">Add</label><br />
                                                            <button class="btn btn-success btn-sm" type="button"
                                                                @click="addLaboratoryItem()"
                                                                :disabled="laboratory_investigation == ''">Add</button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Name </th>
                                                                        <th>Quantity </th>
                                                                        <th>Description</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr v-for="(item, index) in consultationForm.laboratory"
                                                                        :key="item.id">
                                                                        <td>{{ item.name }}</td>
                                                                        <td><input class="form-control" type="number" v-model="consultationForm.laboratory[index].quantity" /></td>
                                                                        <td><textarea class="form-control" v-model="consultationForm.laboratory[index].description"></textarea></td>
                                                                        <td><button class="btn btn-xs btn-danger" type="button" @click="removeLaboratoryItem(index)"><i class="fa fa-trash"></i></button> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="tab-pane fade" id="radiology" role="tabpanel" aria-labelledby="radiology-tab">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Radiology Service</label>
                                                        <model-list-select class="form-control" :list="radiology_services" v-model="radiology_investigation" option-value="id" option-text="name" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-white">Add</label><br />
                                                    <button class="btn btn-success btn-sm" type="button" @click="addRadiologyItem()" :disabled="radiology_investigation == ''">Add</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Name </th>
                                                                <th>Quantity </th>
                                                                <th>Description</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in consultationForm.radiology" :key="item.id">
                                                                <td>{{ item.name}}</td>
                                                                <td><input class="form-control" type="number" v-model="consultationForm.radiology[index].quantity"/></td>
                                                                <td><textarea class="form-control"  v-model="consultationForm.radiology[index].description"></textarea></td>
                                                                <td><button class="btn btn-xs btn-danger" type="button" @click="removeItem(index)"><i class="fa fa-trash"></i></button> </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="physiotherapy" role="tabpanel" aria-labelledby="physiotherapy-tab">
                                        </div>
                                        <div class="tab-pane fade" id="admission" role="tabpanel" aria-labelledby="admission-tab">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-dark" @click="reviewConsultation()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="review">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <div class="card">
                    <div class="card-header bg-dark">
                        <button class="btn btn-default" @click="returnConsultation"><i class="fa fa-arrow-left mr-1"></i> Back</button>
                    </div>
                    <div class="card-body"><EMRConsultantDetailReview :consultationForm="consultationForm" /></div>
                    <div class="card-footer">
                        <div class="text-right">
                            <button class="btn btn-default" @click="returnConsultation()"><i class="fa fa-arrow-left mr-1"></i> Back</button>
                            <button class="btn btn-default" @click="createConsultation()"> Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
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
            consultation: {},
            
            consultationForm: new Form({
                complaint: '',
                history: '',
                action_plan: '',
                initial_icd_10: [],
                final_icd_10: [],
                laboratory: [],
                radiology: [],
                prescriptions: [],
                id: '',
            }),
            durations: [],
            drugs: [],
            drugName: '',
            drugId: '',
            drugs: '',
            specific_drugs: [],
            modal: false,
            drugForms: [],
            loading: false,
            routes: [], 
            frequencies:[],
            final_complaining: "assisted",
            final_complaining_history: false,
            final_history: false,
            history_type: 'assisted',
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
                specific_drug_id: '',
                specific_drug_name: '',
                quantity: '',
            }),
            laboratory_investigation: '',
            laboratory_services: [],
            locations: [],
            modal: true,
            patient: {},
            radiology_investigation: '',
            radiology_services: [],
            serviceName: '',
            serviceId: '',
            services: [],
            serviceForms: [],
            socrates: {active: 'site',},
            specific_drugs: [],
            symptoms: [],
            icd_10_codes: [],
            itemForm: new Form({ description: '', detail: '', service_id: '', service_name: '', quantity: '', symptoms: [], }),
            investigationForm: new Form({ id: '', doctor_id: '', doctor_name: '', start_date: '', patient_id: '', services: [], }),
            review: false,
            symptoms: [],
            value: [],
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
            this.consultationForm.prescriptions.push(drug);
            this.itemForm.reset();
        },
        addTag(newTag) {
            const tag = {
                name: newTag,
                code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
            }
            this.options.push(tag)
            this.value.push(tag)
        },
        addLaboratoryItem() {
            var item = this.laboratory_services.find(item => item.id === this.laboratory_investigation);
            var index = this.consultationForm.laboratory.map(function (o) { return o.id; }).indexOf(this.laboratory_investigation);
            if (index < 0) {
                this.consultationForm.laboratory.push({ id: item.id, category_id: item.category_id, description: '', name: item.name, quantity: 1, service_id: item.service_id, })
            }
            else {
                this.consultationForm.laboratory[index].quantity++;
            }
            this.laboratory_investigation = '';
        },
        addRadiologyItem(){
            var item = this.radiology_services.find(item => item.id === this.radiology_investigation);
            var index = this.consultationForm.radiology.map(function(o) { return o.id; }).indexOf(this.investigation);
            if (index < 0){
                this.consultationForm.radiology.push({id: item.id, category_id:item.category_id, description: '', name: item.name, quantity: 1, service_id:item.service_id,})
            }
            else{
                this.consultationForm.radiology[index].quantity++;
            }
            this.radiology_investigation = '';
        },
        createConsultation() {
            this.consultationForm.id = this.$route.params.id;
            this.consultationForm.post('/api/emr/consultations/consultants')
            .then(response => {
                this.loading = false;
                this.$router.push('/emr/consultations/doctor_queue');
                this.$swal.fire({ icon: 'success', title: 'The Consultation has been saved', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
            });
        },
        generateComplaintNote() {
            var note = '<p>The patient presented complaining of: <ul>';
            var sub_note = '';
            for (let i = 0; i < this.complaintForm.symptoms.length; i++) {
                sub_note = '<li>' + this.complaintForm.symptoms[i].name;
                if (this.complaintForm.symptoms[i].duration != null) {
                    sub_note += ' for ' + this.complaintForm.symptoms[i].number + ' ' + this.complaintForm.symptoms[i].duration + '.';
                }
                if (this.complaintForm.symptoms[i].pain_level != null) {
                    sub_note += ' Patient has a pain level of ' + this.complaintForm.symptoms[i].pain_level + ' on a scale of 1 - 10.';
                }
                if (this.complaintForm.symptoms[i].experience_changes != null) {
                    if (this.complaintForm.symptoms[i].experience_changes == 'yes') {
                        sub_note += ' Patient experiences changes best described as ' + this.complaintForm.symptoms[i].experience_change_character + '.';
                    }
                    else {
                        sub_note += ' Patient experienced no changes overtime.';
                    }
                }
                sub_note = sub_note + '</li>';
                note = note + sub_note;
            }
            note = note + '</ul></p>'
            this.consultationForm.complaint = note;
            this.final_complaining_history = true;
            this.final_complaining = "unassisted";
        },
        getInitials() {
            axios.get('/api/emr/consultations/consultants/' + this.$route.params.id)
            .then((response) => {
                //this.consultation = response.data.consultation;
                this.laboratory_services = response.data.laboratory_services;
                this.radiology_services = response.data.radiology_services;
                this.drugForms = response.data.drug_forms; 
                this.frequencies = response.data.frequencies;
                this.routes = response.data.routes; 
                this.specific_drugs = response.data.specific_drugs;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Consultation Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Consultation Form was not loaded successfully',
                })
            });
        },
        limitText(count) { return `and ${count} other symptoms` },
        changeSocrates(id) {
            this.socrates.active = id;
        },
        refreshPage(response) {
            this.durations = response.data.durations;
            this.frequencies = response.data.frequencies;
            this.icd_10_codes = response.data.icd_10_codes;
            this.locations = response.data.locations;
            this.positions = response.data.positions;
            this.routes = response.data.routes;
            this.symptoms = response.data.symptoms;
        },
        removeDrug(drug){this.consultationForm.prescriptions.pop(drug);},
        removeLaboratoryItem(index) {
            this.consultationForm.laboratory.splice(index, 1);
        },
        removeService(service) { this.investigationForm.services.pop(service); },
        reviewConsultation() {this.review = true;},
        returnConsultation(){this.review = false;},
        searchServices() {
            axios.get('/api/emr/hims/services/search?q=' + this.itemForm.service_name)
            .then((response) => { this.drugs = response.data.services; })
            .catch(() => { });
        },
        submitSymptoms() {
            this.itemForm.symptoms = this.value;
            this.value = [];
        },
        setService(drug) {
            this.itemForm.service_name = service.name;
            this.itemForm.service_id = service.id;
            this.services = [];
            this.modal = false;
        },
        updateInvestigation() {
            this.$Progress.start();
            this.investigationForm.put('/api/emr/hims/consultations/' + this.investigationForm.id)
            .then(response => {
                this.loading = false;
                Fire.$emit('pageReload');
                this.$swal.fire({ icon: 'success', title: 'The Investigation has been updated', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
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
            this.itemForm.specific_drug_id = item ? item.id : null;
            this.itemForm.specific_drug_name = item ? item.name: null;
        },

    },
    mounted() {
        this.getInitials();
    },
    props: {
        'appointment': Object,
        'consultant': Object,
        'contact': Object,
        'editMode': Boolean,
        'visit': Object,
    },
}
</script>