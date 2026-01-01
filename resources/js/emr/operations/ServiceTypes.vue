<template>
<section class="container-fluid">
<div class="row">
    <div class="modal fade" id="serviceTypeFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Service Type Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <OperationFormServiceType :service_type.sync="service_type" :editMode="editMode" @refreshDepartmentForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Service Types</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                        <table class="table table-head-fixed text-nowrap table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Queueable</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th><button class="btn btn-xs btn-primary" @click="addServiceType"><i class="fa fa-plus mr-1"></i> Add</button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(st, index) in service_types.data" :key="service_type.id">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ st.name }}</td>
                                <td>{{ st.queueable == 1 ? 'Yes' : 'No' }}</td>
                                <td>{{ st.status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td v-html="st.description"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="service_types.per_page != null ? service_types.per_page : 52" :records="service_types.total != null ? service_types.total : 550" ></pagination>
                    </div>
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
            loading: false,
            query: '',
            service_type: {},
            service_types: {data: [], total: 0, per_page: 20},
        };
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addServiceType(){
            this.loading = true;
            this.editMode = false;
            this.service_type = {};
            $('#serviceTypeFormModal').modal('show');
            this.loading = false;
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/service_types?page='+this.current_page+'&query='+this.query).then(response => {
                this.loading = false;
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'The Service Types did not load successfully',
                })
            });
        },
        refreshPage(response) {this.service_types = response.data.service_types;},
        updateServiceType(service_type){
            this.loading = true;
            this.editMode = false;
            this.service_type = service_type;
            $('#serviceTypeFormModal').modal('show');
            this.loading = false;
        },
    },
}
</script>