<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Patient</label>
                <div class="form-control">
                    {{ patientName(specimen?.request?.patient) }}
                </div>
            </div>
            <div class="form-group">
                <label>Specimen ID</label>
                <div class="form-control">
                    {{ specimen?.unique_id }}
                </div>
            </div>
        </div> 
        <div class="col-md-12">
            <div class="form-group">
                <label>Decision</label>
                <select class="form-control" id="decision" name="decision" v-model="approvalData.decision">
                    <option value="">--Select Decision--</option>
                    <option value="confirm">Confirm</option>
                    <option value="reject">Reject</option>
                </select>
            </div>
            <div class="form-group" v-if="approvalData.decision == 'reject'">
                <label>Reason</label>
                <input class="form-control" id="reason" name="reason" v-model="approvalData.reason">
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <QuillEditor class="form-control" content-type="html" name="remarks" id="remarks" v-model:content="approvalData.remarks" placeholder="Description"></QuillEditor>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 float-right">
            <button class="btn btn-primary" @click="approveRequest">Submit</button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            approvalData: new Form({
                decision: '',
                specimen_id: '',
                remarks: '',
                reason: '',
            }),
            editMode: false,
            loading: false, 
        }
    },
    mounted() {},
    emits: ['specimenActionReload'],
    methods:{
        approveRequest(){
            this.loading = true;
            this.approvalData.specimen_id = this.specimen.id;
            this.approvalData.post('/api/emr/laboratory/specimens/action')
            .then(response =>{
                this.loading = false;
                this.$emit('specimenActionReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Sample has been actioned',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
            this.loading = false;
        },
    },
    props: {
        specimen: Object,
        //specimen_type: String,
    },
    watch:{
        specimen(){
            this.loading = true;
            this.approvalData.specimen_id = this.specimen.id;
            this.loading = false;
        }
    }
}
</script>