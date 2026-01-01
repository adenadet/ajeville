<template>
    <section class="overlay-wrapper">
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Default Modal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <SalesDetailDeliveryNote :delivery_note.sync="delivery_note" />
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <table class="table table-striped table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Store</th>
                    <th>Order Number</th>
                    <th>Unique ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="delivery_notes.length > 0">
                <tr v-for="(delivery_note, index) in delivery_notes" :key="index">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ delivery_note.customer != null ? delivery_note.customer.name : 'Walk In Customer'  }}</td>
                    <td>{{ delivery_note.order != null && delivery_note.order.store != null ? delivery_note.order.store.name : 'Not Assigned' }}</td>
                    <td>{{ delivery_note.order != null ? delivery_note.order.unique_id : 'Undefined Order' }}</td>
                    <td>{{ delivery_note.uuid }}</td>
                    <td>{{ ExcelDate(delivery_note.created_at) }}</td>
                    <td>{{ (delivery_note.status == 1 ? 'Awaiting Assignment' : (delivery_note.status == 2 ? 'Assigned To Driver': (delivery_note.status == 3 ? 'Enroute': (delivery_note.status == 10 ? 'Completed': 'Ongoing'))))}}</td>
                    <td>
                        <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="view != 'direct'">
                            <router-link :to="'/sales_orders/delivery_notes/'+delivery_note.uuid"class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View</router-link>
                            <button type="button" v-show="delivery_note.status == 0" class="dropdown-item btn btn-block btn-sm" @click="makePayment(delivery_note)"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                            <button type="button" v-show="delivery_note.status == 1" class="dropdown-item btn btn-block btn-sm" @click="viewPayment(delivery_note)"><i class="fa fa-file-pdf mr-1 text-success"></i> View Receipt</button>
                            <button type="button" v-show="delivery_note.status <= 1 || delivery_note.status == null" class="dropdown-item btn btn-default btn-sm" @click="rescheduleAppointment(delivery_note)"><i class="fa fa-calendar mr-1"></i> Reschedule Appointment</button>
                            <button type="button" v-show="delivery_note.status == 1" class="dropdown-item btn btn-block btn-sm" @click="resendAppointment(delivery_note.id)"><i class="fa fa-envelope mr-1 text-warning"></i> Resend Confirmation</button>
                            <button type="button" v-show="delivery_note.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deleteAppointment(delivery_note.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="view == 'direct'">
                            <button type="button" @click="viewDeliveryNote(delivery_note)" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View</button>
                            <button type="button" v-show="delivery_note.status == 0" class="dropdown-item btn btn-block btn-sm" @click="makePayment(delivery_note)"><i class="fa fa-credit-card mr-1 text-success"></i> Make Payment</button>
                            <button type="button" v-show="delivery_note.status == 1" class="dropdown-item btn btn-block btn-sm" @click="viewPayment(delivery_note)"><i class="fa fa-file-pdf mr-1 text-success"></i> View Receipt</button>
                            <button type="button" v-show="delivery_note.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deleteAppointment(delivery_note.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Appointment</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="7" class="text-center">No Delivery Notes Found</td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
<script>
export default {
    data(){
        return {
            customers: {total: 0,},
            delivery_note: {},
            loading: false,
        }
    },
    methods:{
        viewDeliveryNote(delivery_note){
            this.loading = true;
            this.delivery_note = delivery_note;
            $('#deliveryNoteModal').modal('show');
            this.loading = false;
        },
        scrollHanle(evt) {
            console.log(evt)
        },
    },
    mounted() {
    },
    props:{
        delivery_notes: Array,
        view: String,
    },
}
</script>