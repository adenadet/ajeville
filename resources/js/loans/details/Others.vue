<template>
<section>
    <div class="modal fade" id="OtherModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Edit File' : 'Add File'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LoanFormOthers :account="account" :editMode="editMode" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Loan Files</h3>
            <button class="card-tools btn btn-sm btn-primary" @click="addNew()" v-if="source == 'account'">Add File</button>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-stripped ">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>File</th>
                        <th>Added By</th>
                        <th>Comment</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody v-if="others != null">
                    <tr v-for="(other, index) in others" :key="other.id">
                        <td>{{ index | addOne }}</td>
                        <td>{{ other.file_name }}</td>
                        <td>{{ other.creator | FullName }}</td>
                        <td>
                            <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu">
                                <button class="btn btn-block dropdown-item" v-if="guarantor.status == 1" @click="viewGuarantor(guarantor.guarantor)"><i class="fa fa-eye mr-1 text-primary"></i> View </button>
                                <button class="btn btn-block dropdown-item" v-else @click="resendGuarantor(guarantor.id)"><i class="fa fa-reply mr-1"></i> Resend Request</button>
                                <button v-if="guarantor.status == 0" class="btn btn-block dropdown-item" @click="deleteGuarantor(guarantor.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Guarantor Request</button>
                            </div>
                        </td>
                    </tr>    
                </tbody>
                <tbody v-else>
                    <tr><td colspan="5">No Other File has been added</td></tr>
                </tbody>
            </table>
        </div> 
    </div>
</section>
</template>
<script>
export default {

    data(){
        return {
            account: {},
            editMode: false,
            form: new Form({}),
            others: {},
            other: {},
        }
    },
    methods:{
        addNew(){
            this.$Progress.start();
            this.editMode = false;
            Fire.$emit('OtherDataFill', {});
            $('#OtherModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#OtherModal').modal('hide');
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/loans/others/account/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Account was loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Accounts was not loaded successfully',
                })
            });
        },
        reloadPage(response){
            this.account = response.data.account;
            this.others = response.data.others;
            this.closeModal();
        },
        resendGuarantor(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "The guarantor would receive an email requesting his confirmation",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend request!'
                })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/loans/guarantors/resend/'+id)
                    .then(response=>{
                        Swal.fire(response.data.status, response.data.message, response.data.status);
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewGuarantor(guarantor){
            Fire.$emit('GuarantorDataFill', guarantor);
            $('#GuarantorViewModal').modal('show');
        },
    },
    mounted() {
        this.getAllInitials();
    },
    props:{
        source: String,
    },
}
</script>