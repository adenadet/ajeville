<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-3">
            <InventoryFormItemSearch @item-search="searchItems" search_type="all_products" />
        </div>
        <div class="col-md-9">
            <InventoryFormItemBulk :items.sync="items" @reloadItems="getAllInitials" />
        </div>
    </div>
</section>
</template>
<script>
import InventoryFormItemBulk from '@/inventory/forms/ItemBulk.vue';
import InventoryFormItemSearch from '@/inventory/forms/ItemSearch.vue';
export default {
    components:{
        InventoryFormItemBulk, InventoryFormItemSearch
    },
    data(){
        return  {
            categories: [],
            drugs: [],
            items: [],
            loading: true,
            ItemData: new Form({
                barcode: '',
                classification_id: '',
                category_id: '',
                description: '',
                id: '', 
                image: '',
                items:[],
                last_landing_cost: '',
                name: '', 
                specific_id: '',
                category_id: '', 
                quantity: 0, 
                status: '',
                type_id: '',     
                unique_id: '',
            }),
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
            await axios.get('/api/inventory/items?detailed=no&paginated=no&status=all')
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
        searchItems(filterQuery){
            this.loading = true;
            axios.post('/api/inventory/items/search', filterQuery)
            .then(response => {
                this.updateItems(response)
            })
            .catch(error => {
                //console.error('Error:', error);
            });
            this.loading = false;
        },
        updateItems(response) {
            this.items = response.data.items || [];
        },
    },
}
</script>