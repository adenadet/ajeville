<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th v-if="source != 'employee'">Employee</th>
                <th>Date</th>
                <th>Time</th>
                <th>Source</th>
                <th v-if="source != 'employee'"></th>
            </tr>
        </thead>
        <tbody v-if="clock_ins.length > 0">
            <tr v-for="clock_in in clock_ins" :key="clock_in.id">
                <td v-if="source != 'employee'">{{ clock_in.employee != null && clock_in.employee.user != null ? FullName(clock_in.employee.user) : 'Old Staff' }}</td>
                <td>{{ ExcelDate(clock_in.clock_in_time) }}</td>
                <td>{{ ExcelTime(clock_in.clock_in_time) }}</td>
                <td>{{ clock_in.source }}</td>
                <td v-if="source != 'employee'">Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td :colspan="source != 'employee' ? 5 : 3">No Clock In have been set</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            attendance_summaries: [],
            current_page: 1,
            loading: false,
            employee: {},
            employee_leave_types: [],
            leave_types: [],
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/dashboard').then(response =>{
                this.reset(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Leave Types did not load successfully',});
            });
        }, 
        reset(response){
            this.attendance_summaries = response.data.attendance_summaries;
            this.employee = response.data.employee;
            this.employee_leave_types = response.data.employee_leave_types;
        }
    },
    props:{
        clock_ins: Array,
        source: String,
    }
}
</script>