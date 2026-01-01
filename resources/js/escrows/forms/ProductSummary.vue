<template>
<section>
    <form class="overlay-wrapper" role="form" @submit.prevent="editMode ? updateProduct() :createProduct()">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-sm-8">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" v-model="productData.description" name="description" id="description" required/>
                    <input type="hidden" name="id" id="id" v-model="productData.id" />
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" name="role" id="role" v-model="productData.role">
                        <option value="">--Select Role--</option>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" v-model="productData.category_id" name="category_id" id="category_id" required>
                        <option value="">--Category--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" class="form-control" v-model="productData.unit_price" name="unit_price" id="unit_price" required/>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="text" class="form-control" v-model="productData.quantity" name="quantity" id="quantity" />
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor theme="snow" v-model:content="productData.details" content-type="html" placeholder="Put a Detailed description of the product to ensure that there will be no issue determining the product" name="details" id="details"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            
        </div>
        <div class="row">
            <div class="col-sm-4"><input type="submit" name="submit" class="submit btn btn-success" value="Submit" /></div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            categories: [],
            loading: false,
            productData: new Form({
                id: '',
                owner_id: '',
                item_code: '',
                category_id: '',
                description: '',
                details: '',
                detailed: '',
                quantity: '',
                role: '',
                status: '',
                unit_price: '',
            }),
        }
    },
    emits:['reloadProducts'],
    mounted(){
        this.getAllInitials();
    },
    methods:{
        createProduct(){
            this.loading = true
            this.productData.post('/api/escrows/products')
            .then(response =>{
                this.loading = false
                this.$emit('reloadProducts');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Product has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/escrows/transactions/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        updateProduct(){
            this.loading = true
            this.productData.put('/api/escrows/products/'+this.product.id)
            .then(response =>{
                this.loading = false
                this.$emit('reloadProducts');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Product : '+ this.product.item_code+' has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        }
    },
    props:{
        editMode: Boolean,
        product: Object,
    },
    watch:{
        product(){
            this.productData.fill(this.product);
        }
    }
}
</script>