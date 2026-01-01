<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="employeeFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Update Employee Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployee :editMode="true" :employee.sync="employee" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" :src="'/img/profile/'+(employee.user != null ? employee.user.image : 'default.png')" :alt="FullName(employee.user)" :title="FullName(employee.user)">
            </div>
            <h3 class="profile-username text-center">{{FullName(employee.user)}}</h3>
            <p class="text-muted text-center">{{ employee.designation != null ? employee.designation.name : 'Staff' }}</p>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Department:</b> <a class="float-right">{{ employee.department != null ? employee.department.name : 'Unassigned'}}</a></li>
                <!--li class="list-group-item"><b>Friends:</b> <a class="float-right">13,287</a></li-->
                <li class="list-group-item"><b>Supervisor:</b> <a class="float-right">{{ employee.supervisor != null ? FullName(employee.supervisor.user) : 'Unassigned'}}</a></li>
                <li class="list-group-item"><b>Line Manager:</b> <a class="float-right">{{ (employee.line_manager != null && employee.line_manager.user != null) ? FullName(employee.line_manager.user) : 'Unassigned'}}</a></li>
            </ul>

            <!--button @click="updateEmployee" href="#" class="btn btn-primary btn-block"><b>Follow</b></button-->
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/employees/me')
            .then(response =>{
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
    props:{
        employee: Object,
    }
}
</script>