<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="salesOrderModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Approve Sales Order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="editMode = false"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ApprovalFormSalesOrder :order.sync="order" @approvalSalesReload="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Sales Order Details</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-white mt-2"></i></button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <button v-if="order.status == 1" class="dropdown-item btn btn-block btn-sm" @click="approveOrder()"><i class="fa fa-file-signature mr-1 text-info"></i> Approve Sales Order</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="cancelOrder()"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Order</button>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <SalesDetailOrder :order.sync="order" view="approvals" @salesOrderReload="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import useInvoiceTools from '@/globalMethods/useInvoiceTools';
import {ref} from 'vue';

export default {
    computed:{
        sub_total(){
            if (!this.order.order_items?.length) return 0;
            return this.order.order_items.reduce(
                (sum, it) => sum + it.unit_price * it.quantity,
                0
            );
        },
    },
    data() {
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            form: new Form({}),
            order: {
                order_items: [],
            },
            query: null,
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addOrder(){
            $('#orderModal').modal('show');
        },
        approveOrder(){
            $('#salesOrderModal').modal('show');
        },
        closeModals() {
            $('#approvalFormModal').modal('hide');
            $('#orderFormModal').modal('hide');
        },
        collectPayment(){
            $('#paymentFormModal').modal('show');
        },
        getAllInitials() {
            this.loading = true 
            axios.get('/api/approvals/sales_orders/'+ this.$route.params.id)
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
        mailOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.get('/api/sales_order/orders/mail/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(response) {
            this.order = response.data.order;
            this.closeModals();
        },
        updateOrder(){
            this.loading = true;
            $('#orderFormModal').modal('show');
            this.loading = false;
        }
    },
    setup () {
        const invoiceRef = ref(null);
        const { downloadPdf, printInvoice } = useInvoiceTools(invoiceRef);
        return { invoiceRef, downloadPdf, printInvoice };
    },
}
</script>
<style scoped>
@media print {
  body *        { visibility: hidden !important; }
  #invoice, #invoice *    { visibility: visible !important; }
  #invoice      { position: absolute; top: 0; left: 0; width: 100%; }
  .no-print     { visibility: hidden !important; }
}
</style>