<template>
<div class="col-12">
    <div class="card overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <NoticeDetailList source="admin" :notices="notices" @reloadPage="reset"/>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="notices.per_page != null ? notices.per_page : 52" :records="notices.total != null ? notices.total : 550" ></pagination>
        </div>
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
            current_page: 1,
            departments: [],
            editMode: false,
            form: new Form({}),
            loading: false,
            notice: {},
            notices: {},
        }
    },
    methods:{
        createNotice(){
            this.editMode = false;
            this.notice = {};
            //Fire.$emit('noticeDataFill', this.notice);
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
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        editNotice(notice){
            this.editMode = true;
            this.notice = notice;
            //Fire.$emit('noticeDataFill', notice);
            $('#noticeModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/notices?t=all&page='+page).then(response =>{
                this.reset(response);
                this.loading = false;
                toast.fire({icon: 'success', title: 'Notice loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Notice not loaded successfully',});
            });
        },
        reset(response){
            this.categories = response.data.categories;
            this.departments = response.data.departments;
            this.notices = response.data.notices;
        },
    },
    mounted() {
        this.getAllInitials();
        //Fire.$on('reloadNotice', response =>{this.reset(response); console.log("Updated")});
    }   
}
</script>