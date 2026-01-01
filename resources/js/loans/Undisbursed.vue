<template>
<section class="container-fluid">
    <div class="modal fade" id="loanDisbursementModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true"> 
        <div class="modal-xl modal-dialog"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <h6 class="modal-title">{{editMode ? 'Update Loan Type' : 'Create New Loan Type'}} </h6> 
                    <button type="button" class="btn-default btn btn-sm" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()">
                        <i class="text-danger fa fa-times"></i>
                    </button> 
                </div> 
                <div class="modal-body p-3"> 
                    <LoanFormDisbursement :editMode="editMode"/>
                </div> 
            </div>
        </div> 
    </div>
    <div class="row">
        <div class="col-md-12">    
            <div class="card custom-card"> 
                <div class="card-header justify-content-between"> 
                    <div class="card-title"> Undisbursed Loans</div> 
                    <!--div class="card-tools"> 
                        <button class="btn btn-sm btn-primary-light" @click="addNew()"><i class="fa fa-plus mr-1"></i> New Type</button> 
                    </div--> 
                </div> 
                <div class="card-body"> 
                    <div class="table-responsive"> 
                        <table class="table text-nowrap"> 
                            <thead>
                                <tr>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Loan Name</th>
                                    <th scope="col">Loan Type</th>
                                    <th scope="col">Amount</th>
                                    
                                    <th scope="col">Created On</th>
                                    <th scope="col">Duration</th>

                                    <th scope="col"></th>
                                </tr> 
                            </thead> 
                            <tbody>
                                <tr v-for="account in accounts.data" :key="account.id">
                                    <th scope="row">{{account.user | FullName}}</th>
                                    <th scope="row">{{account.name}} <br /><span class="text-muted">{{ account.unique_id }}</span></th>
                                    <td>{{ account.type ? account.type.name : 'Old Type' }}</td>
                                    <td>{{ account.amount | currency}}</td>
                                    <td>{{ account.created_at | excelDate }}</td>
                                    <td>{{ account.duration }} weeeks</td>
                                    <td>
                                        <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu">
                                            <button class="btn btn-block dropdown-item" @click="createDisbursement(account)"><i class="fa fa-check mr-1 text-primary"></i> Confirm Disbursement </button>
                                        </div>
                                    </td>
                                </tr> 
                            </tbody>
                        </table> 
                    </div> 
                </div>    
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
data(){
    return  {
        accounts: {},
        editMode: false,
        form: new Form({}),
    }
},
mounted() {
    Fire.$on('TypesRefresh', response =>{
        this.refreshUndisbursed(response);
        this.closeModal();
    });
},
created() {
    this.getInitials();
},
methods:{
    closeModal(){
        $('#loanDisbursementModal').modal('hide');
    },
    createDisbursement(account){
        this.$Progress.start();
        this.editMode = false;
        Fire.$emit('LoanDisbursementDataFill', account);
        $('#loanDisbursementModal').modal('show');
        this.$Progress.finish();
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
    getInitials(){
        axios.get('/api/loans/disbursements').then(response =>{
            this.refreshUndisbursed(response);
        })
        .catch(()=>{
            toast.fire({icon: 'error', title: 'Loan Types not loaded successfully',});
        });
    },
    refreshUndisbursed(response){
        this.accounts = response.data.accounts;
        //this.closeModal();
    },
},
props:{
    //'chat': Object,
},
}
</script>
