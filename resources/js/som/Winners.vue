<template>
<div class="card card-primary">
    <div class="card-header">Staffs of the Month</div>
    <div class="card-body overlay-dark">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-3"><button class="btn btn-default" @click="showSOM(month.id)" v-for="month in staff_months.data" :key="month.id">{{ExcelMonthYear(month.month) }}</button></div>
            <div class="col-md-9">
                <SOMDetailMonthWinner :month.sync="staff_month" />
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
        return  {
            items: [],
            loading: false,
            staff_month: {},
            staff_months: {},
        }
    },
    mounted() {
        this.getAllInitials();
        /*Fire.$on('BranchDataFill', response =>{});
        Fire.$on('AfterCreation', ()=>{});*/
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/som/winners').then(response =>{
                this.refresh(response);
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Winners were loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Winners were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.staff_months = response.data.som_months;
            this.items = response.data.staff_months;
        },
        showSOM(id){
            axios.get('/api/som/winners/'+id).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Winners were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Winners were not loaded successfully',
                })
            });            
        },
    },
    props:{branch: Object, editMode: Boolean, users: Array,}
}
</script>
