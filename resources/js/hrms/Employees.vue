<template>
<section class="contain-fluid">
    <div class="modal fade" id="employeeModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Employee Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployee :editMode.sync="editMode" :employee.sync="employee" @refreshPage="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uploadModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Upload Employees </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeImport :editMode.sync="editMode" @refreshPage="getAllInitials(1)"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card overlay-wrapper p-0">
                <div class="card-header bg-navy">
                    <h3 class="card-title">{{source == 0 ? 'Inactive' : (source == 1 ? 'Active' : (source == 2 ? 'Resigned' : (source == 3 ? 'Terminated' : (source == 4 ? 'Deceased' : (source == 5 ? 'Retired' : 'All')))))}} Employees</h3>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 400px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary mr-1" @click="searchEmployee"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="source" @change="getAllInitials(1)">
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                    <option value="2">Resigned</option>
                                    <option value="3">Terminated</option>
                                    <option value="4">Deceased</option>
                                    <option value="5">Retired</option>
                                    <option value="all">All</option>
                                </select>
                                <button type="button" class="btn btn-primary ml-1" @click="addEmployee"><i class="fa fa-user-plus"></i></button>
                                <button type="button" class="btn btn-success ml-1" @click="uploadEmployees"><i class="fa fa-upload"></i></button>
                            </div>
                        </div>    
                    </div>
                </div>
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <HrmsDetailEmployeeList :employees.sync="employees.data" :source="source" @refreshPage="getAllInitials(current_page)"/>
                <div class="card-footer bg-navy">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="employees.per_page != null ? employees.per_page : 52" :records="employees.total != null ? employees.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            editMode: false,
            employee:{},
            employees: {},
            form: new Form({}),
            loading: false,
            query: '',
            source: '1',
        }
    },
    methods:{
        addEmployee(){
            this.editMode = false;
            this.employee = {};
            $('#employeeModal').modal('show');
        },
        closeModals(){
            $('#employeeModal').modal('hide'); 
            $('#uploadModal').modal('hide'); 
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
            axios.get('/api/hrms/employees?page='+page+'&source='+this.source).then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Employees not loaded successfully',})
            });
        },
        refreshPage(response){
            this.employees = response.data.employees;
            this.closeModals();
        },
        searchEmployee(){
            axios.get('/api/hrms/employees/search/'+this.query)
            .then((response ) => {this.refreshPage(response);})
            .catch(()=>{});
        },
        uploadEmployees(){
            this.editMode = false;
            this.employee = {};
            $('#uploadModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>