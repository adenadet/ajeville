<template>
<section class="overlay-wrapper"><div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="approveBatch">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Batch</label>
                    <div class="form-control">
                        {{batch.batch_number}} [{{ batch.unique_id }}]
                    </div>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Decision</label>
                    <select class="form-control" id="decision" name="decision" v-model="approvalData.decision">
                        <option value="">--Select Decision--</option>
                        <option value="confirm">Confirm</option>
                        <option value="reject">Reject</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Remark</label>
                    <QuillEditor class="form-control" contentType="html" name="remarks" id="remarks" v-model.content="approvalData.remark" placeholder="Description"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            approvalData: new Form({
                id: '',
                decision: '',
                remark: '',
            }),
            loading: false,
        };
    },
    emits:['refreshBatchApproval'],
    methods: {
        approveBatch(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Let me check!',
                confirmButtonText: 'Yes'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.approvalData.put('/api/procurement/batches/confirm/'+this.batch.id)
                    .then( () =>{
                        this.$emit('refreshBatchApproval')
                        this.$swal.fire('Done!', 'Batch has been approved.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
    },
    mounted(){},
    props:{
        batch: {
            type: Object,
            default: () => ({}),
        },    
    }
};
</script>