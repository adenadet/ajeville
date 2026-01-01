<template>
<section>
    <form>
        <alert-error :form="HealthSubstanceForm"></alert-error>
        <div class="row">
            <div class="col-sm-12" v-if="((patient != null) && (patient.first_name != null) && (typeof (patient.first_name) != 'undefined'))">
                <div class="form-group">
                    <label>Patient</label>
                    <div class="form-control">{{patient | patientName}}</div>
                    <input type="hidden" name="patient_id" id="patient_id" v-model="HealthSubstanceForm.patient_id" />
                </div>
            </div>
            <div class="col-sm-12" v-else>
                <div class="form-group">
                    <label>Patient</label>
                    <select class="form-control" name="patient_id" id="patient_id" v-model="HealthSubstanceForm.patient_id" >
                        <option value="">--Select Service User--</option>
                        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient | patientName }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name of Product/Substance</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" v-model="HealthSubstanceForm.product_name" />
                    <has-error :form="HealthSubstanceForm" field="product_name"></has-error>
                </div>
            </div> 
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Type of Harm the substance could cause</label>
                    <wysiwyg v-model="HealthSubstanceForm.harm_type" rows="4"></wysiwyg>
                    <has-error :form="HealthSubstanceForm" field="harm_type"></has-error>
                </div>
            </div> 
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description of the Substance</label>
                    <div class="input-group">
                        <select class="" id="substance_form" name="substance_form" v-model="HealthSubstanceForm.substance_form">
                            <option value="">--Select Substance Form--</option>
                            <option value="liquid">Liquid</option>
                            <option value="solid">Solid</option>
                            <option value="vapour">Vapour</option>
                            <option value="gas">Gas</option>
                        </select>
                        <div class="input-group-append">
                            <input type="text" name="substance_colour" id="substance_colour" class="input-group-text" v-model="HealthSubstanceForm.substance_colour" placeholder="Enter the colour of the substance" />
                        </div>
                    </div>
                    <has-error :form="HealthSubstanceForm" field="substance_form"></has-error>
                    <has-error :form="HealthSubstanceForm" field="substance_colour"></has-error>
                </div>
            </div> 
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Detail how substance causes harm</label>
                    <input type="text" class="form-control" id="causes_harm" name="causes_harm" v-model="HealthSubstanceForm.causes_harm" />
                    <has-error :form="HealthSubstanceForm" field="causes_harm"></has-error>
                </div>
            </div> 
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Who is likely to come into contact with the substance?</label>
                    <input type="text" class="form-control" id="contact" name="contact" v-model="HealthSubstanceForm.contact" />
                    <has-error :form="HealthSubstanceForm" field="contact"></has-error>
                </div>
            </div> 
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>How often is it used, or may it occur?</label>
                    <input type="text" class="form-control" id="frequency" name="frequency" v-model="HealthSubstanceForm.frequency" />
                    <has-error :form="HealthSubstanceForm" field="frequency"></has-error>
                </div>
            </div> 
        </div>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>What will the substance be used for or what circumstances/activity may produce the substance?</label>
                    <wysiwyg v-model="HealthSubstanceForm.substance_use" rows="4" name="substance_use" id="substance_use"></wysiwyg>
                    <has-error :form="HealthSubstanceForm" field="substance_use"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Can hazardous substance, circumstances or activity be eliminated, or a safer alternative be used?</label>
                    <select class="form-control" name="safer_alternative" id="safer_alternative" v-model="HealthSubstanceForm.safer_alternative" >
                        <option value="">--Select Answer--</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row" v-if="HealthSubstanceForm.safer_alternative == 'no'">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Controls (list control measures for storage, use and handling as appropriate)</label>
                    <wysiwyg v-model="HealthSubstanceForm.controls" rows="4" name="controls" id="controls"></wysiwyg>
                    <has-error :form="HealthSubstanceForm" field="controls"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Detail emergency procedures in case of accidental spillage or contact</label>
                    <wysiwyg v-model="HealthSubstanceForm.emergency_procedures" rows="4" name="emergency_procedures" id="emergency_procedures"></wysiwyg>
                    <has-error :form="HealthSubstanceForm" field="emergency_procedures"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Are all staff aware of this assessment, in particular the controls and emergency procedures?</label>
                    <select class="form-control" name="staff_aware" id="staff_aware" v-model="HealthSubstanceForm.staff_aware" >
                        <option value="">--Select Answer--</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                    <has-error :form="HealthSubstanceForm" field="staff_aware"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Have controls reduced the risk of harm to an acceptable level?</label>
                    <select class="form-control" name="reduced_risk" id="reduced_risk" v-model="HealthSubstanceForm.reduced_risk" >
                        <option value="">--Select Answer--</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                    <has-error :form="HealthSubstanceForm" field="reduced_risk"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Action to be taken where required</label>
                    <wysiwyg v-model="HealthSubstanceForm.further_actions" rows="4" name="further_actions" id="further_actions"></wysiwyg>
                    <has-error :form="HealthSubstanceForm" field="further_actions"></has-error>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updateRequest() : createRequest()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            HealthSubstanceForm: new Form({
                patient_id: '',
                task_id: '',
                id: '',
                repeating: '',
                start_date: '',
                frequency_id: '',
                quantity: 0,
                details: '',
                domiciliary: '',
            }),
            today: '',
            patients: [],
            tasks: [],
            frequencies: [],
        }
    },
    mounted() {
        this.getInitials();
        const date = new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('patientTaskDataFill', patient_task => {
            this.HealthSubstanceForm.fill(patient_task);
            if(this.patient != null){this.HealthSubstanceForm.patient_id = this.patient.id;}
        });
        Fire.$on('AfterCreation', () => {
            //axios.get("api/profile").then(({ data }) => (this.ApplicantData.fill(data)));
        });
    },
    methods: {
        createRequest() {
            this.$Progress.start();
            this.HealthSubstanceForm.post('/api/emr/nursing/patient_tasks')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshHealthSubstance', response.data.health_substance);
                Swal.fire({
                    icon: 'success',
                    title: 'The Health Substance Assessment details has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        updateRequest() {
            this.$Progress.start();
            this.HealthSubstanceForm.put('/api/emr/assessment/health_substances/'+this.HealthSubstanceForm.id)
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshHealthSubstance', response.data.health_substance);
                Swal.fire({
                    icon: 'success',
                    title: 'The Patient Task has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        getInitials() {
            axios.get('/api/emr/nursing/tasks/initials')
            .then(response => {
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Nursing Tasks were loaded successfully',
                });
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Nursing Tasks were not loaded successfully',
                })
            });
        },
        refresh(response) {
            this.patients = response.data.patients;
            this.tasks = response.data.tasks;
            this.frequencies = response.data.frequencies;
        },
    },
    props: {
        editMode: Boolean,
        patient: Object,
        domiciliary: Number,
    }
}
</script>