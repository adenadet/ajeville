<template>
<section class="container-fluid row p-0 overlay-wrapper">
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
        <HrmsDetailEmployeeLeaveType source="staff" :assigned_leave_types="user_leave_types.data" />
        <div class="card-footer bg-navy"><pagination v-model="current_page" @paginate="getAllInitials" :per-page="user_leave_types.per_page != null ? user_leave_types.per_page : 52" :records="user_leave_types.total != null ? user_leave_types.total : 550" ></pagination></div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            loading: false,
            user_leave_types: {
                data: [],
            },
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/employee_leave_types/assigned').then(response =>{
                this.reset(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Leave Types did not load successfully',});
            });
        }, 
        reset(response){
            this.employee = response.data.employee;
            this.user_leave_types = response.data.user_leave_types;
        }
    },
}
</script>