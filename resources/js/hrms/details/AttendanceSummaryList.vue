<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Date</th>
                <th v-if="source != 'employee'">Employee</th>
                <th>Shift </th>
                <th>Status </th>
                <th>Clock In </th>
                <th>Clock Out </th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="schedules.length > 0">
            <tr v-for="schedule in schedules" :key="schedule.id">
                <td>{{ ExcelDate(schedule.date) }}</td>
                <td v-if="source != 'employee'">{{ schedule.employee != null ? FullName(schedule.employee.user) : 'Old Staff' }}</td>
                <td>{{ schedule.shift != null ? schedule.shift.name : 'Old Shift' }}</td>
                <td v-if="schedule.clock_in != null"><i class="fa fa-check"></i></td>
                <td v-else-if="schedule.clock_in != null"><i class="fa fa-times"></i></td>
                <td v-else></td>
                <td>{{ schedule.clock_in != null ? ExcelTime(schedule.clock_in) : '' }}</td>
                <td>{{ schedule.clock_out != null ? ExcelTime(schedule.clock_out) : '' }}</td>
                <td><button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'employee'">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewSchedule(schedule)"><i class="fa fa-eye mr-1 text-primary"></i> View Employee</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateSchedule(schedule)"><i class="fa fa-edit mr-1 text-warning"></i> Update Record</button>
                        <button v-if="source == 'admin'" class="dropdown-item btn btn-block btn-sm" @click="deleteSchedule(schedule.id)"><i class="fa fa-trash mr-1 text-danger"></i> Update Record</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr><td colspan=5>No Shift was assigned</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            loading: false,
            schedule: {},
        }
    },
    emits: ['refreshSchedulesPage'],
    methods:{
        closeModals(){
            $('#scheduleModal').modal('hide');
            $('#scheduleFormModal').modal('hide');
        },
        deleteSchedule(id){
            this.$swal.fire({
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
                    this.loading = true;
                    this.form.delete('/api/hrms/attendance_summaries/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        updateSchedule(schedule){
            this.schedule = schedule;
            this.editMode = true;
            $('#scheduleFormModal').modal('show');
        },
        refreshPage(response){
            this.$emit('refreshSchedulesPage');
            this.closeModals();
        },
        viewSchedule(schedule){
            this.schedule = schedule;
            $('#scheduleModal').modal('show');
        }
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        schedules: Array,
        source: String,
        style: String,
    }
}
</script>