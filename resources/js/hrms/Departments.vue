<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="departmentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Department</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <OperationFormDepartment :department="department" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Departments</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            <button type="submit" class="btn btn-primary" @click="addDepartment"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <OperationDetailDepartmentList :departments="departments" @refreshDepartments="getAllInitials" source="hr_admin" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="departments.per_page != null ? departments.per_page : 52" :records="departments.total != null ? departments.total : 550" ></pagination>
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
            current_page: 1,
            department: {},
            departments: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addDepartment() {
            this.department = {};
            this.editMode = false;
            $('#departmentModal').modal('show');
        },
        deleteDepartment(id){
            alert("Working")
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/departments').then(response => {
                this.loading = false;
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'The departments did not load successfully',
                })
            });
        },
        refreshPage(response) {this.departments = response.data.departments;},
        updatePlan(){},
    },
    props: {}
}
</script>