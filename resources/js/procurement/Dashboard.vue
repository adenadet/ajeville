<template>
<section>
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{(purchase_orders.total ?? 0) + (work_orders.total ?? 0)}}</h3><p>All Orders</p></div>
                <div class="icon"><i class="fa fa-copy"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{purchase_orders.total ?? 0}}</h3><p>Purchase Orders</p></div>
                <div class="icon"><i class="fa fa-stats-bars"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary">
                <div class="inner"><h3>{{work_orders.total ?? 0}}</h3><p>Work Orders</p></div>
                <div class="icon"><i class="fa fa-person-add"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-purple">
                    <h3 class="card-title">Ongoing Purchase Orders</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <ProcurementDetailPurchaseOrderList :purchase_orders="purchase_orders.data" source="dashboard" ty="All" />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-purple">
                    <h3 class="card-title">Ongoing Work Orders</h3>
                </div>
                <div class="card-body table-responsive p-0"style="height: 300px;">
                    <ProcurementDetailWorkOrderList :work_orders="work_orders.data" source="dashboard" ty="All" />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import ProcurementDetailPurchaseOrderList from './details/PurchaseOrderList.vue';
import ProcurementDetailWorkOrderList from './details/WorkOrderList.vue';
export default {
    components:{ProcurementDetailPurchaseOrderList, ProcurementDetailWorkOrderList},
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            purchase_orders: {},        
            ongoing_purchase_orders: {},
            pending_purchase_orders: {},
            work_orders: {data:[],},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addVendor(){
            this.loading = true;
            this.editMode = false;
            this.vendor = {};
            //Fire.$emit('StoreDataFill', {});
            $('#vendorFormModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#vendorFormModal').modal('hide');
            $('#vendorModal').modal('hide');
        },
        deleteVendor(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/inventory/stores/'+id)
                    .then(response=>{
                        this.$emit('storeReload', response);  
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/dashboard')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Dashboard loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.purchase_orders         = response.data.purchase_orders;
            this.work_orders         = response.data.work_orders;
        },
        updateVendor(vendor){
            this.loading = true;
            this.editMode = true;
            this.vendor = vendor;
            $('#vendorFormModal').modal('show');
            this.loading = false;         
        },
    },
}
</script>
