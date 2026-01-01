<template>
    <section class="container-fluid row p-0 overlay-wrapper">
        <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="col-md-3"></div>
        <div class="col-md-9">
            <HrmsDetailAssignedEmployeeLeaveType source="staff" :assigned_leave_types="user_leave_types.data" />
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