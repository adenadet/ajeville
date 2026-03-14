<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Sales Orders</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <FinanceDetailInvoiceList :invoices.sync="invoices.data" source="approval" @salesOrderReload="getAllInitials" />
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="invoices.per_page != null ? invoices.per_page : 52" :records="invoices.total != null ? invoices.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import FinanceDetailInvoiceList from '@/inventory/InvoiceList.vue';
export default {
    components:{
        FinanceDetailInvoiceList
    },
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form_type: '',
            loading: false,
            invoice: {},
            invoices: {data:[], total: 0,},
            query: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        approveOrder(order){
            this.loading = true;
            this.editMode = true;
            this.form_type = "accept";
            $('#orderModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#orderModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/finance/invoices?status=unapproved&page='+this.current_page+'&query='+this.query)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Sales Orders loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transfer Orders not loaded successfully',
                })
            });
        },
        issueRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "issue";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        refreshPage(response){
            this.invoices = response.data.invoices;
        },
    },
}
</script>