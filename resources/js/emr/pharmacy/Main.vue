<template>
<section class="appie-hero-area appie-hero-4-area">
    <div class="container">
        <div class="modal fade" id="modalShoppingCart">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Extra Large Modal</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>A list of all the items selected</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card col-7">
                <div class="card-header">
                    <h3 class="card-title">Pharmacy</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" @click="showCart"><i class="fas fa-cart-plus"></i></button>
                        <button type="button" class="btn btn-tool"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="card-header">
                    <div class="form-group">
                        <label>Search *</label>
                        <input type="text" class="form-control" placeholder="Enter Name" id="query" name="query" v-model="searchForm.query" @change="searchItems()">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 1%">#</th>
                                <th style="width: 15%">Product Name</th>
                                <th style="width: 10%">Generic name</th>
                                <th style="width: 8%" class="text-center">Brand</th>
                                <th>Price</th>
                                <th style="width: 20%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, index) in all_products.data" :key="index">
                                <td style="width: 1%">{{index | addOne}}</td>
                                <td style="width: 15%">{{product.name}}<br />{{product.concentration}}/{{product.form}}</td>
                                <td style="width: 10%">{{product.category}}</td>
                                <td style="width: 8%" class="text-center">{{product.brand}}</td>
                                <td>&#x20A6; {{product.unit_price | currency}}</td>
                                <td style="width: 20%" class="project-actions text-right">
                                    <button class="btn btn-primary btn-sm" @click="addCart(product)"><i class="fas fa-cart-plus"></i>Buy</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination :data="all_products" @pagination-change-page="getProducts">
                        <span slot="prev-nav">&lt; Previous </span>
                        <span slot="next-nav">Next &gt;</span>
                    </pagination>
                </div>
            </div>
            <div class="card col-5">
                <form action="#" @submit.prevent="editMode ? editPrescription() : addPrescription()">
                    <div class="table-responsive rounded mb-3">
                        <alert-error :form="shoppingCart"></alert-error> 
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th style="width: 1%">#</th>
                                    <th style="width: 25%">Product Name</th>
                                    <th style="width: 15%">Price</th>
                                    <th style="width: 8%" class="text-center">Quantity</th>
                                    <th style="width: 20%">Sub Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body"> 
                                <tr v-for="(item, index) in shoppingCart.items" :key="index">
                                    <td>{{index | addOne}}</td>
                                    <td><div class="d-flex align-items-center">{{item.name}}</div></td>
                                    <td>{{item.unit_price}}</td>
                                    <td><input class="form-control" type="number" name="item.quantity" id="item.quantity" v-model="item.quantity" required /></td>
                                    <td>
                                        {{item.unit_price * item.quantity}}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-danger" title="Delete" v-on:click="deleteItem(item)"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfooter v-show="shoppingCart.items.length != 0">
                                <button class="btn btn-sm btn-primary" @click="purchaseItems">Submit</button>
                            </tfooter>
                        </table>
                    </div>                         
                </form>
            </div>
        </div>
        <!--
        <div class="card">
            <div class="card-header"><h3>Shopping Cart</div>
            <div class="card-body p-0">
                <table class="table mb-0 tbl-server-info">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th></th><th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body"> 
                        <tr v-for="(item, index) in shoppingCart.items" :key="index">
                            <td>{{index | addOne}}</td>
                            <td><div class="d-flex align-items-center">{{item.name}}</div>
                            <span>{{item.concentration}}/{{item.form}}</span></td>
                            <td><input class="form-control" type="number" name="item.quantity" id="item.quantity" v-model="item.quantity" /></td>
                            <td><input class="form-control" type="number" name="item.days" id="item.days" v-model="item.days" /></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-danger" title="Delete" v-on:click="deleteItem(item)"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
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
        </div>-->
        <!--PharmacyFormNew /> -->
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            all_products: {},
            editMode: false,
            pending: {},
            completed: {},
            products: {}, 
            searchForm: new Form({query:'',}),
            shoppingCart: new Form({
                appointment_id: null,
                items: [],
            })
        }
    },
    created() {
        this.getInitials();
        Fire.$on('Reload', response =>{
            
        });
    },
    methods:{
        addCart(product){
            this.shoppingCart.items.push(product);
        },
        addPrescription(){
            console.log(this.shoppingCart.items);
            this.shoppingCart.post('/api/pharmacy/products')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });    
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            this.$Progress.fail();
            });
        },
        deleteItem(item){
            this.shoppingCart.items.pop(item);
            //this.calculateCost();
        },
        getProducts(page=1){
            axios.get('/api/pharmacy/products/search?q='+this.searchForm.query+'&page='+page)
            .then(response=>{
                this.itemsRefresh(response)   
            });
        },
        getInitials(){
            axios.get('/api/pharmacy/products').then(response =>{
                this.productRefresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Pharmacy loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Pharmacy did not load successfully',
                })
            });
        },
        productRefresh(response){
            this.all_products = response.data.products;
        },
        purchaseItems(){
            //this.shoppingCart.post()
            console.log(this.shoppingCart.items);
        },
        searchItems(){
            axios.get('/api/pharmacy/products/search?q='+this.searchForm.query)
            .then(response=>{
                this.productRefresh(response);    
            });
        },
        showCart(){
            $('#modalShoppingCart').modal('show');
        }
    }
}
</script>