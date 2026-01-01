<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="prices-tab" data-toggle="pill" href="#prices" role="tab" aria-controls="prices" aria-selected="true">Prices</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="plans-tab" data-toggle="pill" href="#plans" role="tab" aria-controls="plans" aria-selected="false">Plans</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="details-tab" data-toggle="pill" href="#details" role="tab" aria-controls="details" aria-selected="false">Details</a>
                        </li>
                        <!--li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill" href="#custom-tabs-four-settings" role="tab" aria-controls="custom-tabs-four-settings" aria-selected="false">Settings</a>
                        </li-->
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade show active" id="prices" role="tabpanel" aria-labelledby="prices-tab">
                            <div class="row"> 
                                <div class="col-md-3">
                                    <InventoryFormItemSearch @item-search="searchItems" search_type="all_products" />
                                </div>
                                <div class="col-md-9 p-0">
                                    <FinanceFormPricelistItemBulk :price_list_id="$route.params.id" :price_list_items.sync="price_list_items" @refreshPriceLists="getAllInitials" />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="plans" role="tabpanel" aria-labelledby="plans-tab">
                            <FinanceDetailPricelistPlanList :price_list.sync="price_list" />    
                        </div>
                        <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
                            <FinanceDetailPricelist :price_list.sync="price_list" source="emr" /> 
                        </div>
                        <!--div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel" aria-labelledby="custom-tabs-four-settings-tab">
                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                        </div-->
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
            categories: [],
            drugs: [],
            items: [],
            loading: true,
            price_list: {},
            price_list_items: [], //{data: [], total: 0},
            types: [],
        }
    },
    emits: ['itemReload'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addPackageItem(item){
            this.ItemData.items.push(item)
        },
        createItem(){
            this.loading = true;
            this.ItemData.post('/api/inventory/items')
            .then(response =>{
                this.loading = false;
                this.$emit('itemReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });  
        },
        async getAllInitials(){
            this.loading = true;
            await axios.get('/api/finance/price_lists/'+this.$route.params.id)
            .then(response =>{
                this.updateItems(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Items loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Items not loaded successfully',
                })
            });
            this.loading = false;
                
        },
        searchItems(formData){
            this.loading = true;
            axios.put('/api/finance/price_lists/'+this.$route.params.id+'/search', formData)
            .then(response => {
                this.updateItems(response)
            })
            .catch(error => {
                console.error('Error:', error);
            });
            this.loading = false;
            //this.price_list = response.data.price_list;
            
        },
        async updateItems(response){
            this.price_list_items = response.data.price_list_items;
            this.price_list = response.data.price_list;
        }
    },
}
</script>