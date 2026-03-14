<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="approvalFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Approve Purchase Order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormPurchaseOrderApproval :purchase_order.sync="purchase_order" @refreshPurchaseOrderApproval="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignStoreFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Store</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormAssignStore :purchase_order.sync="purchase_order" :editMode.sync="editMode" @refreshAssignStore="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignVendorFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Vendor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormAssignVendor item_type="purchase_order" :item.sync="purchase_order" :editMode.sync="editMode" @refreshAssignVendor="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="purchaseDetailModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Purchase Order Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementDetailPurchaseOrder :purchase_order.sync="purchase_order_detailed" :source="source" @purchaseOrderReload="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed table-striped  text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Store</th>
                <th>PO Number</th>
                <th>PO Date</th>
                <th>PO Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="purchase_orders.length > 0">
            <tr v-for="(purchase_order, index) in purchase_orders" :key="index">
                <td>{{ addOne(index) }}</td>
                <td>{{ purchase_order.vendor != null ? purchase_order.vendor.name : 'Not Assigned'  }}</td>
                <td>{{ purchase_order.store != null ? purchase_order.store.name : 'Not Assigned' }}</td>
                <td>{{ purchase_order.unique_id }}</td>
                <td>{{ ExcelDate(purchase_order.date) }}</td>
                <td>{{ purchase_order.status == 0 ? 'Drafts' : (purchase_order.status == 1 ? 'Awaiting Approval' : (purchase_order.status == 2 ? 'Approved': (purchase_order.status == 10 ? 'Completed': (purchase_order.status == 100 ? 'Rejected': (purchase_order.status == 1000 ? 'Deleted': 'Ongoing')))))}}</td>
                <td>
                    <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu" v-if="source == 'admin'">
                        <router-link :to="'/procurement/purchase_orders/'+purchase_order.id" class="btn btn-block dropdown-item" ><i class="fa fa-eye mr-1"></i> View Purchase Order</router-link>
                        <button type="button" class="btn btn-block dropdown-item" @click="submitForApproval(purchase_order.id)" v-if="purchase_order.status < 1 && purchase_order.store_id != null && purchase_order.vendor_id != null" ><i class="fa fa-check mr-1 text-success"></i> Submit for Approval</button>
                        <button type="button" class="btn btn-block dropdown-item" @click="assignVendor(purchase_order)"><i class="fa fa-user-tag text-purple mr-1"></i> Assign Vendor </button>
                        <button type="button" class="btn btn-block dropdown-item" @click="changeStore(purchase_order)"><i class="fa fa-home text-purple mr-1"></i> Change Store </button>
                        <button type="button" v-if="purchase_order.status <= 1" class="btn btn-block dropdown-item" @click="cancelPurchaseOrder(purchase_order.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Work Order </button>
                    </div>
                    <div class="dropdown-menu" v-else-if="source == 'approvals'">
                        <button type="button" @click="viewPurchaseOrder(purchase_order.id)" class="btn btn-block dropdown-item" ><i class="fa fa-eye mr-1 text-primary"></i> View Purchase Order</button>
                        <button type="button" v-if="purchase_order.status <= 1" @click="approvePurchaseOrder(purchase_order)" class="btn btn-block dropdown-item" ><i class="fa fa-check mr-1 text-success"></i> Approve Purchase Order</button>
                        <button type="button" v-if="purchase_order.status <= 1" class="btn btn-block dropdown-item" @click="cancelPurchaseOrder(purchase_order.id)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Work Order </button>
                    </div>
                    <div class="dropdown-menu" v-else>
                        <router-link :to="'/procurement/purchase_orders/'+purchase_order.id" class="btn btn-block dropdown-item" ><i class="fa fa-eye mr-1"></i> View Purchase Order</router-link>
                        <button type="button" class="btn btn-block dropdown-item" @click="submitForApproval(purchase_order.id)" v-if="purchase_order.status < 1"><i class="fa fa-check mr-1 text-warning"></i> Submit for Approval</button>
                        <button type="button" class="btn btn-block dropdown-item" @click="assignVendor(purchase_order)" v-if="purchase_order.status <= 1"><i class="fa fa-user-tag text-purple mr-1"></i> Assign Vendor </button>
                        <button type="button" class="btn btn-block dropdown-item" @click="changeStore(purchase_order)"v-if="purchase_order.status <= 1"><i class="fa fa-home text-purple mr-1"></i> Change Store </button>
                        <button type="button" v-if="purchase_order.status <= 1" class="btn btn-block dropdown-item" @click="cancelPurchaseOrder(purchase_order.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Work Order </button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="7">No Purchase Order meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import ProcurementDetailPurchaseOrder from '@/procurement/details/PurchaseOrder.vue';
import ProcurementFormAdditionalCost from '@/procurement/forms/AdditionalCost.vue';
import ProcurementFormAssignStore from '@/procurement/forms/AssignStore.vue';
import ProcurementFormAssignVendor from '@/procurement/forms/AssignVendor.vue';
import ProcurementFormPurchaseOrderApproval from '@/procurement/forms/PurchaseOrderApproval.vue';
import ProcurementFormPurchaseOrder from '@/procurement/forms/PurchaseOrder.vue';
export default {
    components:{
        ProcurementDetailPurchaseOrder, ProcurementFormAdditionalCost, ProcurementFormAssignStore, ProcurementFormAssignVendor, ProcurementFormPurchaseOrderApproval, ProcurementFormPurchaseOrder
    },
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            purchase_order: {},
            purchase_order_detailed: {},
            query: '',
        }
    },
    emits:['refreshPurchaseOrderList'],
    mounted() {},
    methods: {
        approvePurchaseOrder(purchase_order){
            this.loading = true;
            this.purchase_order = purchase_order;
            $('#approvalFormModal').modal('show');
            this.loading = false;
        },
        assignVendor(purchase_order){
            this.loading = true;
            this.purchase_order = purchase_order;
            $('#assignVendorFormModal').modal('show');
            this.loading = false;
        },
        cancelPurchaseOrder(id) {
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
                if (result.value) {
                    this.form.delete('/api/procurement/purchase_orders/'+id)
                    .then(response => {
                        this.$emit('refreshPurchaseOrderList', response);
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        changeStore(purchase_order){
            this.loading = true;
            console.log(purchase_order);
            this.purchase_order = purchase_order;
            $('#assignStoreFormModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#assignStoreFormModal').modal('hide');
            $('#assignVendorFormModal').modal('hide');
            $('#purchaseOrderFormModal').modal('hide');
        },
        deletePurchaseOrder(id) {
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
                if (result.value) {
                    this.form.delete('/api/procurement/purchase_orders/' + id)
                    .then(response => {
                        this.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        getInitials(){
            this.closeModals();
            this.$emit('refreshPurchaseOrderList');
        },
        submitForApproval(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Submit Purchase Order!'
            })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/procurement/purchase_orders/submit/'+id)
                    .then(response=>{
                        this.$swal.fire('Submitted!', 'Purchase Order has been submitted for approval.', 'success');
                        this.getInitials()
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewPurchaseOrder(id){
            this.loading = true;
            axios.get("/api/procurement/purchase_orders/"+id)
            .then((response) => {
                this.purchase_order_detailed = response.data.purchase_order;
                $('#purchaseDetailModal').modal('show');
            })
            .catch(() => {
                this.$toast.fire({
                    icon: "error",
                    title: "Purchase Order is not valid",
                });
            });
            this.loading = false;
        },
    },
    props:{
        purchase_orders: Array,
        source: String,
        ty: String,
        view: String,
    },
    watch:{
        purchase_orders(){}
    },
}
</script>