<template>
<section class="col-md-12">
    <div class="modal fade" id="requirementModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true"> 
        <div class="modal-xl modal-dialog"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <h6 class="modal-title">{{editMode ? 'Update Loan Requirement' : 'Create New Loan Requirement'}} </h6> 
                    <button type="button" class="btn-default btn btn-sm" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()">
                        <i class="text-danger fa fa-times"></i>
                    </button> 
                </div> 
                <div class="modal-body p-3"> 
                    <LoanFormRequirement :editMode="editMode"/>
                </div> 
            </div>
        </div> 
    </div>
    <div class="card custom-card"> 
        <div class="card-header justify-content-between"> 
            <div class="card-title"> Loan Requirement Items </div> 
            <div class="card-tools"> 
                <button class="btn btn-sm btn-primary" @click="addNew()"><i class="fa fa-plus mr-1"></i> New Requirement</button> 
            </div> 
        </div> 
        <div class="card-body"> 
            <div class="table-responsive"> 
                <table class="table text-nowrap"> 
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Rate</th>
                            <th scope="col">Expires</th>
                            <th scope="col">Need Attachment</th>
                            <th scope="col">Last Edit</th>
                            <th scope="col"></th>
                        </tr> 
                    </thead> 
                    <tbody>
                        <tr v-for="requirement in requirements.data" :key="requirement.id">
                            <th scope="row">{{ requirement.name }}</th>
                            <td>{{ requirement.type | firstUp }}</td>
                            <td>{{ requirement.rate != null ? requirement.rate : 'Not Applicable'}}</td>
                            <td>{{ requirement.expires == 0 || requirement.expires == null ? 'No' : 'Yes' }}</td>
                            <td>{{ requirement.attachment == 0 || requirement.attachment == null ? 'No' : 'Yes' }}</td>
                            <td>{{ requirement.editor == null ? 'System Generated' : requirement.editor.first_name+' '+requirement.editor.first_name }}
                                <br/><span class="text-muted">{{ requirement.updated_at | excelDate }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-default-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                <div class="dropdown-menu">
                                    <!--router-link class="btn btn-block dropdown-item" :to="'/loans/1'"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link-->
                                    <button class="btn btn-block dropdown-item" @click="editRequirement(requirement)"><i class="fa fa-edit mr-1 text-warning"></i> Edit </button>
                                    <button class="btn btn-block dropdown-item" @click="deleteRequirement(requirement.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Requirement</button>
                                </div>
                            </td>
                        </tr> 
                    </tbody>
                </table> 
            </div> 
        </div>    
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            form: new Form({}),
            requirements: {},
            loan: {},

        }
    },
    mounted() {
        Fire.$on('RequirementsRefresh', response =>{
            this.refreshPage(response);
            this.closeModal();
        });
    },
    created() {
        this.getInitials();
    },
    methods:{
        addNew(){
            this.$Progress.start();
            this.editMode = false;
            Fire.$emit('RequirementDataFill', {});
            $('#requirementModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#requirementModal').modal('hide');
        },
        deleteRequirement(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/loans/requirements/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Requirement has been deleted.', 'success');
                        Fire.$emit('RequirementsRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editRequirement(requirement){
            this.$Progress.start();
            this.editMode = true;
            Fire.$emit('RequirementDataFill', requirement);
            $('#requirementModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(){
            axios.get('/api/loans/requirements').then(response =>{
                this.refreshPage(response);
                toast.fire({icon: 'success', title: 'Loan Requirements loaded successfully',});
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Loan Requirements not loaded successfully',});
            });
        },
        refreshPage(response){
            this.requirements = response.data.loan_requirements;
        },
    },
    props:{
        //'chat': Object,
    },
}
</script>
