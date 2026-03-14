<template>
<section class="content">
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Work Orders</h3>
            <div class="card-tools">
                <div class="input-group input-group" style="width: 300px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary mr-1" @click="searchWorkOrder"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <ProcurementDetailWorkOrderList :editMode="false" ty="service" source="admin" :work_orders="work_orders.data"/>
        <div class="card-footer bg-navy">
            <div class="col-12">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="work_orders.per_page != null ? work_orders.per_page : 52" :records="work_orders.total != null ? work_orders.total : 550" ></pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import ProcurementDetailWorkOrderList from '@/procurement/details/WorkOrderList.vue';
export default {
    components:{ProcurementDetailWorkOrderList},
    data(){
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
            work_order: {},
            work_orders: {data: [],},
        }
    },
    methods:{
        closeModals(){
            $('#workOrderDetailModal').modal('hide'); 
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/procurement/work_orders?status=1&page='+page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Work Orders loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Work Orders not loaded successfully',})
            });
        },
        refreshPage(response){
            this.work_orders = response.data.work_orders;
            this.closeModals();
        },
        searchWorkOrder(){},
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>