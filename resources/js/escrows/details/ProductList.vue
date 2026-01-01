<template>
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
    <div class="modal fade" id="transactionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Start Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormTransaction :editMode="editMode" :product.sync="product" :transaction="{}"/>
                </div>
            </div>
        </div>
    </div>
    <section v-if="products.length != 0 && style == 'table'">
        <table class="table table-hover table-striped table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" colspan="2">Name</th>
                    <th v-if="source == 'admin'">Owner</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Detailed</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="products.length >= 1">
                <tr v-for="product in products">
                    <td class="text-center">
                        <img :src="product.image" :alt="product.description" :title="product.description" class="img-fluid img-circle img-size-32 mr-2" width="32">
                        <br /><span class="badge badge-secondary-inverse mr-2" v-html="product.item_code"></span>
                    </td>
                    <td class="text-bold" v-html="product.description"></td>
                    <td v-if="source == 'admin'">{{ product.owner != null ? fullName(product.owner) : 'No User has this' }}</td>
                    <td>{{currency(product.unit_price)}}</td>
                    <td>Approved</td>
                    <td :title="product.details" v-html="readMore(product.details, 50, '...')"></td>
                    <td>
                        <button class="nav-link btn btn-sm btn-tool p-3" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v text-dark"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'/escrows/products/'+product.id+'/'+source"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Product</button></router-link>
                            <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'browse'" @click="addReview(product)"><i class="fa fa-comments mr-1 text-teal"></i> Review Product</button>
                            <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'mine'" @click="updateProduct(product)"><i class="fa fa-edit mr-1 text-success"></i> Update Product</button>
                            <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'mine'" @click="addImage(product)"><i class="fa fa-file-image mr-1 text-teal"></i> Add Product Image</button>
                            <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'browse'" @click="startTransaction(product)"><i class="fa fa-shopping-cart mr-1 text-purple"></i> Purchase Product</button>
                            <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'mine'" @click="deactivateProduct(product.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Product</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr><td colspan="8">No Product has been created</td></tr>
            </tbody>
        </table>
    </section>
    <div v-else-if="products.length != 0 && style == 'grid'">
        <div class="row" v-if="products.length >= 1">
            <div class="col-md-4" v-for="product in products">
                <div class="card" style="">
                    <img class="card-img-top" :src="product.image" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title text-bold" v-html="product.description"></h5>
                        <p class="card-text" v-html="readMore(product.details, 100, '...')"></p>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button">
                                <i class="fa fa-bars text-dark"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <router-link :to="'/escrows/products/'+product.id+'/'+source"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Product</button></router-link>
                                <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'browse'" @click="addReview(product)"><i class="fa fa-comments mr-1 text-teal"></i> Review Product</button>
                                <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'mine'" @click="updateProduct(product)"><i class="fa fa-edit mr-1 text-success"></i> Update Product</button>
                                <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'browse'" @click="startTransaction(product)"><i class="fa fa-shopping-cart mr-1 text-purple"></i> Purchase Product</button>
                                <button class="dropdown-item btn btn-block btn-sm" v-if="source == 'mine'" @click="deactivateTransaction(product.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Product</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0 m-0" v-else style="border: none !important;">
        <div class="small-box"  style="height: 300px;">
            <div class="inner"><h3>0</h3><p>Products </p></div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            product: {},
            
        }
    },
    emits:['getValues'],
    methods:{
        closeModal(){
            $('#productModal').modal('hide');  
            $('#transactionModal').modal('hide');
        },
        deactivateProduct(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This product will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/escrows/products/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.$emit('getValues');
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        getValues(){
            this.closeModal();
            this.$emit('getValues');
        },
        startTransaction(product){
            this.loading = true;
            this.editMode = false;
            this.product = product;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        updateProduct(product){
            alert(product.details);
            this.loading = true;
            this.editMode = true;
            this.product = product;
            $('#productModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        products: Array,
        source: String,
        style: String,
    },
    watch:{}
}
</script>