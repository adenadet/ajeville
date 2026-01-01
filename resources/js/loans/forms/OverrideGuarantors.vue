<template>
    <section class="card">
        <div class="card-header">
            <h3 class="card-title" v-if="loan != null && loan.unique_id != null">{{ loan.unique_id }}  Guarantor Confirmation</h3>
            <h3 class="card-title" v-else>Something Went Wrong</h3>
        </div>
        <div class="card-body">
            <form @submit.prevent="overrideConfirmation()">
                <alert-error :form="guarantorConfirmationData"></alert-error> 
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label>Action</label>
                            <select type="text" class="form-control" id="status" name="status"  v-model="guarantorConfirmationData.status" required>
                                <option value=''>---Select status---</option>
                                <option value="confirm">Confirmed</option>
                                <option value="reject">Reject</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Comments</label>
                            <wysiwyg rows="5" v-model="guarantorConfirmationData.approved_remarks"></wysiwyg>
                        </div>
                    </div>
                </div>
                <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
            </form>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return {
            actions: [],
            account: {},
            current: {},
            loan: {},
            guarantorConfirmationData: new Form({
                approved_remarks: '',
                file_id: '',
                status: '',
            }),
        }
    },
    methods:{
        overrideConfirmation(){
            this.guarantorConfirmationData.file_id = this.file.id;
            this.guarantorConfirmationData.put('/api/loans/files/confirm/'+this.file.id)
            .then(response=>{
                Fire.$emit('GetCourse', response);
                Swal.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.guarantorConfirmationData.reset();
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
    },
    mounted() {
        Fire.$on('GuarantorOverrideDataFill', loan => {
            this.loan = loan;
        });  
    },
    props: {
        editMode: Boolean,
    },
}
</script>