<template>
    <section class="card">
        <div class="card-body">
            <div class="row overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <div class="col-md-12">
                    <form @submit.prevent="confirmLeaveRequest()">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Decision *</label>
                                    <select v-model="LeaveConfirmData.action" class="form-control">
                                        <option value="">--Select Decision--</option>
                                        <option value="confirm">Confirm</option>
                                        <option value="reject">Reject</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remark *</label>
                                    <QuillEditor v-model:content="LeaveConfirmData.remark" content-type="html"></QuillEditor>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Message to Downline </label>
                                    <QuillEditor v-model:content="LeaveConfirmData.message" content-type="html"></QuillEditor>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
<script>

export default {
    data(){
        return {
            LeaveConfirmData: new Form({
                request_id: '',
                action: '',
                remark: '',
                message: '',
            }),
            loading: false,
        }
    },
    emits: ['refreshPage'],
    methods:{
        addUser(){
            this.editMode = false;
            this.user = {};
            $('#userModal').modal('show');
        },
        confirmLeaveRequest(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, final decision!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.LeaveConfirmData.put('/api/hrms/leaves/confirm/'+this.LeaveConfirmData.request_id)
                    .then(response=>{
                        this.$swal.fire('Done!', 'Leave Request has been '+(this.LeaveConfirmData.action == 'confirm' ? 'confirmed' : 'rejected'), 'success');
                        this.$emit('refreshPage');
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.loading = false;
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        
    },
    mounted(){},
    props:{
        leave_request_id: Number,
    },
    watch:{
        leave_request_id(){
            this.LeaveConfirmData.reset();
            this.LeaveConfirmData.request_id = this.leave_request_id;
        }
    }
}
</script>
