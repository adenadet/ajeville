<template>
<section class="row">
    <div class="col-md-12">
        <div class="row">
            <form @submit.prevent="editMode ? updateRequirement() : createRequirement() ">
                <alert-error :form="requirementData"></alert-error> 
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name *" v-model="requirementData.name" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Type</label>
                            <select type="text" class="form-control" id="type" name="type" v-model="requirementData.type" required>
                                <option value="">--Select Requirement Type--</option>
                                <option value="advanced">Advanced</option>
                                <option value="basic">Basic</option>
                                <option value="interest">Interest</option>
                                <option value="guarantors">Guarantors</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Rate/Quantity</label>
                            <input type="number" class="form-control" id="rate" name="rate" placeholder="Rate/Quantity *" v-model="requirementData.rate">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-check form-switch"> 
                            <input class="form-check-input" type="checkbox" role="switch" name="expires" id="expires" checked="" v-model="requirementData.expires"> 
                            <label class="form-check-label">Does the requirement expires?</label> 
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-check form-switch"> 
                            <input class="form-check-input" type="checkbox" role="switch" name="attachment" id="attachment" checked="" v-model="requirementData.attachment"> 
                            <label class="form-check-label">Does the requirement require an attachment?</label> 
                        </div>
                    </div>
                </div>
                <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
            </form>
        </div>
    </div>
</section>
</template>
<script>
    export default {
        data(){
            return {
                banks: [],
                loan_types: [],
                requirementData: new Form({
                    id:'',
                    name: '',
                    type: '', 
                    rate: '', 
                    expires: '', 
                    attachment: '', 
                }),
            }
        },
        methods:{
            createRequirement(){
                this.requirementData.post('/api/loans/requirements')
                .then(response=>{
                    Fire.$emit('GetCourse', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'New Requirement was created successfully',
                    });
                    this.requirementData.reset();
                    Fire.$emit('RequirementsRefresh', response);
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
                axios.get('/api/loans/accounts/initials').then(response =>{
                    this.banks = response.data.all_banks,
                    this.loan_types = response.data.loan_types;
                    this.users = response.data.users;
                })
                .catch(()=>{
                    toast.fire({
                        icon: 'error',
                        title: 'Loan Form Initialization failed',
                    })
                });
            },
            updateRequirement(id){
                this.requirementData.put('/api/loans/requirements/'+this.requirementData.id)
                .then(response=>{

                    Swal.fire({
                        icon: 'success',
                        title: 'Requirement was updated successfully',
                    });
                    Fire.$emit('RequirementsRefresh', response);
                    this.requirementData.reset();
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
            Fire.$on('RequirementDataFill', requirement =>{
                this.requirementData.fill(requirement);
            });     
        },
        props: {
            editMode: Boolean,
        },
    }
</script>