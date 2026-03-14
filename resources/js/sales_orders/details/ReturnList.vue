<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="approvalFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title">Update Return Order: {{ return_order.unique_id }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ApprovalFormAction :editMode="editMode" reference_type="returns" :reference.sync="return_order" @approvalReload="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="returnOrderFormModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title">Update Return Order: {{ return_order.unique_id }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <SalesFormReturn :editMode="editMode" :return_order_id.sync="return_order.id" @returnFormRefresh="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-striped table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Unique ID</th>
                <th>Sales Order ID</th>
                <th>Store ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="return_orders && return_orders.length > 0">
            <tr v-for="return_order in return_orders" :key="return_order.id">
                <td>{{ ExcelDate(return_order.date)  }}</td>
                <td>{{ return_order.customer != null ? return_order.customer.name : 'WalkIn Customer' }}</td>
                <td>{{ return_order.unique_id }}</td>
                <td>{{ return_order.order != null ? return_order.order.unique_id : 'No Sales Order Attached' }}</td>
                <td>{{ return_order.store != null ? return_order.store.name : 'No Store Attached' }}</td>
                <td>{{ currency(return_order.amount) }}</td>
                <td>
                    <span v-if="return_order.status == 1" class="badge badge-secondary">Awaiting Approval</span>
                    <span v-else-if="return_order.status == 10" class="badge badge-primary">Approved</span>
                    <span v-else-if="return_order.status == 100" class="badge badge-danger">Rejected</span>
                    <span v-else class="badge badge-warning">Ongoing</span>
                </td>
                <td>
                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'approvals'">
                        <router-link :to="'/approvals/returns/'+return_order.unique_id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View</router-link>
                        <button v-if="return_order.status == 1" class="dropdown-item btn btn-block btn-sm" @click="approveOrder(return_order.unique_id)"><i class="fa fa-file-signature mr-1 text-info"></i> Approve Return</button>
                        <button v-if="return_order.status > 1 && return_order.status < 10" class="dropdown-item btn btn-block btn-sm" @click="resendAppointment(return_order.id)"><i class="fa fa-envelope mr-1 text-purple"></i> Resend Invoice</button>
                        <button v-if="return_order.status <= 1 || return_order.status == 40" class="dropdown-item btn btn-block btn-sm" @click="deleteOrder(return_order.unique_id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'returns'">
                        <router-link :to="'/sales_orders/returns/'+return_order.unique_id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View Return</router-link>
                        <button v-if="return_order.status <= 1" class="dropdown-item btn btn-block btn-sm" @click="updateReturn(return_order.unique_id)"><i class="fa fa-edit mr-1 text-warning"></i> Update Return</button>
                        <button v-if="return_order.status <= 1 || return_order.status == 100" class="dropdown-item btn btn-block btn-sm" @click="deleteOrder(return_order.unique_id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Return</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="7">No Returns meets your criteria</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import ApprovalFormAction from '@/approvals/forms/Action.vue';
import SalesFormReturn from '@/sales_orders/forms/Return.vue';
export default {
    components:{ApprovalFormAction, SalesFormReturn},
    data() {
        return {
            return_order: {id: null, unique_id: ''},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    emits:['returnOrderReload'],
    mounted() {},
    methods: {
        approveOrder(order_id){
            this.loading = true;
            this.getReturnDetails(order_id);
            $('#approvalFormModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#customerFormModal').modal('hide');
            $('#orderReturnFormModal').modal('hide');
            $('#approvalFormModal').modal('hide');
        },
        deleteOrder(id) {
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
                    this.form.delete('/api/sales/returns/'+id)
                    .then(response => {
                        this.$emit('returnOrderReload', response);
                        this.$swal.fire('Deleted!', 'Return Order has been cancelled.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        getReturnDetails(order_id){
            axios.get('/api/sales/returns/'+order_id)
            .then(response => {
                this.return_order = response.data.return_order;
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Sales Order loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Sales Order was not loaded successfully',
                })
            });
        },
        refreshPage(){
            this.closeModals();
            this.$emit('returnOrderReload');
        },
        updateReturn(order_id){
            this.loading = true;
            this.editMode = true;
            this.getReturnDetails(order_id);
            $('#returnOrderFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        return_orders: Array,
        source: String,
    },
    watch:{
    },
}
</script>