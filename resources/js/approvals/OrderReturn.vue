<template>
<section>
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
    <div class="modal fade" id="returnOrderFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Update Return: {{ return_order.unique_id }}</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <SalesFormReturn :editMode="editMode" :return_order.sync="return_order" @returnFormRefresh="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Return Details</h3>
                    <div class="card-tools">
                        <button v-if="return_order.status == 1" class="btn btn-info btn-xs" @click="approveReturn"><i class="fa fa-file-signature mr-1"></i>Approve</button>
                    </div>
                </div>
                <div class="card-body overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <SalesDetailReturn :return_order.sync="return_order" />      
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            form: new Form({}),
            return_order: {
                unique_id: '',
                id: '',
                return_items: [],
            },
            query: null,
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        approveReturn(){
            $('#approvalFormModal').modal('show');
        },
        closeModals() {
            $('#approvalFormModal').modal('hide');
            $('#returnOrderFormModal').modal('hide');
        },
        createDeliveryNote(){
            $('#deliveryNoteFormModal').modal('show');
        },
        makePayment(){
            $('#paymentFormModal').modal('show');
        },
        getInitials() {
            this.loading = true 
            this.closeModals();
            axios.get('/api/sales/returns/'+ this.$route.params.id)
            .then(response => {
                this.refreshPage(response);
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
        refreshPage(response) {
            this.return_order = response.data.return_order;
            this.closeModals();
            this.loading = false;
        },
        updateReturn(){
            this.loading = true;
            this.editMode = true;
            $('#returnOrderFormModal').modal('show');
            this.loading = false;
        }
    },
}
</script>