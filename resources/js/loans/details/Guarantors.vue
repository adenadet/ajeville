<template>
<section>
    <div class="modal fade" id="GuarantorModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <h4 class="modal-title">{{ editMode ? 'Edit Guarantor Request: '+ loan.name : 'Create New Guarantor Request'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <GuarantorFormRequest :editMode="editMode" :loan.sync="account"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="GuarantorOverrideModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Guarantor Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <GuarantorDetailGuarantee />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="GuarantorViewModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Guarantor Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <GuarantorDetailGuarantee />
                </div>
            </div>
        </div>
    </div>
    <div class="overlay-wrapper">
        <div class="overlay" v-if="loading">
            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        </div>
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Loan Guarantors</h3>
                <button class="card-tools btn btn-xs btn-primary" @click="addNew()" v-if="source == 'Customer'">Modify Guarantors</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-stripped">
                    <thead class="bg-green">
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Guarantor</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Feedback</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody v-if="(guarantors != null) && (guarantors.length != 0)">
                        <tr v-for="(guarantor, index) in guarantors" :key="guarantor.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ FullName(guarantor) }}</td>
                            <td>{{ guarantor.email }} | {{ guarantor.phone }}</td>
                            <td>{{ guarantor.status == 0 ? 'Not Done' : (guarantor.status == 1 ? 'Ongoing': (guarantor.status == 2 ? 'Rejected' : (guarantor.status == 3 ? 'Confirmed': 'Unknown'))) }}</td>
                            <td :title="guarantor.description">{{ guarantor.description }}</td>
                            <td>
                                <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                <div class="dropdown-menu">
                                    <button class="btn btn-block dropdown-item" v-if="guarantor.status == 3" @click="viewGuarantor(guarantor.guarantor)"><i class="fa fa-eye mr-1 text-primary"></i> View </button>
                                    <button class="btn btn-block dropdown-item" v-else @click="resendGuarantor(guarantor.id)"><i class="fa fa-reply mr-1"></i> Resend Request</button>
                                    <button v-if="guarantor.status == 0" class="btn btn-block dropdown-item" @click="deleteGuarantor(guarantor.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Guarantor Request</button>
                                </div>
                            </td>
                        </tr>    
                    </tbody>
                    <tbody v-else>
                        <tr><td colspan="6">No Guarantor has been added</td></tr>
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</section>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
import GuarantorDetailGuarantee from '../../guarantors/details/View.vue';
export default {
    component:{
        GuarantorDetailGuarantee
    },
    data(){
        return {
            account: {},
            editMode: false,
            form: new Form({}),
            guarantors: [],
            guarantor: {},
            loading: true,
        }
    },
    methods:{
        addNew(){
            this.loading = true;
            this.editMode = false;
            this.$emit('GuarantorDataFill', this.account);
            $('#GuarantorModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#GuarantorModal').modal('hide');
            $('#GuarantorViewModal').modal('hide');
        },
        deleteGuarantor(id){
            this.loading = true;
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
                    this.form.delete('/api/loans/guarantors/'+id)
                    .then(response=>{
                        Swal.fire(response.data.status, response.data.message, response.data.status);
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/guarantors/loans/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                toast.fire({
                    icon: 'success',
                    title: 'Account was loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Accounts was not loaded successfully',
                })
            });
        },
        reloadPage(response){
            this.account = response.data.account;
            this.guarantors = response.data.guarantors;
            this.loading = false,
            this.closeModal();
        },
        resendGuarantor(id){
            this.loading = true;
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
                        Swal.fire({
                            icon: 'success', 
                            text: response.data.message, 
                            title: response.data.status,
                        });
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
                this.loading = false;
            });
        },
        viewGuarantor(guarantor){
            Fire.$emit('GuarantorDetailDataFill', guarantor);
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