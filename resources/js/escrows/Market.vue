<template>
<section class="p-0">
    <div class="card m-0">
        <div class="card-header bg-dark">
            <h3 class="card-title">Search for Product</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card m-0">
        <div class="card-header bg-dark"><h3 class="card-title">Market Listings</h3></div>
        <EscrowDetailProductList :products="products.data" source="browse" />
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="products.per_page != null ? products.per_page : 52" :records="products.total != null ? products.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            loading: false,
            products: { data: []},
            searchForm: new Form({
                search: '',
            }),
        }
    },
    methods:{
        addImage(){},
        reloadPage(response){
            this.customer = response.data.customer;
            this.staffs   = response.data.staffs;
        }
    },
    mounted() {},
    props:{
        product: Object,
    },
    watch:{
        product(){
            
        }
    }
}
</script>