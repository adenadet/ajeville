<template>
<section>
    <div class="modal fade" id="assignVendorFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Vendor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormAssignVendor item_type="work_order" :item.sync="work_order" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="otherCostFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Other Cost</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormOtherCost item_type="work_order" :item.sync="work_order" :editMode.sync="editMode" @refreshPage="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="workOrderDetailModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Work Order Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementDetailWorkOrder :work_order.sync="work_order" :source="source" @refreshPage="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap table-striped table-hover">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Vendor</th>
                <th>Department</th>
                <th>Work Order</th>
                <th>WO Date</th>
                <th>WO Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="work_orders.length > 0">        
            <tr v-for="(work_order, index) in work_orders" :key="index">
                <td>{{ addOne(index) }}</td>
                <td>{{ work_order.vendor != null ? work_order.vendor.name : 'Not Assigned'  }}</td>
                <td>{{ work_order.store != null ? work_order.store.name : 'Not Assigned' }}</td>
                <td>{{ (work_order.name!=null ? work_order.name  +' [': '')+ work_order.unique_id + (work_order.name!=null ?']': '') }}</td>
                <td>{{ ExcelDate(work_order.date) }}</td>
                <td>{{ work_order.status == 0 ? 'Drafts' : (work_order.status == 1 ? 'Awaiting Approval' : (work_order.status == 2 ? 'Approved': 'Ongoing'))}}</td>
                <td>
                    <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu" v-if="source != 'admin'">
                        <router-link :to="'/procurement/work_orders/'+work_order.id" class="btn btn-block dropdown-item" @click="addWorkOrderItem()"><i class="fa fa-eye mr-1"></i> View Work Order</router-link>
                        <button class="btn btn-block dropdown-item" @click="addWorkOrderItem()"><i class="fa fa-cart-plus mr-1"></i> Add New Item </button>
                        <button class="btn btn-block dropdown-item" @click="assignVendor()"><i class="fa fa-user-tag text-primary mr-1"></i> Assign Vendor </button>
                        <button class="btn btn-block dropdown-item" @click="changeReceiver()"><i class="fa fa-home text-warning mr-1"></i> Change Receiver </button>
                        <button class="btn btn-block dropdown-item" @click="updateOtherCost()"><i class="fa fa-edit text-success mr-1"></i> Update Other Cost </button>
                        <button class="btn btn-block dropdown-item" @click="cancelWorkOrder()"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Work Order </button>
                        <button class="btn btn-block dropdown-item" @click="submitForApproval()" v-if="work_order.status == 0"><i class="fa fa-check mr-1 text-warning"></i> Submit for Approval</button>
                    </div>
                    <div class="dropdown-menu" v-if="source == 'admin'">
                        <button class="btn btn-block dropdown-item" @click="viewWorkOrder()"><i class="fa fa-eye text-primary mr-1"></i> View Work Order </button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="7">No Work Order has been created</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import ProcurementDetailWorkOrder from '@/procurement/details/WorkOrder.vue';
import ProcurementFormAdditionalCost from '@/procurement/forms/AdditionalCost.vue';
import ProcurementFormOtherCost from '@/procurement/forms/OtherCost.vue';
import ProcurementFormAssignStore from '@/procurement/forms/AssignStore.vue';
import ProcurementFormAssignVendor from '@/procurement/forms/AssignVendor.vue';
import ProcurementFormPurchaseOrderApproval from '@/procurement/forms/PurchaseOrderApproval.vue';
import ProcurementFormPurchaseOrder from '@/procurement/forms/PurchaseOrder.vue';
export default {
    components:{
        ProcurementDetailWorkOrder, ProcurementFormAdditionalCost, ProcurementFormOtherCost, ProcurementFormAssignStore, ProcurementFormAssignVendor, ProcurementFormPurchaseOrderApproval, ProcurementFormPurchaseOrder
    },
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
            work_order: {},
        }
    },
    mounted() {},
    methods: {
        closeModals() {
            $('#storeModal').modal('hide');
        },
        deleteWorkOrder(id) {
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
                    this.form.delete('/api/procurement/work_orders/' + id)
                    .then(response => {
                        this.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'Work Order has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        assignVendor(){
            this.loading = true;
            this.editMode = false;
            $('#assignVendorFormModal').modal('show');  
            this.loading = false;
        },
        closeModal(){
            $('#assignVendorFormModal').modal('hide');  
            $('#orderItemFormModal').modal('hide');
            $('#otherCostFormModal').modal('hide');
            $('#workOrderItemFormModal').modal('hide');
        },
        searchWorkOrder(){
            this.loading = true;
            this.loading = false;
        },
        viewWorkOrder(work_order){
            this.work_order = work_order;
            $('#workOrderDetailModal').modal('show');
        }
    },
    props:{
        work_orders: Array,
        source: String,
        ty: String,
    },
    watch:{
        work_orders(){
        /*    if (this.purchase_orders.length == 0){this.loading = true;}
            else{this.loading = false;}*/
        }
    },
}
</script>