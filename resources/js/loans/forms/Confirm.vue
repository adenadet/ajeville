<template>
<section class="card">
    <div class="card-header">
        <h3 class="card-title" v-if="current.id == 16">Completed Confirmation</h3>
        <h3 class="card-title" v-else-if="current != null && current.role != null">{{ current.role.name }} Confirmation</h3>
        <h3 class="card-title" v-else>Something Went Wrong</h3>
    </div>
    <div class="card-body" v-if="current != null && current.role != null">
        <form @submit.prevent="createConfirmation() ">
            <alert-error :form="loanConfirmationData"></alert-error> 
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Action</label>
                        <select type="text" class="form-control" id="action" name="action"  v-model="loanConfirmationData.action" required>
                            <option value=''>---Select Action---</option>
                            <option value="8" v-if="current.role != null && current.role.name == 'Account Officer'">Send To Risk Officer</option>
                            <option value="11" v-if="current.role != null && current.role.name == 'Group Head'">Send To Directorate Head</option>
                            <option value="12" v-if="current.role != null && current.role.name == 'Group Head'">Send To Enterprise Risk Manager</option>
                            <option value="16" v-if="current.role != null && current.role.name == 'Managing Director'">Confirm Loan</option> 
                            <option v-for="(action,index) in actions" :value="action.stage_number" :key="action.stage_number">{{ index == 0 ? 'Send To' : 'Return To' }} {{ action.role.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Comments</label>
                        <wysiwyg rows="5" v-model="loanConfirmationData.description"></wysiwyg>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
        </form>
    </div>
    <div class="card-body" v-else>
        <div class="row">
            <div class="col-md-12">
                <p v-if="current.id == 16">The confirmation process has been completed and can not be reversed.</p>
                <p v-else>Kindly confirm that all Loan requirements have been fulfilled especially Guarantors and Checklist have been completed.</p>
            </div>
        </div>
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
            loan_types: [],
            loanConfirmationData: new Form({
                action: '',
                description: '',
                loan_id:'', 
            }),
        }
    },
    methods:{
        createConfirmation(){
            this.loanConfirmationData.loan_id = this.account.id;
            this.loanConfirmationData.post('/api/loans/confirms')
            .then(response=>{
                Fire.$emit('GetCourse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'New Requirement was created successfully',
                });
                this.loanConfirmationData.reset();
                this.$router.push('/staff/confirm/loans');
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
        getInitials(){
            axios.get('/api/loans/confirms/initials/'+this.$route.params.id).then(response =>{
                this.actions = response.data.actions,
                this.account = response.data.account;
                this.current = response.data.current;
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Loan Confirmation Form Initialization failed',
                })
            });
        },
    },
    mounted() {
        this.getInitials()
        Fire.$on('RequirementDataFill', requirement =>{
            this.requirementData.fill(requirement);
        });     
    },
    props: {
        editMode: Boolean,
    },
}
</script>