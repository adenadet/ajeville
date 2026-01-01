<template>
<section class="overlay-wrapper">
    <div class="modal fade" id="productModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update' : 'Create'}} Products</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormProduct :product="product" :editMode="editMode" @reloadProducts="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="transactionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Start Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true"><i class="fa fa-times text-white"></i></span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormTransaction :editMode="editMode" :product.sync="product" :transaction="{}"/>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="row align-items-center text-white">
                        <div class="col-6">
                            <h5 class="card-title card-title font-18 text-white">Product Details</h5>
                        </div>
                        <div class="col-6">
                            <div class="card-tools text-right">
                                <button type="button" class="btn btn-tool text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-bars mr-3"></i></button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item btn btn-block btn-sm" v-if="$route.params.owner == 'mine'" @click="addReview(product)"><i class="fa fa-comments mr-1 text-teal"></i> Review Product</button>
                                    <button class="dropdown-item btn btn-block btn-sm" v-if="$route.params.owner == 'mine'" @click="updateProduct(product)"><i class="fa fa-edit mr-1 text-success"></i> Update Product</button>
                                    <button class="dropdown-item btn btn-block btn-sm" v-if="$route.params.owner == 'browse'" @click="startTransaction(product)"><i class="fa fa-shopping-cart mr-1 text-purple"></i> Purchase Product</button>
                                    <button class="dropdown-item btn btn-block btn-sm" v-if="$route.params.owner == 'mine'" @click="deactivateProduct(product.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Product</button>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <EscrowDetailProductSummary :product.sync="product" />
                        </div>
                        <div class="col-md-8">
                            <div class="card card-tabs p-0">
                                <div class="card-header p-0 pt-1 border-bottom-0 bg-navy">
                                    <ul class="nav nav-tabs" id="product-tab" role="tablist">
                                        <li class="nav-item" v-if="$route.params.owner == 'mine'"><a class="nav-link" id="orders-tab" data-toggle="pill" href="#orders" role="tab" aria-controls="orders" aria-selected="true">Orders</a></li>
                                        <li class="nav-item"><a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab" aria-controls="details" aria-selected="false">Details</a></li>
                                        <li class="nav-item"><a class="nav-link" id="summary-tab" data-toggle="pill" href="#summary" role="tab" aria-controls="summary" aria-selected="false">Reviews</a></li>
                                        <li class="nav-item"><a class="nav-link" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Settings</a></li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="product-tabContent">
                                        <div class="tab-pane fade" id="orders" v-if="$route.params.owner == 'mine'" role="tabpanel" aria-labelledby="orders-tab">
                                            <EscrowDetailTransactionList :transactions.sync="product.transactions" :source="$route.params.owner" />
                                        </div>
                                        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                                            <EscrowDetailProduct :product.sync="product" :source="$route.params.owner" :editMode="editMode"/>
                                        </div>
                                        <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                                            <EscrowDetailReviewList :product.sync="product" :source="$route.params.owner" />
                                        </div>
                                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            editMode: true,
            loading: false,
            product: {},
        }
    },
    mounted(){
        this.getAllInitials();
    },
    methods:{
        closeModals(){
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
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        getAllInitials(){
            this.loading = true;
            this.closeModals();
            axios.get('/api/escrows/products/'+this.$route.params.id+'?type=my')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Transactions loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Items not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.product = response.data.product;
        },
        startTransaction(product){
            this.loading = true;
            this.editMode = false;
            this.product = product;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        switchStyle(text){
            this.style = text;
        },
        updateProduct(product){
            this.loading = true;
            this.editMode = true;
            this.product = product;
            $('#productModal').modal('show');
            this.loading = false;
        }
    },
}
</script>