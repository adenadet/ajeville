<template>
<section class="content-header pt-0">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <EServiceFormSearch  search_type="radiologist"/>
                <div class="card overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <EServiceDetailAppointmentList source="radiologist" :appointments.sync="reports" />
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="reports.per_page != null ? reports.per_page : 52" :records="reports.total != null ? reports.total : 550" ></pagination>
                    </div>
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
            current_page: 1,
            loading: false,
            reports: {},
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/radiologists').then(response =>{
                this.refresh(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Reports were loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Reports were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.reports = response.data.reports;
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>