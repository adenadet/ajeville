<template>
<section>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Categories</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item active">
                        <router-link to="/admin_area/courses" class="nav-link"><i class="fa fa-book"></i> All Categories</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/admin_area/exams" class="nav-link"><i class="fas fa-user-class"></i> </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/admin_area/results" class="nav-link"><i class="fas fa-user-cog"></i> Results </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9"></div>
        </div>
        <div class="row">
            
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            all_products: {},
            categories: {},
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