<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ active_leaves }}</h3>
                    <p>Active Leave</p>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar-alt"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box">
                <div class="inner">
                    <h3>{{ pending_leave_allowances }}</h3>
                    <p>Pending Leave Allowances</p>
                </div>
                <div class="icon">
                    <i class=" ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{staff_strength}}</h3>
                    <p>Staff Strength</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ pending_applications }}</h3>
                    <p>Pending Applications</p>
                </div>
                <div class="icon">
                    <i class="fa fa-file-signature"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ staff_requests }}</h3>
                    <p>Staff Requests</p>
                </div>
                <div class="icon">
                    <i class="fa fa-file-signature"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ pending_resignations }}</h3>
                    <p>Pending Resignations</p>
                </div>
                <div class="icon">
                    <i class="fa fa-file-signature"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">New Employees</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Date of Joining</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="employee in new_employees.data" :key="employee.id">
                                <td>{{ FullName(employee.user) }}</td>
                                <td>{{ employee.designation != null ? employee.designation.name : 'Not Assigned'  }}</td>
                                <td>{{ employee.department != null ? employee.department.name : 'Not Assigned' }}</td>
                                <td>{{ ExcelDate(employee.date_of_joining) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
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
            active_leaves: 0,
            attendance_summaries: [],
            current_page: 1,
            loading: false,
            new_employees: {},
            pending_applications: 0,
            pending_leave_allowances: 0,
            pending_resignations: 0,
            staff_requests: 0,
            staff_strength: 0,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/dashboard/admin').then(response =>{
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