<template>
    <section class="row">
        <div class="col-md-12">
            <div class="overlay-wrapper">
                <div class="overlay" v-if="loading">
                    <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                </div>
                <div class="modal fade" id="confirmFileModal">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Confirm File</h4>
                                <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <LoanFormConfirmFile />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="fileModal">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Upload Files</h4>
                                <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <LoanFormFile :account_id.sync="account.id" :editMode="editMode" :type="file_type" @reloadLoanFiles="reloadFiles"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Uploaded Files</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-xs" title="Add File" @click="addLoanFile()">
                                <i class="fa fa-file mr-1"></i>Add File
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead class="bg-green">
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>File</th>
                                    <th>Uploaded</th>
                                    <th>Status</th>
                                    <th v-if="source == 'account'"></th>
                                </tr>
                            </thead>
                            <tbody v-if="files != null">
                                <tr v-for="(file, index) in files" :key="file.id">
                                    <td>{{ addOne(index)}}</td>
                                    <td>{{ file.file_type }}</td>
                                    <td><a :href="'/'+file.source" target="_blank"><i class="fa fa-file-pdf text-danger"></i> </a></td>
                                    <td>{{ ExcelDate(file.created_at)  }}</td>
                                    <td>{{ file.status == 0 ? 'Unconfirmed' : (file.status == 1 ? 'Confirmed' : 'Rejected') }}</td>
                                    <td v-if="source == 'account'">
                                        <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-if="file.status == 0"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu">
                                            <button :disabled="loading" class="btn btn-block dropdown-item" @click="confirmFile(file)" ><i class="fa fa-file mr-1"></i> Confirm File</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr><td :colspan="source=='account' ? 6 : 5">No File Has Been Uploaded</td></tr>
                            </tbody>
                        </table>
                    </div>
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
export default {
    data(){
        return {
            editMode: false,
            file_type: '',
            file: {},
            files: [],
            form: new Form({}),
            loading: true,
            search_query: '',
        }
    },
    methods:{
        addLoanFile(){
            this.loading =true;
            this.editMode = false;
            $('#fileModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#confirmFileModal').modal('hide');
            $('#fileModal').modal('hide');
        },
        confirmFile(file){
            this.loading = true;
            this.editMode = false;
            $('#confirmFileModal').modal('show');
            this.loading = false;
        },
        /*approveFile(id){
            this.loading = true;
            axios.get('/api/loans/files/accept/'+id).
            then(response =>{
                this.files = response.data.files;
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Account Files was not loaded successfully',});
                this.loading = false;
            });
        },*/
        getInitials(){
            this.loading = true;
            axios.get('/api/loans/files/'+this.$route.params.id).
            then(response =>{
                this.reloadFiles(response);
                this.loading = false;
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Account Files was not loaded successfully',
                });
                this.loading = false;
            });
        },
        rejectFile(id){
            this.loading = true;
            axios.get('/api/loans/files/reject/'+id).
            then(response =>{
                this.files = response.data.files;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Account Files was not loaded successfully',});
                this.loading = false;
            });
        },
        searchLoanFile(){

        },
        reloadFiles(response){
            this.closeModal();
            this.files = response.data.files;
        },
    },
    mounted() {
        this.getInitials();   
    },
    props:{
        account: Object,
        source: String, 
    },
}
</script>
