<template>
<div class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="quotationModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Create New Quote</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModal()" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <SalesFormQuotation :editMode="editMode" :quotation.sync="quotation" @refreshQuotation="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Quotations</h3>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 350px;">
                            <input v-model="query" type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" @click="getInitials()"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-primary" @click="addQuotation()"><i class="fas fa-plus"></i></button>
                                <select v-model="status" class="ml-3 form-control" @change="getInitials()">
                                    <option value="draft">Draft</option>
                                    <option value="sent">Sent</option>
                                    <option value="agreed">Agreed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <SalesDetailQuotationList :quotations="quotations.data" source="main" @quotationReload="getInitials"/>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="quotations.per_page != null ? quotations.per_page : 52" :records="quotations.total != null ? quotations.total : 550" ></pagination>
                </div>
            </div>
        </div>
    </section>
</div>
</template>
<script>
import SalesDetailQuotationList from '@/sales_orders/details/QuotationList.vue';
import SalesFormQuotation from '@/sales_orders/forms/Quotation.vue';
export default {
    components: {
        SalesDetailQuotationList, SalesFormQuotation 
    },
    data() {
        return {
            current_page: 1,
            quotations: {data:[]},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
            quotation: {},
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addQuotation(){
            this.loading = true;
            this.quotation = {};
            $('#quotationModal').modal('show');
            this.loading = false;
        },
        closeModals() {
            $('#quotationModal').modal('hide');
        },
        downloadOrders(){},
        getInitials(page = 1) {
            this.loading = true 
            axios.get('/api/sales/quotations?page='+page+'&status='+this.source+'&search='+this.query)
            .then(response => {
                this.refreshPage(response);
                this.loading = false; 
                this.$toast.fire({
                    icon: 'success',
                    title: 'Quotations loaded successfully',
                });
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Quotations were not loaded successfully',
                })
            });
        },
        refreshPage(response) {
            this.quotations = response.data.quotations;
            this.closeModals();
        },
    },
}
</script>