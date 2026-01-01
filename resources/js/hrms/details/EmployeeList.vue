<template>
<section>
    <div class="modal fade" id="employeeFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Update Employee Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployee :editMode.sync="editMode" :employee.sync="employee" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="employeeStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Change Employee Status</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeStatus :editMode.sync="editMode" :employee.sync="employee" @refreshPage="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="leaveTypeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Leave Type</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeLeaveType :editMode.sync="editMode" :employee.sync="employee" @refreshPage="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 600px;">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <table class="table table-hover table-head-fixed table-striped text-nowrap">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Supervisor</th>
                    <th>Line Manager</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="employee in employees" :key="employee.id">
                    <td>{{ employee.username }}</td>
                    <td>{{ FullName(employee.user) }}</td>
                    <td>{{ employee.department != null ? employee.department.name : "No Department" }}</td>
                    <td>{{ employee.designation != null ? employee.designation.name : "No Designation" }}</td>
                    <td>{{ employee.supervisor != null ? FullName(employee.supervisor.user) : 'No Supervisor Assigned Yet' }}</td>
                    <td>{{ employee.line_manager != null ? FullName(employee.line_manager.user) : 'No Supervisor Assigned Yet'  }}</td>
                    <td>{{ employee.employment_status != null ? (employee.employment_status == 0 ? 'Inactive' : (employee.employment_status == 1 ? 'Active' : (employee.employment_status == 2 ? 'Resigned' : (employee.employment_status == 3 ? 'Terminated' : (employee.employment_status == 4 ? 'Deceased' : (employee.employment_status == 5 ? 'Retired' : 'Undefined')))))): 'Undefined'}}</td>
                    <td>{{ ExcelDate(employee.date_of_joining) }} {{(employee.date_of_leaving != null && employee.date_of_joining != '') ? ' - '+ExcelDate(employee.date_of_leaving) : ' Till Now'  }}</td>
                    <td><button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'/hrms_admin/employees/'+employee.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Employee</button></router-link>
                            <button class="dropdown-item btn btn-block btn-sm" @click="assignLeaveType(employee)"><i class="fa fa-user-tag mr-1 text-success"></i> Assign Leave Type</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="modifyEmployee(employee)"><i class="fa fa-edit mr-1 text-success"></i> Update Record</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="updateEmployeeStatus(employee)"><i class="fa fa-user mr-1 text-danger"></i> Change Employee Status</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            areas:[],
            branches:[],
            departments:[],
            editMode: false,
            employee: {},
            form: new Form({}),
            loading: false,
            query: '',
            savings:{},
            states:[],
            employee:{},
            users:{},
        }
    },
    emits: ['refreshPage'],
    methods:{
        addUser(){
            this.editMode = false;
            this.user = {};
            $('#userModal').modal('show');
        },
        assignLeaveType(employee){
            //this.editMode = false;
            this.employee = employee;
            $('#leaveTypeModal').modal('show');
        },
        closeModals(){
            $('#employeeFormModal').modal('hide');
            $('#employeeStatusModal').modal('hide');
            $('#uploadModal').modal('hide');
            $('#userModal').modal('hide'); 
            $('#roleModal').modal('hide');
        },
        deleteUser(id){
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
                    this.form.delete('/api/ums/staffs/'+id)
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
        getAllInitials(page=1){
            this.loading = true
            axios.get('/api/ums/staffs?page='+page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Users not loaded successfully',})
            });
        },
        modifyEmployee(employee){
            this.employee = employee;
            this.editMode = true;
            $('#employeeFormModal').modal('show');
        },
        refreshPage(response){
            this.$emit('refreshPage', response);
            this.closeModals();
        },
        searchEmployee(){
            axios.get('/api/hrms/employees/search/'+this.query)
            .then((response ) => {this.refreshPage(response);})
            .catch(()=>{});
        },
        setUserRole(user){
            this.user = user;
            this.editMode = true;
            $('#roleModal').modal('show');
        },
        updateEmployeeStatus(employee){
            this.employee = employee;
            $('#employeeStatusModal').modal('show');
        },
        uploadEmployees(){
            this.editMode = false;
            this.employee = [];
            $('#uploadModal').modal('show');
        }
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        employees: Array,
        source: String,
    }
}
</script>