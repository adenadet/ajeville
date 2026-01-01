<template>
<section class="overlay-wrapper">
    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bordered Table</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>                  
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Task</th>
                        <th>Progress</th>
                        <th style="width: 40px">Label</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(vote, index) in votes" :key="vote.id">
                        <td>{{ addOne(index)  }}</td>
                        <td>{{ FullName(vote.nominee) }} <br /><span class="text-muted">{{vote.nominee.department != null ? vote.user.department.name : '' }}</span></td>
                        <td>{{ vote.branch != null ? vote.branch.name : 'Not Specified' }}</td>
                        <td><span class="badge bg-danger">55%</span></td>
                    </tr>
                </tbody>
            </table>
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
        return  {
            items: [],
            loading: false,
            votes: {},
            staff_months: {},
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods:{
        refresh(response){},
    },
    props:{staff_month: Object,},
    watch:{
        staff_month(){
            this.votes = this.staff_month.votes;
        }
    },
}
</script>
