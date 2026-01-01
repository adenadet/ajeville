<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Branches</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <OperationDetailBranchList :branches="branches" :loading="loading" @refreshBranches="getInitials" />
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="branches.per_page != null ? branches.per_page : 52" :records="branches.total != null ? branches.total : 550" ></pagination>
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
            branch: {},
            branches: {},
            current_page: 1,
            loading: false,
            query: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/branches').then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'The price list did not load successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {this.branches = response.data.branches;},
        updatePlan(){},
    },
    props: {}
}
</script>