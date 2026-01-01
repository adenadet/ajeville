<template>
<section>
<form v-if="((appointment.consent != null) && (appointment.consultation != null))">
	<alert-error :form="IssueData"></alert-error> 
	<div class="card row">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card-header">Issue Certificate</div>
        <div class="card-body overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Patient</label>
                        <div class="form-control" v-html="appointment.patient != null ? appointment.patient.last_name+' '+appointment.patient.first_name+' '+appointment.patient.middle_name: 'No patient detail'" :class="{'is-invalid' : IssueData.errors.has('first_name') }"></div>
                        <has-error :form="IssueData" field="first_name"></has-error> 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Service</label>
                        <div class="form-control" v-html="appointment.service != null ? appointment.service.name : 'No service selected'" :class="{'is-invalid' : IssueData.errors.has('middle_name') }"></div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Action*</label>
                        <select class="form-control" id="summary" name="summary" required v-model="IssueData.issue_action" :class="{'is-invalid' : IssueData.errors.has('issue_action') }">
                            <option value="">--Select Action--</option>
                            <option value="certificate">Issue Certificate</option>
                            <option value="cert_ref">Issue Certificate + Referral</option>
                            <option value="referral">Referral only</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Details*</label>
                        <QuillEditor theme="snow" v-model:content="IssueData.issue_detail" content-type="html" id="details" name="details" placeholder="Details *" required :class="{'is-invalid' : IssueData.errors.has('issue_detail') }" />
                    </div>
                </div>
            </div>
            <button @click.prevent="createIssueData()" type="submit" name="submit" class="submit btn btn-success">Submit</button>        
        </div>
    </div>
</form>
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data() {
        return {
            IssueData: new Form({
                issue_action: "",
                issue_detail: '',
            }),
            loading : false,
        };
    },
    methods:{
        createIssueData(){
            this.loading = true;
            this.IssueData.appointment_id = this.appointment.id;
            this.IssueData.put('/api/emr/appointments/issue/'+this.appointment.id).then(response =>{
                this.loading = false;
                this.$emit('reportReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Certifcate is cleared for issue',
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
        appointment: Object,
    }
};
</script>