<template>
<section class="overlay-wrapper p-0">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="approvalFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Approve Batch</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormBatchApproval :batch.sync="batch" @refreshBatchApproval="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="batchModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Batch Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementDetailBatch :batch.sync="batch"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="batchFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Assign Store</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormBatch :batch.sync="batch" :editMode.sync="editMode" @refreshBatchForm="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Product</th>
                        <th>Batch number</th>
                        <th>Package Type</th>
                        <th>Package Quantity</th>
                        <th>Quantity Delivered</th>
                        <th>Expiry Date</th>
                        <th>Approved</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="batches != null && batches.length > 0">
                    <tr v-for="(batch, index) in batches" :key="index">
                        <td>{{ addOne(index) }}</td>
                        <td>{{ batch.item != null ? batch.item.name : 'Old Product' }}</td>
                        <td>{{ batch.batch_number }}</td>
                        <td>{{ batch.package_id }}</td>
                        <td>{{ batch.package_quantity }}</td>
                        <td>{{ batch.quantity }}</td>
                        <td>{{ batch.expiry_date }}</td>
                        <td>
                            <span class="badge badge-warning" v-if="batch.status == 0">Pending</span>
                            <span class="badge badge-success" v-if="batch.status == 1">Yes</span>
                            <span class="badge badge-danger" v-if="batch.status == 1">Rejected</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-dark"></i></button>
                            <div class="dropdown-menu">
                                <button @click="viewBatch(batch.id)" class="btn btn-block dropdown-item" ><i class="fa fa-eye mr-1"></i> View Batch</button>
                                <button v-if="source == 'approvals' && batch.status == 0" class="btn btn-block dropdown-item" @click="approveBatch(batch)"><i class="fa fa-check mr-1 text-warning"></i> Submit for Approval</button>
                                <button v-else class="btn btn-block dropdown-item" @click="updateBatch(batch)"><i class="fa fa-edit mr-1 text-primary"></i> Update Batch</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr><td colspan="9">No Batches Have been delivered</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            batch: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        };
    },
    emits:['refreshBatchList'],
    methods: {
        approveBatch(batch){
            this.loading = true;
            this.batch = batch;
            $('#approvalFormModal').modal('show');  
            this.loading = false;
        },
        closeModal(){
            $('#approvalFormModal').modal('hide');
            $('#batchModal').modal('hide');
            $('#batchFormModal').modal('hide');
        },
        getInitials(){
            this.closeModal();
            this.$emit('refreshBatchList');
        },
        updateBatch(batch){
            this.loading = true;
            this.batch = batch;
            this.editMode = true;
            $('#batchFormModal').modal('show');  
            this.loading = false;
        },
        viewBatch(batch){
            this.loading = true;
            this.batch = batch;
            $('#batchModal').modal('show');  
            this.loading = false;
        },
    },
    mounted(){
        //this.getAllInitials();
    },
    props:{
        batches: Array,
        purchase_order: Object,
        source: String,
        view: String,

    },
    watch:{
        batches(){},
        purchase_order() {
            this.loading = true;
            this.getInitials();
            this.closeModal();
            this.loading = false;
        },
    },
};
</script>