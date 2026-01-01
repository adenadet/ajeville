<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="departmentFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Department Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <OperationFormDepartment :department.sync="department" :editMode="editMode" @refreshDepartmentForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Phone no.</th>
                <th>Head of Department</th>
                <th>HOD Email</th>
                <th>HOD Phone</th>
                <th><button class="btn btn-xs btn-primary" @click="addDepartment"><i class="fa fa-plus mr-1"></i>Add</button></th>
            </tr>
        </thead>
        <tbody v-if="departments.length > 0">
            <tr v-for="(department, index) in departments" :key="department.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ department.name }}</td>
                <td>{{ department.ext }}</td>
                <td>{{ (department.hod != null && department.hod.user != null) ? FullName(department.hod.user) : department.hod_id+' Not Selected'}}</td>
                <td>{{ department.email}}</td>
                <td>{{ department.phone }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars text-dark"></i></button>
                    <div class="dropdown-menu">
                        <router-link v-if="source == 'emr_operations'" class="btn btn-block dropdown-item" :to="'/emr/operations/departments/'+department.id"><i class="fa fa-eye mr-1 text-dark"></i> View Department</router-link>
                        <router-link v-if="source == 'hr_admin'" class="btn btn-block dropdown-item" :to="'/hrms_admin/departments/'+department.id"><i class="fa fa-eye mr-1 text-dark"></i> View Department</router-link>
                        <router-link v-if="source == 'emr_operations'" class="btn btn-block dropdown-item" :to="'/operations/departments/'+department.id"><i class="fa fa-eye mr-1 text-dark"></i> View Department </router-link>
                        <button class="btn btn-block dropdown-item" @click="editDepartment(department)"><i class="fa fa-edit mr-1 text-primary"></i> Edit Department</button>
                        <button class="btn btn-block dropdown-item" @click="deleteDepartment(department.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Department</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="8">No Departments Found</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            department: {},
            editMode: false,
            loading: false,
        }
    },
    emits: ['refreshDepartments'],
    mounted() {},
    methods: {
        addDepartment(){
            this.loading = true;
            this.editMode = false;
            this.department = {};
            $('#departmentFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#departmentFormModal').modal('show');
        },
        deleteDepartment(id){
            //alert("Working")
        },
        editDepartment(department){
            this.loading = true;
            this.department = department;
            this.editMode = true;
            $('#departmentFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
            this.$emit('refreshDepartments');
        }
    },
    props: {
        departments: Array,
        source: String,
    }
}
</script>