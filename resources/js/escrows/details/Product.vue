<template>
<section>
    <div class="modal fade" id="productModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update Item:'+product.item_code : 'Create'}} Product</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormProduct :product.sync="product" :editMode.sync="editMode" @reloadProducts="getValues()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <label>Name</label>
                <div class="form-control" v-html="productData.description"></div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Role</label>
                <div class="form-control" v-html="productData.role"></div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Category</label>
                <div class="form-control" v-html="product.category != null ? product.category.name : 'Loading'"></div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Price</label>
                <div class="form-control" v-html="productData.unit_price"></div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Quantity</label>
                <div class="form-control" v-html="productData.quantity"></div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Description</label>
                <div v-html="productData.details"></div>
            </div>
        </div>
    </div>
    <div class="row">
        
    </div>
    <div class="row">
        <div class="col-sm-4"><input type="button" name="Update" class="submit btn btn-success" value="Update" @click="editProduct"/></div>
    </div>
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
        editProduct(){
            this.loading = true;

            this.loading = false;
        },
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