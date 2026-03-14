<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">Price List Details</h4>
                </div>
                <div class="card-body p-0 table-responsive" style="height: 150px">
                    <FinanceDetailPricelist :price_list.sync="price_list" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <InventoryFormItemSearch @item-search="searchItems" search_type="all_products" />
        </div>
        <div class="col-md-9 p-0">
            <FinanceFormPricelistItemBulk :price_list_id="$route.params.id" :price_list_items.sync="price_list_items" @refreshPriceLists="getAllInitials" />
        </div>    
    </div>
</section>
</template>
<script>
import FinanceDetailPricelist from '@/finance/details/Pricelist.vue';
import FinanceFormPricelist from '@/finance/forms/PriceList.vue';
import FinanceFormPricelistItemBulk from '@/finance/forms/PriceListItemBulk.vue';
import InventoryFormItemSearch from '@/inventory/forms/ItemSearch.vue';
export default {
    components:{FinanceDetailPricelist, FinanceFormPricelist, FinanceFormPricelistItemBulk, InventoryFormItemSearch },
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