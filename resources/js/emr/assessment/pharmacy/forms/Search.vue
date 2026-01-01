<template>
<div class="card">
    <div class="card-header">
        <div class="form-group">
            <label>Search *</label>
            <input type="text" class="form-control" placeholder="Enter Name" id="query" name="query" v-model="searchForm.query" @change="updateItems()">
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Drug Name</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Label</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in items.data" :key="index">
                    <td>{{index | addOne}}</td>
                    <td>{{item.name}}</td>
                    <td>{{item.price | currency}}</td>
                    <td>{{item.category.name}}</td>
                    <td><button class="btn btn-sm btn-primary" @click="addItemList(item)">Add Item</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <pagination :data="items" @pagination-change-page="getItems">
            <span slot="prev-nav">&lt; Previous </span>
            <span slot="next-nav">Next &gt;</span>
        </pagination>
    </div>
</div>
</template>
<script>
export default {
    data(){
        return {
            items: {},
            searchForm: new Form({query:'',}),
        }
    },
    methods:{
        addItemList(item){
            console.log(item); 
            Fire.$emit('updatePurchaseList', item);
        },
        searchItems(){
            axios.get('/api/inventory/purchases/search?q=')
            .then(response=>{
                this.itemsRefresh(response);   
            });
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/inventory/purchases/search').then(response =>{
                this.$Progress.finish();
                this.itemsRefresh(response);
                //toast.fire({icon: 'success', title: 'Products loaded successfully',});
            })
            .catch(()=>{this.$Progress.fail(); toast.fire({icon: 'error', title: 'Products not loaded successfully',})});
        },
        getItems(page=1){
            axios.get('/api/inventory/purchases/search?page='+page)
            .then(response=>{
                itemsRefresh(response)   
            });
        },
        itemsRefresh(response){
            this.items = response.data.products;    
        },
        updateItems(){
            axios.get('/api/emr/pharmacy/search?q='+searchForm.query)
            .then(response=>{
                itemsRefresh(response)   
            });
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>