<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-3">
            <HrmsDetailEmployeeCard :employee="employee" />
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary">Assigned Leave Types</div>
                <div class="card-body table-responsive p-0" style="height: 350px;">
                    <HrmsDetailEmployeeLeaveTypeList :employee_leave_types="employee_leave_types" source="employee" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Shifts</span>
                    <span class="info-box-number">1,410</span>
                </div>
            </div>
            <div class="info-box">
                <span class="info-box-icon"><i class="far fa-calendar text-info"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Attendance %</span>
                    <span class="info-box-number">99</span>
                </div>
            </div>
            <div class="info-box">
                <span class="info-box-icon"><i class="far fa-calendar-check text-primary"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Overtime</span>
                    <span class="info-box-number">1,410</span>
                </div>
            </div>
            <div class="info-box">
                <span class="info-box-icon"><i class="far fa-calendar-times text-danger"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Late/Absent Days</span>
                    <span class="info-box-number">1,410</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!--div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Last Month's Attendance</h4>
                </div>
                <div class="card-body p-0">
                    <HrmsDetailAttendanceSummaryList :schedules="attendance_summaries" source="employee" /> 
                </div>
            </div>   
        </div-->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title">This Month's Schedule</h4>
                </div>
                <div class="card-body p-0">
                    <HrmsDetailAttendanceSummaryList :schedules="attendance_summaries" source="employee" /> 
                </div>
            </div>   
        </div>
    </div>
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
        this.getAllInitials();
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
            //this.leave_requests = response.data.leave_requests;
        }
    },
}
</script>