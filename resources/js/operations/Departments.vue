<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Departments</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                            <button type="submit" class="btn btn-default" @click="getInitials"><i class="fas fa-search"></i></button>
                            <!--button type="button" class="btn btn-primary ml-1" @click="addDepartment"><i class="fas fa-plus"></i></button-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <OperationDetailDepartmentList :departments.sync="departments.data" :loading="loading" @refreshDepartments="getInitials" />
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="departments.per_page != null ? departments.per_page : 52" :records="departments.total != null ? departments.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            department: {},
            departments: {data: [], total: 0, per_page: 20},
            loading: false,
            query: '',
        };
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/departments?page='+this.current_page+'&query='+this.query).then(response => {
                this.loading = false;
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'The Departments did not load successfully',
                })
            });
        },
        refreshPage(response) {this.departments = response.data.departments;},
    },
}
</script>