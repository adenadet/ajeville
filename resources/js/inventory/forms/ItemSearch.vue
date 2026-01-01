<template>
    <section class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Search for Item</h3>
        </div>
        <div class="card-body overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <form>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Category</label>
                            <select v="categories.length > 0" class="form-control" v-model="itemSearchData.category_id" option-value="id" option-text="name" placeholder="Select Category">
                                <option value="">--Select Item Category--</option>
                                <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Classification</label>
                            <select v-if="classifications.length > 0" class="form-control" v-model="itemSearchData.classification_id" option-value="id" option-text="name" placeholder="Select Classification">
                                <option value="">--Select Item Classification--</option>
                                <option v-for="classification in categories" :value="classification.id">{{ classification.name }}</option>
                            </select>
                            <!--model-list-select class="form-control" :list="categories" v-model="itemSearchData.classification_id" option-value="id" option-text="name" placeholder="Select Classification" /-->
                            <select v-else class="form-control" v-model="itemSearchData.classification_id" option-value="id" option-text="name" placeholder="Select Issuing Store">
                                <option value="">--Select Item Classification--</option>
                                <option v-for="classification in classifications" :value="classification.id">{{ classification.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Brand</label>
                            <select class="form-control" v-model="itemSearchData.brand_id" option-value="id" option-text="name" placeholder="Select Issuing Store">
                                <option value="">--Select Brands--</option>
                                <option v-for="brand in brands" :value="brand.id">{{ brand.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Item</label>
                            <input type="text" name="name" id="name" class="form-control" v-model="itemSearchData.name" placeholder="Name of item">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" @click="sendOut()">Submit</button>
            </form>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            categories: [],
            classifications: [],
            brands: [],
            item: {},
            items: [],
            itemSearchData: new Form({
                brand_id: '',
                category_id: '',
                classification_id: '',
                name: '', 
            }),
            loading: true,
        };
    },
    emits: ['itemSearch'],
    mounted() {
        this.getInitials();
    },
    methods:{
        addNewItem(){
            this.loading = true;
            this.editMode = false;
            this.item = {};
            $('#itemModal').modal('show');
            this.loading = false; 
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/inventory/items/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Item Search did not loaded successfully',
                })
            });
        },
        isEmptySearch() {
            const { brand_id, category_id, classification_id, name } = this.itemSearchData;
            return !brand_id && !category_id && !classification_id && !name;
        },
        refreshPage(response){
            this.brands = response.data.brands;
            this.categories = response.data.categories;
            this.classifications = response.data.classifications;
            this.items = response.data.items;
        },
        sendOut() {
            if (this.isEmptySearch()) {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No query item!',
                    footer: 'Please enter query item!',
                });
                return;
            }
            this.loading = true;
            
            this.$emit('itemSearch', this.itemSearchData);
            //this.itemSearchData.reset();
            this.loading = false;
        }
    },
    props:{
        category: Object,
        classification: Object,
        search_type: String,
        services: Array,
        source: String,
    },
    watch:{
        item(){
            if (this.item != null ){
                this.ItemData.fill(this.item);
            }
            else{
                this.ItemData.reset();
            }
        }
    }
}
</script>