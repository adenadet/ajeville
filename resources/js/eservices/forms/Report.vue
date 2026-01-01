<template>
<section>
<form>
    <alert-error :form="ReportData"></alert-error> 
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Patient</label>
                <div class="form-control"  :class="{'is-invalid' : ReportData.errors.has('first_name') }">{{ FullName(patient)}}</div>
                <has-error :form="ReportData" field="first_name"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Service</label>
                <div class="form-control" v-html="service != null ? service.name : 'No service selected'"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Result Summary</label>
                <select class="form-control" id="summary" name="summary" required v-model="ReportData.summary" :class="{'is-invalid' : ReportData.errors.has('summary') }">
                    <option value="">--Select Report Summary--</option>
                    <option value="normal">Normal</option>
                    <option value="suggestive">Abnormal suggestive of TB</option>
                    <option value="not suggestive">Abnormal not suggestive of TB</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Findings <span><small>You can choose multiple</small></span></label>
                <select class="form-control" id="findings" name="findings[]" multiple v-model="ReportData.findings" :class="{'is-invalid' : ReportData.errors.has('findings') }" required>
                    <option value="">--Select Findings--</option>
                    <option value="-1">None</option>
                    <option v-for="finding in findings" :value="finding.id" :key="finding.id">{{finding.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label>Detailed Report*</label>
                <QuillEditor theme="snow" id="details" name="details" class="form-control" placeholder="Details *" required v-model:content="ReportData.details" content-type="html" :class="{'is-invalid' : ReportData.errors.has('details') }"></QuillEditor>
            </div>
        </div>
    </div>
    <button @click.prevent="editMode ? updateReportData() : createReportData()" type="submit" name="submit" class="submit btn bg-navy">Submit</button>
</form>
</section>
</template>
<script>
import Form from 'vform';
export default {
    data(){
        return  {
            findings: [],
            patient: {},
            ReportData: new Form({
                patient_id:'', 
                appointment_id:'', 
                id: '',
                summary: '',
                findings: [],
                details: '', 
            }),
            service: {},
        }
    },
    emits:['refreshAppointment'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        createReportData(){
            this.loading = true;
            this.ReportData.appointment_id = this.appointment.id;
            this.ReportData.patient_id = this.patient.id;
            this.ReportData.post('/api/emr/radiologists')
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Report has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/radiologists/initials')
            .then(response =>{
                this.findings = response.data.findings;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Reports were not loaded successfully',
                })
            });
        },
        updateReportData(){
            this.loading = true;
            this.ReportData.appointment_id = this.report.id;
            this.ReportData.patient_id = this.patient.id;
            this.ReportData.put('/api/emr/radiologists/'+ this.ReportData.id)
            .then(response =>{
                this.$emit('refreshAppointment', response);
                this.loading = false;
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Report has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });            
        },
    },
    props:{
        appointment: Object,
        editMode: Boolean,
        report: Object,
    },
    watch:{
        report(){
            if (this.report != null ){
                this.ReportData.summary = this.report.summary;
                this.ReportData.details = this.report.details;
                this.ReportData.id = this.report.id;
                this.ReportData.findings = [];
                for (let i = 0; i < this.report.findings.length; i++) {this.ReportData.findings.push(this.report.findings[i].id);} 
            }
        },
        appointment(){
            this.patient = this.appointment.patient;
            this.service = this.appointment.service;
        }
    }
}
</script>
