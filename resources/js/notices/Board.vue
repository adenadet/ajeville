<template>
    <div class="card card-danger">
        <div class="card-header">
            <h3 class="card-title">Notices</h3>
        </div>
        <div class="card-body" style="height:400px; overflow-y: scroll;">
            <div class="callout callout-danger" v-for="notice in notices.data" :key="notice.id">
                <h5>{{notice.topic}}</h5>
                <p>{{readMore(notice.content, 50, '...')}}</p>
                <a :href="'/notices/'+notice.id"><button class="btn btn-sm btn-primary"> Details</button></a>
            </div>
        </div>
        <div class="card-footer"><a class="float-right" href="/notices">See More</a></div>
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
                    Fire.$emit('CatRefresh', response);   
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
        }
    },
    props:{
        notices: Object,
        source: String,
    }
}
</script>