<template>
<section>
<form>
    <alert-error :form="consultationData"></alert-error>
    <div class="card overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card-body">
            <h3>Annex G: United Kingdom pre-entry TB screening programme medical record Symptom screen, history of contact with TB and discretionary medical examination (all applicants)</h3>
            <p>Symptom screen</p>
            <div class="card-body">
                <div class="row">
                    <label class="col-sm-12">Has the applicant (or their child) had any of the following symptoms in the last 3 months *</label>
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cough" id="cough" v-model="consultationData.sym_cough">
                            <label class="form-check-label">Cough</label>
                        </div>
                    </div> 
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fever" id="fever" v-model="consultationData.sym_fever">
                            <label class="form-check-label">Fever</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="haemoptysis" id="haemoptysis" v-model="consultationData.sym_haemoptysis">
                            <label class="form-check-label">Haemoptysis</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="night_sweats" id="night_sweats" v-model="consultationData.sym_night_sweats">
                            <label class="form-check-label">Night Sweats</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="weight_loss" id="weight_loss" v-model="consultationData.sym_weight_loss">
                            <label class="form-check-label">Weight Loss</label>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="row">
                    <label class="col-sm-12">For Children only; is there any history of the following:</label>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kid_respiratory" id="kid_respiratory" v-model="consultationData.kid_respiratory">
                            <label class="form-check-label">Any respiratory disease, such as Cystic fibrosis</label>
                        </div>
                    </div> 
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kid_throaic_surgery" id="kid_throaic_surgery" v-model="consultationData.kid_throaic_surgery">
                            <label class="form-check-label">Previously Thoraic surgery</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kid_cyanosis" id="kid_cyanosis" v-model="consultationData.kid_cyanosis">
                            <label class="form-check-label">Cyanosis</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kid_cyanosis" id="kid_cyanosis" v-model="consultationData.kid_respiratory_insufficiency">
                            <label class="form-check-label">Respiratory Insufficiency that limits activity</label>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="row">
                    <label class="col-sm-12">For Women only:</label>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="women_pregnant" id="women_pregnant" v-model="consultationData.women_pregnant">
                            <label class="form-check-label">Pregnant</label>
                        </div>
                    </div> 
                </div>
                <div class="clear"></div>
                <div class="row">
                    <label class="col-sm-12">For All Applicants</label>
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="all_previous_tb" id="all_previous_tb" v-model="consultationData.all_previous_tb">
                            <label class="form-check-label">Is there any history of previous TB?</label>
                        </div>
                    </div> 
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="all_household_tb" id="all_household_tb"  v-model="consultationData.all_household_tb">
                            <label class="form-check-label">has anyone in the household been diagnosed with TB in the last 2 years?</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="all_recent_contact" id="all_recent_contact" v-model="consultationData.all_recent_contact">
                            <label class="form-check-label">Is there any history of recent contact with a case of active pulmonary TB (shared the same enclosed air space or household or other enclosed environmentfor a prolonged period for days or weeks)></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Doctor's Decision:</label>
                            <select class="form-control" id="decision" name="decision" required v-model="consultationData.decision" :class="{'is-invalid' : consultationData.errors.has('decision') }">
                                <option value="">--Select Decision--</option>
                                <option value="6">Send to Xray for CXR</option>
                                <option value="7">Send to Lab for Sputum Test</option>
                                <option value="8">Kid under 11 years</option>
                                <option value="10">Patient Postponed</option>
                                <option value="11">Patient Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label>Doctor's Remark:</label>
                            <QuillEditor id="remarks" name="remarks" placeholder="Enter Remark *" required  content-type="html"  v-model:content="consultationData.remarks" :class="{'is-invalid' : consultationData.errors.has('remarks') }"></QuillEditor>
                        </div>
                    </div>
                </div>
                <button @click.prevent="editMode ? updateConsultationData() : createConsultationData()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            consultationData: new Form({
                decision: "",
                remarks: "",
                all_previous_tb:'',
                all_household_tb: '',
                all_recent_contact: '',
                kid_respiratory:'',
                kid_throaic_surgery: '',
                kid_cyanosis: '', 
                kid_respiratory_insufficiency: '',
                women_pregnant: '',
                sym_cough: '',
                sym_fever: '',
                sym_haemoptysis: '',
                sym_night_sweats: '',
                sym_weight_loss: '',
                appointment_id: '',
                id: '',
            }),
            loading : false,
        }
    },
    mounted() {},
    methods:{
        createConsultationData(){
            this.loading = true;
            this.consultationData.appointment_id = this.appointment.id;
            this.consultationData.post('/api/emr/consultations')
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Consultation has been saved',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        },
        updateConsultationData(){
            this.loading = true;
            this.consultationData.put('/api/emr/consultations/'+this.consultation.id)
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Consultation details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });          
        },
    },
    props:{
        editMode: Boolean,
        consultation: Object, 
        appointment: Object,
        patient: Object,
    },
    watch:{
        consultation(){
            this.consultationData.fill(this.consultation);
        }
    }
}
</script>