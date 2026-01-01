<template>
<section class="col-md-12">
    <div class="modal fade" id="loanTypeModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true"> 
        <div class="modal-xl modal-dialog"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <h6 class="modal-title">{{editMode ? 'Update Loan Type' : 'Create New Loan Type'}} </h6> 
                    <button type="button" class="btn-default btn btn-sm" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()">
                        <i class="text-danger fa fa-times"></i>
                    </button> 
                </div> 
                <div class="modal-body p-3"> 
                    <LoanFormType :editMode="editMode"/>
                </div> 
            </div>
        </div> 
    </div>
    <div class="card custom-card"> 
        <div class="card-header justify-content-between"> 
            <div class="card-title"> Loan Type Items </div> 
            <div class="card-tools"> 
                <button class="btn btn-sm btn-primary-light" @click="addNew()"><i class="fa fa-plus mr-1"></i> New Type</button> 
            </div> 
        </div> 
        <div class="card-body"> 
            <div class="table-responsive"> 
                <table class="table text-nowrap"> 
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Basic Rate</th>
                            <th scope="col">Min. Duration</th>
                            <th scope="col">Max. Duration</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                            <th scope="col">Status</th>
                            <th scope="col"></th>
                        </tr> 
                    </thead> 
                    <tbody>
                        <tr v-for="loan_type in loan_types.data" :key="loan_type.id">
                            <th scope="row">{{ loan_type.name }}</th>
                            <td>{{ loan_type.category == 1 ? 'Personal' : (loan_type.category == 1 ? 'Business' : 'Others')}}</td>
                            <td>{{ loan_type.percentage | currency}}</td>
                            <td>{{ loan_type.min_duration == null || loan_type.min_duration == 0  ? 'Not Limit' : loan_type.min_duration }}</td>
                            <td>{{ loan_type.max_duration == null || loan_type.max_duration == 0  ? 'Not Limit' : loan_type.max_duration }}</td>
                            <td>{{ loan_type.start_date | excelDate  }}</td>
                            <td>{{ loan_type.end_date | excelDate }}</td>
                            <td>{{ loan_type.status == 1 ? 'Active' : 'Inactive'}}</td>
                            <td>
                                <button type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                <div class="dropdown-menu">
                                    <router-link class="btn btn-block dropdown-item" :to="'/loan_types/'+loan_type.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                                    <button class="btn btn-block dropdown-item" @click="editLoanType(loan_type)"><i class="fa fa-edit mr-1 text-warning"></i> Edit </button>
                                    <button class="btn btn-block dropdown-item" @click="deleteLoanType(loan_type.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Loan Request</button>
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
            loan_types: {},
        }
    },
    mounted() {
        Fire.$on('TypesRefresh', response =>{
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
            Fire.$emit('LoanTypeDataFill', {});
            $('#loanTypeModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#loanTypeModal').modal('hide');
        },
        deleteLoanType(id){
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
                    this.form.delete('/api/loans/types/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Type has been deleted.', 'success');
                        Fire.$emit('LoanTypesRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editLoanType(loan_type){
            this.$Progress.start();
            this.editMode = true;
            Fire.$emit('LoanTypeDataFill', loan_type);
            $('#loanTypeModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(){
            axios.get('/api/loans/types').then(response =>{
                this.refreshPage(response);
                toast.fire({icon: 'success', title: 'Loan Types loaded successfully',});
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Loan Types not loaded successfully',});
            });
        },
        refreshPage(response){
            this.loan_types = response.data.loan_types;
        },
    },
    props:{
        //'chat': Object,
    },
}
</script>
