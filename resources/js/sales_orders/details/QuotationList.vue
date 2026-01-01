<template>
    <section class="overlay-wrapper table-responsive p-0">
        <div class="modal fade" id="customerFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Create New Customer</h4>
                        <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <CRMFormCustomer :editMode="editMode" :customer.sync="customer" @customerFormReload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="quotationFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Create New Customer</h4>
                        <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <SalesFormQuotation :editMode="editMode" :quotation_id.sync="quotation_id" @quotationFormReload="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card-body table-responsive p-0" style="height: 600px;">
            <table class="table table-head-fixed table-striped text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Store</th>
                        <th>Order Number</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="quotations.length > 0">
                    <tr v-for="(quotation, index) in quotations" :key="index">
                        <td>{{ addOne(index) }}</td>
                        <td>{{ quotation.customer != null ? quotation.customer.name : 'Walk In Customer'  }}</td>
                        <td>{{ quotation.store != null ? quotation.store.name : 'Not Assigned' }}</td>
                        <td>{{ quotation.uuid }}</td>
                        <td>{{ ExcelDate(quotation.date) }}</td>
                        <td>
                            <span v-if="quotation.status == 'draft'" class="badge badge-secondary">Draft</span>
                            <span v-else-if="quotation.status == 'sent'" class="badge badge-info">Sent</span>
                            <span v-else-if="quotation.status == 'agreed'" class="badge badge-success">Agreed</span>
                            <span v-else-if="quotation.status == 'cancelled'" class="badge badge-danger">Cancelled</span>
                            <span v-else class="badge badge-primary">Ongoing</span>
                        </td>
                        <td>
                            <button class="nav-link btn btn-sm btn-tool p-1" data-toggle="dropdown" type="button">
                                <i class="fa fa-ellipsis-v text-dark mt-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <router-link :to="'/sales_orders/quotations/'+quotation.uuid" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-dark"></i>View</router-link>
                                <button v-if="quotation.customer_id == 0" class="dropdown-item btn btn-block btn-sm" @click="addCustomer(quotation.uuid)"><i class="fa fa-user-plus mr-1 text-primary"></i> Assign To Customer</button>
                                <button v-if="quotation.status == 'draft'" class="dropdown-item btn btn-block btn-sm" @click="updateQuote(quotation.uuid)"><i class="fa fa-edit mr-1 text-warning"></i> Update Order</button>
                                <button v-if="quotation.status == 'sent'" class="dropdown-item btn btn-block btn-sm" @click="agreeQuote(quotation)"><i class="fa fa-credit-card mr-1 text-success"></i> Confirm Quote</button>
                                <button v-if="quotation.status != 'cancelled'" class="dropdown-item btn btn-block btn-sm" @click="resendQuote(quotation.id)"><i class="fa fa-envelope mr-1 text-purple"></i> Send Quote</button>
                                <button v-if="quotation.status != 'agreed' && quotation.status != 'cancelled'" class="dropdown-item btn btn-block btn-sm" @click="deleteQuote(quotation.unique_id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Quote</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="7">No Quotation meets your request</td>
                    </tr>
                </tbody>
            </table>
        </div>    
    </section>
</template>
<script>
import ApprovalFormSalesQuotation from '../../approvals/forms/SalesQuotation.vue';
import CRMFormCustomer from '../../crm/forms/Customer.vue';
import SalesFormQuotation from '../forms/Quotation.vue';
export default {
    components: {
        ApprovalFormSalesQuotation, CRMFormCustomer, SalesFormQuotation,
    },
    data() {
        return {
            quotation_id: '',
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    emits:['quotationReload'],
    mounted() {},
    methods: {
        addCustomer(quotation_id){
            this.loading = true;
            this.customer = {};
            this.editMode = false;
            this.quotation_id = quotation_id;
            $('#assigncustomerFormModal').modal('show');
            this.loading = false;
        },
        agreeQuote(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "A new Sales Order will be created from this Quotation!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.get('/api/sales/quotation/confirm/'+id)
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
        closeModals() {
            $('#customerFormModal').modal('hide');
            $('#quotationFormModal').modal('hide');
        },
        deleteQuote(id) {
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
                    this.form.delete('/api/sales/quotations/'+id)
                    .then(response => {
                        this.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'QUotation has been cancelled.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        resendQuote(id) {
            this.loading = true;
            this.form.get('/api/sales/quotations/mail/'+id)
            .then(response => {
                this.$swal.fire('Success', 'Quotation has been sent successfully.', 'success');
                this.loading = false;
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                this.loading = false;
            });
        },
        refreshPage(){
            this.closeModals();
            this.$emit('quotationReload');
        },
        updateQuote(quotation_id){
            this.loading = true;
            this.quotation_id = quotation_id;
            this.editMode = true;
            $('#quotationFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        quotations: Array,
        source: String,
    },
    watch:{
        quotations(){
        
        }
    },
}
</script>