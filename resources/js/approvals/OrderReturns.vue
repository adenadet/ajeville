<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Sales Order Returns</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <SalesDetailReturnList :return_orders.sync="return_orders.data" source="approvals" @returnOrderReload="getAllInitials" />
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="return_orders.per_page != null ? return_orders.per_page : 52" :records="return_orders.total != null ? return_orders.total : 550" ></pagination>
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
            return_order_id: 0,
            return_orders: {data:[], total: 0,},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
            source: 'unapproved',
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
            this.return_orders = response.data.returns;
            this.closeModals();
        },
        uploadReturns(){
            alert("Coming Soon");
        },
    },
}
</script>
