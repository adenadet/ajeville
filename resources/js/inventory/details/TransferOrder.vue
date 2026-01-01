<template>
    <section class="container-fluid overlay-wrapper position-relative">
        <div class="modal fade" id="issueTransferOrderModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Issue Transfer Request</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <InventoryFormStoreIssue :issue_request.sync="transfer_order" issue_type="1"  @transferOrderReload="getAllInitials" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="rejectTransferOrderModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Reject Transfer Request</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <InventoryFormTransferOrderReject :transfer_order.sync="transfer_order" @transferOrderReload="getAllInitials" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="transferOrderModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Transfer Request</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <InventoryFormTransferOrder :transfer_order.sync="transfer_order" @transferOrderReload="getAllInitials" :editMode.sync="editMode" :form_type="form_type"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="ribbon-wrapper ribbon-xl" v-if="transfer_order.status >= 5">
            <div v-if="transfer_order.status == 10" class="ribbon bg-danger text-xl">REJECTED</div>
            <div v-else-if="transfer_order.status == 6" class="ribbon bg-success text-xl">COMPLETED</div>
            <div v-else-if="transfer_order.status == 5" class="ribbon bg-success text-xl">ONGOING</div>
        </div>
        
        <div class="invoice p-3 mb-3">
            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="fas fa-globe"></i> Transfer Order Request
                        <small class="float-right">Date: {{ExcelDate(transfer_order.created_at)}}</small>
                    </h4>
                </div>
            </div>
            <div class="row invoice-info">
                <div class="col-sm-3 invoice-col">
                    Created By:
                    <address v-if="transfer_order.creator != null">
                        <strong>{{ FullName(transfer_order.creator) }}</strong><br>
                        Department: <strong>{{ transfer_order.creator.department != null ? transfer_order.creator.department.name : 'Not Done' }}</strong><br>
                        Store: <strong>{{ transfer_order.requesting_store.name }}</strong><br>
                        At: <strong>{{ ExcelDate(transfer_order.created_at) }}</strong><br>
                    </address>
                </div>
                <div class="col-sm-3 invoice-col">
                    Approved By:
                    <address v-if="transfer_order.approver != null">
                        <strong>{{ FullName(transfer_order.approver) }}</strong><br>
                        Department: <strong>{{ transfer_order.approver.department != null ? transfer_order.approver.department.name : 'Not Done' }}</strong><br>
                        Store: <strong>{{ transfer_order.requesting_store.name }}</strong><br>
                        At: <strong>{{ ExcelDate(transfer_order.approved_at) }}</strong><br>
                    </address>
                </div>
                <div class="col-sm-3 invoice-col">
                    Issued By
                    <address v-if="transfer_order.issuer != null">
                        <strong >{{ FullName(transfer_order.issuer) }}</strong><br />
                        Department: <strong>{{ transfer_order.issuer.department.name }}</strong><br>
                        Store: <strong>{{ transfer_order.issuing_store.name }}</strong><br>
                        At: <strong>{{ ExcelDate(transfer_order.accepted_at) }}</strong><br>
                    </address>
                    <address v-else>
                        <span class="text-warning">Awaiting Approval</span><br>
                        Store: {{ transfer_order.issuing_store != null ? transfer_order.issuing_store.name : 'Issuing Store Not Chosen' }}<br>
                    </address>
                </div>
                <div class="col-sm-3 invoice-col">
                    <b>Request ID: {{ capitalize(transfer_order.unique_id) }}</b><br>
                    <br>
                    <address v-if="transfer_order.fulfiller != null">
                        Fulfilled By:
                        <strong >{{ FullName(transfer_order.fulfiller) }}</strong><br />
                        {{ excelDate(transfer_order.fulfilled_at)}}<br>
                        Fulfillment ID: {{ transfer_order.fulfillment.unique_id }}<br>
                    </address>
                    <address v-else>
                        Not Yet Fulfilled
                    </address>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Items</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Timelines</a>
                                </li>
                                <!--li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Messages</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill" href="#custom-tabs-four-settings" role="tab" aria-controls="custom-tabs-four-settings" aria-selected="false">Settings</a>
                                </li-->
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                    <div class="row">
                                        <div class="col-12 table-responsive">
                                                <table class="table table-striped">
                                                    <thead class="bg-dark">
                                                        <tr>
                                                            <th>S/N</th>
                                                            <th>Item</th>
                                                            <th>Item Unique ID</th>
                                                            <th>Requested Qty</th>
                                                            <th>Approved Qty</th>
                                                            <th v-if="transfer_order.status >= 3">Issued Qty</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(order_item, index) in transfer_order.items" :key="order_item.id">
                                                            <td>{{ addOne(index) }}</td>
                                                            <td>{{ order_item.item.name }}</td>
                                                            <td>{{ order_item.item_unique_id }}</td>
                                                            <td>{{ order_item.requested_quantity != null ? order_item.requested_quantity : 0 }}</td>
                                                            <td>{{ order_item.approved_quantity != null ? order_item.approved_quantity : 0 }}</td>
                                                            <td v-if="transfer_order.status >= 3">{{ order_item.transfer_quantity != null ? order_item.transfer_quantity : 0 }}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                        </div>
                                    </div>
                                    <div class="row no-print">
                                        <div class="col-12">
                                            <button type="button" v-if="transfer_order.status < 4" class="btn btn-danger" @click="rejectRequest()"><i class="fas fa-trash mr-1"></i> Reject Request</button>
                                            <button type="button" v-if="transfer_order.status == 1" class="btn btn-success float-right" @click="approveRequest()"><i class="fas fa-check mr-1"></i> Approve Request</button>
                                            <button type="button" v-else-if="transfer_order.status == 2" class="btn btn-success float-right" style="margin-right: 5px;" @click="acceptRequest()"><i class="fas fa-check-double mr-1"></i> Accept Request</button>
                                            <button type="button" v-else-if="transfer_order.status == 3" class="btn btn-primary float-right" style="margin-right: 5px;" @click="issueRequest()"><i class="fas fa-cash-register"></i> Issue Request</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="timeline">
                                                <div v-if="transfer_order.status == 10">
                                                    <i class="fas fa-trash bg-danger"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> {{ExcelDate(transfer_order.rejected_at)}}</span>
                                                        <h3 class="timeline-header"><a href="#">{{FullName(transfer_order.rejecter)}}</a> rejected the Transfer order</h3>

                                                        <div class="timeline-body" v-html="transfer_order.rejection_note"></div>
                                                    </div>
                                                </div>
                                                <div v-if="transfer_order.status >= 3 && transfer_order.accepted_at != null">
                                                    <i class="fas fa-envelope bg-blue"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> {{ExcelDate(transfer_order.accepted_at)}}</span>
                                                        <h3 class="timeline-header"><a href="#">{{FullName(transfer_order.accepter)}}</a> accepted the Transfer order</h3>
                                                        <div class="timeline-body" v-html="transfer_order.acceptance_note"></div>
                                                    </div>
                                                </div>
                                                <div v-if="transfer_order.status >= 2 && transfer_order.approved_at != null">
                                                    <i class="fas fa-check-double bg-green"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> {{ExcelDate(transfer_order.approved_at)}}</span>
                                                        <h3 class="timeline-header"><a href="#">{{FullName(transfer_order.approver)}}</a> approved the Transfer order</h3>
                                                        <div class="timeline-body" v-html="transfer_order.approval_note"></div>
                                                    </div>
                                                </div>
                                                <div v-if="transfer_order.status >= 1">
                                                    <i class="fas fa-file bg-blue" ></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> {{ExcelDate(transfer_order.created_at)}}</span>
                                                        <h3 class="timeline-header"><a href="#">{{FullName(transfer_order.creator)}}</a> created a Transfer Request</h3>
                                                        <div class="timeline-body" v-html="transfer_order.description"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                                    Message Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. 
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel" aria-labelledby="custom-tabs-four-settings-tab">
                                    Settings Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                                </div-->
                            </div>
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
            form_type: '',
            loading: false,
        }
    },
    mounted() {},
    methods:{
        acceptRequest(){
            //this.loading = true;
            $('#transferOrderModal').modal('show');
            this.editMode = true;
            this.form_type = "accept";
            //$('#transferOrderModal').modal('show');
            //this.loading = false;
        },
        approveRequest(){
            $('#transferOrderModal').modal('show');
            this.editMode = true;
            this.form_type = "approve";
            //$('#transferOrderModal').modal('show');
            //this.loading = false;
        },
        closeModal(){
            $('#transferOrderModal').modal('hide');
        },
        getAllInitials(){
            this.$emits('transferOrderReload');
            this.closeModal();
        },
        issueRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "issue";
            $('#issueTransferOrderModal').modal('show');
            this.loading = false;
        },
        rejectRequest(){
            $('#rejectTransferOrderModal').modal('show');
        },
    },
    props:{
        transfer_order: Object,
    }
}
</script>