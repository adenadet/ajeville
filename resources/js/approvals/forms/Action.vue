<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Reference ID</label>
                <div class="form-control">
                    {{ reference.unique_id }}
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
            <div class="form-group">
                <label>Remark</label>
                <QuillEditor class="form-control" content-type="html" name="remarks" id="remarks" v-model.content="approvalData.description" placeholder="Description"></QuillEditor>
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
                reference_id: '',
                reference_type: '',
                description: '',
            }),
            editMode: false,
            form_type: '',
            loading: false, 
        }
    },
    mounted() {},
    emits: ['approvalReload'],
    methods:{
        approveRequest(){
            this.loading = true;
            this.approvalData.reference_type = this.reference_type;
            this.approvalData.post('/api/approvals/actions')
            .then(response =>{
                this.loading = false;
                this.$emit('approvalReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Request has been approved',
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
        reference: Object,
        reference_type: String,
    },
    watch:{
        reference(){
            this.loading = true;
            this.approvalData.reference_id = this.reference.id;
            this.loading = false;
        }
    }
}
</script>