<template>
<section class="row">
    <div class="modal fade" id="productModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update' : 'Create'}} Product</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormProduct :editMode="editMode" :product.sync="product" @reloadProducts="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card" v-if="products.total != 0 && products.data.length != 0">
            <div class="card-header bg-success">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="card-title card-title font-18 text-white">Products</h5>
                    </div>
                    <div class="col-6">
                        <div class="card-statistics text-right">
                            <div class="card-tools">
                                <button class="btn btn-tool btn-sm text-white" @click="addProduct"><i class="fa fa-plus"></i></button>
                                <button class="btn btn-tool btn-sm text-white" v-if="style == 'table'" @click="switchStyle('grid')"><i class="fa fa-table" title="Grid View"></i></button>
                                <button class="btn btn-tool btn-sm text-white" v-if="style == 'grid'" @click="switchStyle('table')"><i class="fa fa-list" title="Table View"></i></button>
                                <button class="btn btn-tool btn-sm text-white" @click="exportQuery"><i class="fa fa-download"></i></button>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive table-responsive-xl table-responsive-lg p-0" style="height: 600px;">
                <EscrowDetailProductList :products="products.data" source="mine" :style="style" @getValues="getAllInitials()"/>
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="products.per_page != null ? products.per_page : 52" :records="products.total != null ? products.total : 550" >
                </pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            loading: false,
            products: { data: []},
            product: {},
            style: 'grid',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addProduct(){
            this.loading = true;
            this.editMode = false;
            this.product = {};
            $('#productModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#productModal').modal('hide');
        },
        async exportQuery(){
            this.loading = true;
            //this.filterData.post('/api/escrows/payments/generate_report')
            try {
                const response = await axios.post('/api/escrows/products/generate_report',
                    this.filterData,
                    {responseType: 'blob',}
                );

                // Extract filename from header
                const contentDisposition = response.headers['content-disposition'];
                const fileName = contentDisposition
                ? contentDisposition.split('filename=')[1].replace(/"/g, '')
                : 'product_list.csv';

                // Create a blob URL and force download
                const blob = new Blob([response.data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
            } 
            catch (error) {
                console.error('Download failed:', error);
                this.$toast.fire({
                icon: 'error',
                title: 'Failed to generate report',
                });
            }
            this.loading = false;
        },
        getAllInitials(){
            this.closeModals();
            this.loading = true;
            axios.get('/api/escrows/products?t=my&page='+this.current_page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Products loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Products not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.products = response.data.products;
            this.closeModals();
        },
        switchStyle(text){
            this.style = text;
        },
    },
}
</script>