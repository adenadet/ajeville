<template>
    <div class="modal fade" id="noticeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Edit Notice: '+ notice.topic : 'Create New Notice'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <NoticeForm :departments="departments" :editMode="editMode" :notice="notice" @reloadList="reloadList"/>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-header bg-navy">
        <h3 class="card-title">List of Notices</h3>
        <div class="card-tools" v-if="source == 'admin'"><button type="button" class="btn btn-xs btn-primary" @click="createNotice"><i class="fa fa-plus mr-1"></i>Add New</button></div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive p-0">
            <table class="table m-b-0 table-striped">
                <thead>
                    <tr>
                        <th>Topic</th>
                        <th>Content</th>
                        <th>Creator</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="notice in notices.data" :key="notice.id">
                        <td>{{notice.topic}}</td>
                        <td :title="notice.content" v-html="readMore(notice.content, 25, '...')"></td>
                        <td>{{FullName(notice.creator)}}</td>
                        <td>{{ExcelDate(notice.start_date)}}</td>
                        <td>{{ExcelDate(notice.end_date)}}</td>
                        <td>
                            <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <router-link :to="'/notices/'+notice.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1"></i> View Notice</button></router-link>
                                <button v-if="source == 'admin'" class="dropdown-item btn btn-block btn-sm" title="Edit Notice" @click="editNotice(notice)"><i class="fa fa-edit mr-1 text-primary"></i> Edit Notice</button>
                                <button v-if="source == 'admin'" class="dropdown-item btn btn-block btn-sm" title="Delete Notice" @click="deleteNotice(notice.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Notice</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
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
            categories: {},
            departments: [],
            editMode: false,
            form: new Form({}),
            notice: {},
        }
    },
    emits:['reloadPage'],
    methods:{
        createNotice(){
            this.editMode = false;
            this.notice = {};
            $('#noticeModal').modal('show');
        },
        deleteNotice(id){
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
                    this.form.delete('/api/notices/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Notice has been deleted.', 'success');
                        this.$emit('reloadPage', response);   
                    })
                    .catch(()=>{Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        editNotice(notice){
            this.editMode = true;
            this.notice = notice;
            $('#noticeModal').modal('show');
        },
        reset(response){
            this.notices = response.data.notices;
        },
        reloadList(response){
            $('#noticeModal').modal('hide');
            this.$emit('reloadPage', response);
        }
    },
    mounted() {
        //this.getAllInitials();
        //Fire.$on('reloadNotice', response =>{this.reset(response); console.log("Updated")});
    },
    props:{
        notices: Object,
        source: String,
    }
}
</script>