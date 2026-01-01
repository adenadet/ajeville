<template>
    <div class="container-fluid overlay-wrapper">
    <form>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label class="form-label">History</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="consultationForm.history" rows="3"></QuillEditor>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label class="form-label">Complaint</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="consultationForm.complaint" rows="3"></QuillEditor>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group mb-3">
                <label class="form-label">Action Plan</label>
                <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="consultationForm.action_plan" rows="3"></QuillEditor>
            </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group mb-3">
                    <label class="form-label">Initial Diagnosis</label>
                    <!--select multiple class="form-control" v-model="consultationForm.initial_diagnosis">
                        <option v-for="code in icd_10_codes" :key="diag" :value="code.id">{{ code.icd_10_3_code_description }}</option>
                    </select-->
                    <multiselect v-model="consultationForm.initial_icd_10" tag-placeholder="ICD 10 codes" placeholder="Select Diagnosis" label="icd10_3_code_description" track-by="icd10_code" :options="icd_10_codes" :multiple="true" :taggable="true" @tag="addTag" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group mb-3">
                    <label class="form-label">Final Diagnosis</label>
                    <multiselect v-model="consultationForm.final_icd_10" tag-placeholder="ICD 10 codes" placeholder="Select Diagnosis" label="icd10_3_code_description" track-by="icd10_code" :options="icd_10_codes" :multiple="true" :taggable="true" @tag="addTag" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-navy">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdditionals">
                            <h3 class="card-title">Additionals</h3>
                        </a>
                    </div>
                    <div id="collapseAdditionals" class="panel-collapse collapse in">
                        <div class="card-body row">
                            <div class="col-5 col-sm-3">
                                <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true">Dialysis</a>
                                    <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">Prescriptions</a>
                                    <a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">Laboratory </a>
                                    <a class="nav-link" id="vert-tabs-settings-tab" data-toggle="pill" href="#vert-tabs-settings" role="tab" aria-controls="vert-tabs-settings" aria-selected="false">Radiology</a>
                                </div>
                            </div>
                            <div class="col-7 col-sm-9">
                                <div class="tab-content" id="vert-tabs-tabContent">
                                    <div class="tab-pane text-left fade show active" id="vert-tabs-home" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
                                        
                                    </div>
                                    <div class="tab-pane fade" id="vert-tabs-messages" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Laboratory Requests</label>
                                                    <div class="input-group mb-2">
                                                        <select class="form-select" v-model="selectedLab">
                                                            <option value="">Select</option>
                                                            <option v-for="lab in lab_services" :key="lab.id" :value="lab">{{ lab.name }}</option>
                                                        </select>
                                                        <select class="form-select" v-model="selectedLabFreq">
                                                            <option disabled value="">Frequency</option>
                                                            <option>Daily</option>
                                                            <option>Twice Daily</option>
                                                            <option>Thrice Daily</option>
                                                        </select>
                                                        <button class="btn btn-primary" type="button" @click="addLab">Add</button>
                                                    </div>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr><th>Lab</th><th>Frequency</th><th>Action</th></tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(lab, index) in consultationForm.laboratory" :key="index">
                                                            <td>{{ lab.name }}</td>
                                                            <td>{{ lab.frequency }}</td>
                                                            <td><button class="btn btn-sm btn-danger" @click="removeLab(index)">Delete</button></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Prescriptions</label>
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-2">
                                                        <label class="form-label">Drug</label>
                                                        <select class="form-control" v-model="selectedDrug.drug">
                                                            <option value="">Drug</option>
                                                            <option v-for="drug in drugs" :key="drug.id" :value="drug">{{ drug.name }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Dose</label>
                                                        <input type="text" class="form-control" v-model="selectedDrug.dose" placeholder="Dose" />
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="form-label">Form</label>
                                                        <select class="form-control" v-model="selectedDrug.drug_form">
                                                            <option value="">Select</option>
                                                            <option v-for="form in drugForms" :key="form.id" :value="form">{{ form.name }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Frequency</label>
                                                        <select class="form-control" v-model="selectedDrug.frequency">
                                                            <option value="">Select</option>
                                                            <option v-for="freq in frequencies" :key="freq.id" :value="freq">{{ freq.name }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="form-label">Quantity</label>
                                                        <input type="number" class="form-control" v-model="selectedDrug.quantity" placeholder="Quantity" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Duration Type</label>
                                                        <select class="form-control" v-model="selectedDrug.duration_type" >
                                                            <option value="">Select</option>
                                                            <option v-for="duration in durations" :key="duration.id" :value="duration">{{ duration.name }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Duration</label>
                                                        <input type="text" class="form-control" v-model="selectedDrug.duration" placeholder="Duration" />
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                        <button type="button" class="mt-3 mb-3 btn btn-success w-100" @click="addPrescription">Add</button>
                                                    </div>
                                                </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Drug</th><th>Dose</th><th>Frequency</th><th>Quantity</th><th>Duration</th><th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(pres, index) in consultationForm.prescriptions" :key="index">
                                                        <td>{{ pres.drug.name }}</td>
                                                        <td>{{ pres.dose }}</td>
                                                        <td>{{ pres.frequency }}</td>
                                                        <td>{{ pres.quantity }}</td>
                                                        <td>{{ pres.duration }}</td>
                                                        <td><button class="btn btn-sm btn-danger" @click="removePrescription(index)">Delete</button></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="vert-tabs-settings" role="tabpanel" aria-labelledby="vert-tabs-settings-tab">
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Radiology Requests</label>
                                                <div class="input-group mb-2">
                                                    <select class="form-select" v-model="selectedRad">
                                                    <option value="">Select</option>
                                                    <option v-for="rad in rad_services" :key="rad.id" :value="rad">{{ rad.name }}</option>
                                                    </select>
                                                    <select class="form-select" v-model="selectedRadFreq">
                                                    <option disabled value="">Frequency</option>
                                                    <option>Daily</option>
                                                    <option>Twice Daily</option>
                                                    <option>Thrice Daily</option>
                                                    </select>
                                                    <button class="btn btn-primary" type="button" @click="addRad">Add</button>
                                                </div>
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr><th>Radiology</th><th>Frequency</th><th>Action</th></tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(rad, index) in consultationForm.radiology" :key="index">
                                                        <td>{{ rad.name }}</td>
                                                        <td>{{ rad.frequency }}</td>
                                                        <td><button class="btn btn-sm btn-danger" @click="removeRad(index)">Delete</button></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button class="btn btn-success" type="submit">Submit</button>
            </div>
        </div>
    </form>
    </div>
</template>

<script>
export default {
    data() {
      return {
        consultationForm: {
            history: '',
            complaint: '',
            action_plan: '',
            initial_diagnosis: [],
            final_diagnosis: [],
            laboratory: [],
            radiology: [],
            physios: [],
            prescriptions: [],
        },
        diagnoses: ['Malaria', 'Typhoid', 'Pneumonia'],
        drugs: [{ id: 1, name: 'Paracetamol' }, { id: 2, name: 'Amoxicillin' }],
        durations: [],
        icd_10_codes: [],
        lab_services: [{ id: 1, name: 'FBC' }, { id: 2, name: 'Urea & Electrolytes' }],
        laboratory_services: [],
        loading: false,
        rad_services: [{ id: 1, name: 'Chest X-ray' }, { id: 2, name: 'CT Scan' }],
        physio_services: [{ id: 1, name: 'Back Therapy' }, { id: 2, name: 'Neck Therapy' }],
        selectedLab: '',
        selectedLabFreq: '',
        selectedRad: '',
        selectedRadFreq: '',
        selectedPhysio: '',
        selectedPhysioFreq: '',
        selectedDrug: {
            drug: '',
            dose: '',
            frequency: '',
            quantity: '',
            duration: '',
        }
      }
    },
    methods: {
        getAllInitials() {
            axios.get('/api/emr/consultations/consultants/initials')
            .then((response) => {
                this.loading = false;
                this.refreshPage(response);
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
        addLab() {
            if (this.selectedLab && this.selectedLabFreq) {
            this.consultationForm.laboratory.push({ ...this.selectedLab, frequency: this.selectedLabFreq });
            this.selectedLab = '';
            this.selectedLabFreq = '';
            }
        },
        removeLab(index) {
            this.consultationForm.laboratory.splice(index, 1);
        },
        addRad() {
            if (this.selectedRad && this.selectedRadFreq) {
            this.consultationForm.radiology.push({ ...this.selectedRad, frequency: this.selectedRadFreq });
            this.selectedRad = '';
            this.selectedRadFreq = '';
            }
        },
        removeRad(index) {
            this.consultationForm.radiology.splice(index, 1);
        },
        addPhysio() {
            if (this.selectedPhysio && this.selectedPhysioFreq) {
            this.consultationForm.physios.push({ ...this.selectedPhysio, frequency: this.selectedPhysioFreq });
            this.selectedPhysio = '';
            this.selectedPhysioFreq = '';
            }
        },
        removePhysio(index) {
            this.consultationForm.physios.splice(index, 1);
        },
        addPrescription() {
            const d = this.selectedDrug;
            if (d.drug && d.dose && d.frequency && d.quantity && d.duration) {
            this.consultationForm.prescriptions.push({ ...d });
            this.selectedDrug = { drug: '', dose: '', frequency: '', quantity: '', duration: '' };
            }
        },
        removePrescription(index) {
            this.consultationForm.prescriptions.splice(index, 1);
        },
        refreshPage(response){
            this.drugs = response.data.drugs;
            this.drugForms = response.data.drug_forms;
            this.durations = response.data.durations; 
            this.icd_10_codes = response.data.icd_10_codes;
            this.laboratory_services = response.data.laboratory_services;
            this.radiology_services = response.data.radiology_services;
            this.frequencies = response.data.frequencies;
            this.routes = response.data.routes; 
            this.specific_drugs = response.data.specific_drugs;
        }
    },
    mounted(){
        this.getAllInitials();
    }

}
</script>  