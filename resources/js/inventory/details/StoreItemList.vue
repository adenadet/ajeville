<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed table-striped table-hover text-nowrap">
        <thead>
            <tr>
                <th>Item</th>
                <th>Classification</th>
                <th>Category</th>
                <th>Store</th>
                <th>Maximum Level</th>
                <th>Reorder Level</th>
                <th>Expiry Date</th>
                <th>Balance</th>
                <th v-if="view != 'dashboard'">Sold</th>
                <th v-if="view != 'dashboard'">Transferred</th>
                <th v-if="view != 'dashboard'">Issued</th>
                <th v-if="view != 'dashboard'"></th>
            </tr>
        </thead>
        <tbody v-if="store_items != null && store_items.length != 0">
            <tr v-for="store_item in store_items">
                <td>{{ store_item.store_item != null && store_item.store_item.item != null ? store_item.store_item.item.name : 'N/A' }}</td>
                <td>{{ store_item.item != null && store_item.store_item.classification != null ? store_item.store_item.item.classification.name : 'N/A' }}</td>
                <td>{{ store_item.item != null && store_item.store_item.category != null ? store_item.store_item.item.category.name : 'N/A' }}</td>
                <td>{{ store_item.store_item != null && store_item.store_item.store != null ? store_item.store_item.store.name : 'N/A' }}</td>
                <td>{{ store_item.settings != null ? store_item.settings.maximum_level : 'N/A' }}</td>
                <td>{{ store_item.settings != null ? store_item.settings.reorder_level : 'N/A' }}</td>
                <td>{{ store_item.batch != null ? store_item.batch.expiry_date : 'N/A' }}</td>
                <td>{{ store_item.balance }}</td>
                <td v-if="view != 'dashboard'">{{ store_item.sold }}</td>
                <td v-if="view != 'dashboard'">{{ store_item.transferred }}</td>
                <td v-if="view != 'dashboard'">{{ store_item.issued }}</td>
                <td v-if="view != 'dashboard'"></td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="12">No Store Item Available</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    methods:{
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/notices?t=all&page='+page).then(response =>{
                this.reset(response);
                this.loading = false;
                toast.fire({icon: 'success', title: 'Notice loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Notice not loaded successfully',});
            });
        },
    },
    mounted() {},
    props:{
        source: String,
        store_items: Array,
        view: String,
    },
    watch(){

    }
}
</script>