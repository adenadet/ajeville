<template>
<div class="card card-widget">
    <div class="card-header">
        <div class="user-block">
            <img class="img-circle" :src="profilePicture(creator.image)" alt="User Image">
            <span class="username"><a href="#">{{FullName(creator)}}</a></span>
            <span class="description">Shared publicly - {{ExcelDate(notice.created_at) }}</span>
        </div>
    </div>
    <div class="card-body">
        <img v-if="notice.image != null" class="img-fluid pad" :src="'/upload/notices/'+notice.image" alt="Photo">
        <p>{{notice.content}}</p>
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
            creator: {},
            loading: false,
            notice: {},   
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/notices/'+this.$route.params.id).then(response =>{
                this.refresh(response);
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Departments were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Departments were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.notice = response.data.notice;
            this.creator = response.data.notice.creator;
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>
