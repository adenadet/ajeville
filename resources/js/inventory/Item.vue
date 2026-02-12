<template>
<section>
    <div class="modal fade" id="itemFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Item: {{item.name}} [{{item.unique_id}} ]</h4>
                    <h4 class="modal-title" v-show="!editMode">New Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormItem :editMode="editMode" :item.sync="item" @itemReload="reloadPage"/> 
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Item Details</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link">Name <span class="float-right">{{ item.name }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">Item Type <span class="float-right">{{ item.item_type != null ? item.item_type.name : 'N/A' }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">Current Cost Price <span class="float-right">{{ currency(item.last_landing_cost)  }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                Status<span class="float-right badge" :class="item.status == 'active' || item.status == 'Active' ? 'badge-primary' : 'badge-danger'">{{  firstUp(item.status) }}</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav flex-column border-top" v-if="item.item_type != null && item.item_type.name == 'Services'">
                            <li class="nav-item">
                                <a href="#" class="nav-link">Service Type <span class="float-right">{{ item.service != null && item.service.service_type != null? item.service.service_type.name : 'N/A' }}</span></a>
                            </li>
                        </ul>
                        <ul class="nav flex-column border-top">
                            <li class="nav-item">
                                <a href="#" class="nav-link">Category <span class="float-right">{{ item.category != null ? item.category.name : 'N/A' }}</span></a>
                            </li>
                        </ul>
                        <div class="row mt-2 p-3">
                            <button class="btn btn-primary col-12" type="button" @click="editItem(item)">Change Details</button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Store Levels</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Store Name</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="location in locations" :key="location.id">
                                    <td>{{ location.store_item?.store?.name || 'Warehouse' }}</td>
                                    <td>{{ location.balance }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Recent Transfers</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Identification</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transfer_order in transfer_orders" :key="transfer_order.id">
                                    <td>{{ excelShortDate(transfer_order.created_at)  }}</td>
                                    <td>{{ transfer_order.unique_id }}</td>
                                    <td>{{ transfer_order.from_store.name }}</td>
                                    <td>{{ transfer_order.to_store.name }}</td>
                                    <td>{{ transfer_order.quantity }}</td>
                                    <td>{{ transfer_order.status }}</td>
                                    <td><button class="btn btn-sm btn-tool text-dark"><i class="fa fa-eye mr-1"></i> View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Recent Procurements Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="purchase_order in purchase_orders.data" :key="purchase_order.id">
                                    <td>{{ purchase_order.date }}</td>
                                    <td>{{ purchase_order.name }}</td>
                                    <td>{{ purchase_order.quantity }}</td>
                                    <td>{{ purchase_order.status}}</td>
                                    <td><button class="btn btn-sm btn-tool text-dark"><i class="fa fa-eye mr-1"></i> View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">Recent Sales Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="sales_order in sales_orders.data" :key="sales_order.id">
                                    <td>{{ sales_order.name }}</td>
                                    <td>{{ sales_order.date }}</td>
                                    <td>{{ sales_order.quantity }}</td>
                                    <td>{{ sales_order.status == 1 ? 'Active' : 'Inactive' }}</td>
                                    <td><button class="btn btn-sm btn-tool text-dark"><i class="fa fa-eye mr-1"></i> View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            item: {},
            loading: false,
            locations: [],
            purchase_orders: [],
            sales_orders: [],
            transfer_orders: [],
            store_item_setting: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        closeModals(){
            $('#itemFormModal').modal('hide');
            $('#storeItemSettingFormModal').modal('hide');
        },
        editItem(){
            this.editMode = true;
            $('#itemFormModal').modal('show');
        },
        editStoreItemSettings(){
            $('#storeItemSettingFormModal').modal('show');
        },
        getInitials(page=1){
            this.loading = true;
            axios.get('/api/inventory/items/'+this.$route.params.id)
            .then(response =>{
                this.refreshPage(response);
                
                this.$toast.fire({
                    icon: 'success',
                    title: 'Item Details loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Item Details not loaded successfully',
                })
            });
            this.loading = false;
        },
        reloadPage(){
            this.closeModals();
            this.getInitials();
        },
        refreshPage(response){
            this.item = response.data.item;
            this.transfer_orders = response.data.transfer_orders;
            this.locations = response.data.locations;
            this.purchase_orders = response.data.purchase_orders;
        },
    },
}
</script>