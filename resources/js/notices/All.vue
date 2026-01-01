<template>
<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <NoticeDetailList source="general" :notices="notices"/>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="notices.per_page != null ? notices.per_page : 52" :records="notices.total != null ? notices.total : 550" ></pagination>
                </div>
            </div>
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
            current_page: 1,
            loading: false,
            notices: {},
        }
    },
    methods:{
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/notices?page='+page).then(response =>{
                this.refresh(response);
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Notices were loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Departments were not loaded successfully',
                })
            });
        },
        getNotices(page=1){
            axios.get('/api/ums/notices?page='+page)
            .then(response=>{
                this.notices = response.data.notices;   
            });
        },
        refresh(response){
            this.notices = response.data.notices;
            console.log('Working');
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>