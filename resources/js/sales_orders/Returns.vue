<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="returnsModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Create New Quote</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <SalesFormReturn :editMode="editMode" :return_order_id.sync="return_order_id" @returnFormRefresh="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Returns</h3>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 500px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary mr-1" @click="getAllInitials()"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="source" @change="getAllInitials()">
                                    <option value="awaiting">Awaiting Approval</option>
                                    <option value="active">Active</option>
                                    <option value="approved">Approved</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="all">All</option>
                                </select>
                                <button type="button" class="btn btn-primary ml-1" @click="addReturn()"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success ml-1" @click="uploadReturns()"><i class="fa fa-upload"></i></button>
                                <button type="button" class="btn btn-info ml-1" @click="downloadReturns()"><i class="fa fa-download"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <SalesDetailReturnList :return_orders.sync="returns.data" source="returns" @returnOrderReload="getAllInitials" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="returns.per_page != null ? returns.per_page : 52" :records="returns.total != null ? returns.total : 550" ></pagination>
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
            return_order_id: 0,
            returns: {data:[], total: 0,},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addReturn(){
            this.loading = true;
            this.returns = {};
            $('#returnsModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#returnsModal').modal('hide');
        },
        downloadReturns(){},
        getAllInitials() {
            this.loading = true 
            axios.get('/api/sales/returns?page='+this.current_page+'&status='+this.source+'&search='+this.query)
            .then(response => {
                this.refreshPage(response);
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Returns loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Returns were not loaded successfully',
                })
            });
        },
        refreshPage(response) {
            this.returns = response.data.returns;
            this.closeModals();
        },
        uploadReturns(){
            alert("Coming Soon");
        },
    },
}
</script>
