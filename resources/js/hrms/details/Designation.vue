<template>
    <section class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-navy">{{ designation.name }}</div>
                <div class="card-body">
                    <strong><i class="fas fa-book mr-1"></i> Department</strong>
                    <p class="text-muted">{{ designation.department != null ? designation.department.name : 'Not Applicable' }}</p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Unit</strong>
                    <p class="text-muted">{{ designation.unit != null ? designation.unit.name : 'Not Applicable' }}</p>
                    <hr>
                    <!--strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                    <p class="text-muted">
                    <span class="tag tag-danger">UI Design</span>
                    <span class="tag tag-success">Coding</span>
                    <span class="tag tag-info">Javascript</span>
                    <span class="tag tag-warning">PHP</span>
                    <span class="tag tag-primary">Node.js</span>
                    </p>

                    <hr-->

                    <strong><i class="far fa-file-alt mr-1"></i> Description</strong>
                    <p class="text-muted" v-html="designation.description"></p>
                
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-navy">
                    <h3 class="card-title">Employees with Designation</h3>
                </div>
                <div class="card-body overlay-wrapper table-responsive p-0" style="height: 400px;">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Period</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="employees.length != 0">
                            <tr  v-for="employee in employees" :key="employee.id">
                                <td>{{ employee.username }}</td>
                                <td>{{ FullName(employee.user) }}</td>
                                <td>{{ employee.department != null ? employee.department.name : "No Department" }}</td>
                                <td>{{ employee.employment_status != null ? 
                                    (employee.employment_status == 0 ? 'Inactive' : (employee.employment_status == 1 ? 'Active' : (employee.employment_status == 2 ? 'Resigned' : (employee.employment_status == 3 ? 'Terminated' : (employee.employment_status == 4 ? 'Deceased' : (employee.employment_status == 5 ? 'Retired' : 'Undefined')
                                    ))))): 'Undefined'}}
                                </td>
                                <td>{{ ExcelDate(employee.date_of_joining) }} {{(employee.date_of_leaving != null && employee.date_of_joining != '') ? ' - '+ExcelDate(employee.date_of_leaving) : ' Till Now'  }}</td>
                                <td>
                                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <router-link :to="'/hrms/admin/employees/'+employee.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Employee</button></router-link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6">There is no staff with this designation</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            employees: [],
            loading: false,
        }
    },
    emits:['reloadEmployeeLeaveTypes'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {},
    props: {
        designation: Object,
    },
    watch:{
        designation(){
            this.loading = true;
            this.employees = this.designation.employees;
            this.loading = false;
        }
    }
}
</script>