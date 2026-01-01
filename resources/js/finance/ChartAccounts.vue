<template>
<section class="overlay-wrapper p-0">

    <div class="row">
        <div class="col-md-12">
            <div class="sticky-top mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Chart of Accounts</h4>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 550px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary mr-1" @click="getAllInitials()"><i class="fas fa-search"></i></button>
                                    <select class="form-control" v-model="source" @change="getAllInitials()">
                                        <option value="all">All</option>
                                        <option value="active">Active</option>
                                        <option value="deactivated">Deactivated</option>
                                    </select>
                                    <button type="button" class="btn btn-primary ml-1" @click="addChartAccount()"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-success ml-1" @click="uploadChartAccount()"><i class="fa fa-upload"></i></button>
                                    <button type="button" class="btn btn-info ml-1" @click="downloadChartAccount()"><i class="fa fa-download"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <FinanceDetailChartAccountList :chart_accounts="chart_accounts.data" @refreshChartAccounts="getAllInitials" />
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
            returnss: {data:[]},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
            returns: {},
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addChartAccount(){
            this.loading = true;
            this.returns = {};
            $('#returnsModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#returnsModal').modal('hide');
        },
        downloadChartAccounts(){},
        getAllInitials(page = 1) {
            this.loading = true 
            axios.get('/api/finances/chart_accounts?page='+page+'&status='+this.source+'&search='+this.query)
            .then(response => {
                this.refreshPage(response);
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Chart of Accounts loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Chart of Accounts were not loaded successfully',
                })
            });
        },
        refreshPage(response) {
            this.returnss = response.data.returnss;
            this.closeModals();
        },
        uploadChartAccounts(){
            alert("Coming Soon");
        },
    },
}
</script>