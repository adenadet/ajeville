<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">Nominations for {{ ExcelMonthYear(staff_month.month) }}</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped">
                <thead class="bg-dark">                  
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Staff</th>
                        <th>Branch </th>
                        <th style="width: 40px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(nomination, index) in nominations" :key="nomination.id">
                        <td>{{ addOne(index)  }}</td>
                        <td>{{ FullName(nomination.nominee) }} <br /><span class="text-muted">{{nomination.nominee.department != null ? nomination.user.department.name : '' }}</span></td>
                        <td>{{ nomination.branch != null ? nomination.branch.name : 'Not Specified' }}</td>
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
            nominations: {},
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
            this.nominations = this.staff_month.nominations;
        }
    },
}
</script>
