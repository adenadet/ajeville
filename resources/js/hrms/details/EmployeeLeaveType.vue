<template>
<div class="row overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="col-md-3">
        <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-warning">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" :src="'/dist/img/user7-128x128.jpg'" alt="User Avatar">
                </div>
                <h3 class="widget-user-username">{{ leave_type.name }}</h3>
                <h5 class="widget-user-desc">{{ leave_type.status }}</h5>
            </div>
            <div class="card-footer p-0">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">Day Type: <span class="float-right badge bg-primary">31</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Start Date: <span class="float-right badge bg-info">5</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">End Date: <span class="float-right badge bg-success">12</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Followers <span class="float-right badge bg-danger">842</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card-header bg-navy">
            <h3 class="card-title">Employee Leave Assignment</h3>
            <div class="card-tools">
                
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Days Used</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(assigned_leave_type, index) in assigned_leave_types.data">
                        <td>{{ addOne(index) }}</td>
                        <td>{{ assigned_leave_type.employee != null ? FullName(assigned_leave_type.employee.user) : 'Not Found' }}</td>
                        <td>{{ assigned_leave_type.employee != null ? assigned_leave_type.employee.department.name : 'No Department' }}</td>
                        <td>{{assigned_leave_type.used}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-navy"><pagination v-model="current_page" @paginate="getAllInitials" :per-page="leave_types.per_page != null ? assigned_leave_types.per_page : 52" :records="assigned_leave_types.total != null ? assigned_leave_types.total : 550" ></pagination></div>
    </div>
</div>         
</template>
<script>
export default {
    data() {
        return {
            assigned_leave_types: {},
            current_page: 1,
            editMode: true,
            loading: false,
            leave_type: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        closeMOdal(){
            $('#leaveTypeModal').modal('hide');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/employee_leave_types/'+this.leave_type_id)
            .then(response => {
                this.refreshLeaveType(response); this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your Employee Leave Type did not loaded successfully',
                })
            });
        },
        refreshLeaveType(response) {
            this.assigned_leave_types = response.data.assigned_leave_types;
            this.closeModal();
        }
    },
    props: {
        leave_type_id: Number,
    },
    watch:{
        leave_type_id(){
            this.loading = true;
        }
    }
}
</script>