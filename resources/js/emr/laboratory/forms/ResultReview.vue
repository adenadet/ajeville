<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Patient</label>
                <div class="form-control">
                    {{ patientName(result?.request?.patient) }}
                </div>
            </div>
            <div class="form-group">
                <label>Result ID</label>
                <div class="form-control">
                    {{ result?.unique_id }}
                </div>
            </div>
        </div> 
        <div class="col-md-12">
            <div class="form-group">
                <label>Decision</label>
                <select class="form-control" id="decision" name="decision" v-model="reviewData.decision">
                    <option value="">--Select Decision--</option>
                    <option value="confirm">Confirm</option>
                    <option value="reject">Reject</option>
                </select>
            </div>
            <div class="form-group" v-if="reviewData.decision == 'reject'">
                <label>Reason</label>
                <input class="form-control" id="reason" name="reason" v-model="reviewData.reason">
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <QuillEditor class="form-control" content-type="html" name="remarks" id="remarks" v-model:content="reviewData.remarks" placeholder="Description"></QuillEditor>
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
            reviewData: new Form({
                decision: '',
                result_id: '',
                remarks: '',
                reason: '',
            }),
            editMode: false,
            loading: false, 
        }
    },
    mounted() {},
    emits: ['resultActionReload'],
    methods:{
        approveRequest(){
            this.loading = true;
            this.reviewData.result_id = this.result.id;
            this.reviewData.post('/api/emr/laboratory/results/action')
            .then(response =>{
                this.loading = false;
                this.$emit('resultActionReload', response);
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
        result: Object,
    },
    watch:{
        result(){
            this.loading = true;
            this.reviewData.result_id = this.result.id;
            this.loading = false;
        }
    }
}
</script>