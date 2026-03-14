<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Sales Orders</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append"><button type="button" class="btn btn-default" @click="getAllInitials"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <SalesDetailOrderList :orders.sync="orders.data" view="approvals" @salesOrderReload="getAllInitials" />
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="orders.per_page != null ? orders.per_page : 52" :records="orders.total != null ? orders.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import SalesDetailOrderList from '@/sales_orders/details/OrderList.vue';
export default {
    components:{
        SalesDetailOrderList
    },
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form_type: '',
            loading: false,
            order: {},
            orders: {data:[],},
            query: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        approveOrder(order){
            this.loading = true;
            this.editMode = true;
            this.form_type = "accept";
            $('#orderModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#orderModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/approvals/sales_orders?type=unapproved&page='+this.current_page+'&query='+this.query)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Sales Orders loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transfer Orders not loaded successfully',
                })
            });
        },
        issueRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "issue";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        refreshPage(response){
            this.orders = response.data.orders;
        },
    },
}
</script>