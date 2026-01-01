<template>
<section class="overlay-wrapper">
    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Shift </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="schedule in schedules" :key="schedule.id">
                <td>{{ ExcelDate(schedule.date) }}</td>
                <td>{{ schedule.employee != null ? FullName(schedule.employee.user) : 'Old Staff' }}</td>
                <td>{{ schedule.shift != null ? schedule.shift.name : 'Old Shift' }}</td>
                <td><button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'/hrms/admin/employees/'+employee.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Employee</button></router-link>
                        <button class="dropdown-item btn btn-block btn-sm" @click="assignLeaveType(employee)"><i class="fa fa-user-tag mr-1 text-success"></i> Assign Leave Type</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="modifyEmployee(employee)"><i class="fa fa-edit mr-1 text-success"></i> Update Record</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateEmployeeStatus(employee)"><i class="fa fa-user mr-1 text-danger"></i> Change Employee Status</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
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
        modifySchedule(schedule){
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