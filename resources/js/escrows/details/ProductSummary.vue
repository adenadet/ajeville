<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card m-0">
        <div class="card-header bg-dark">
            <h3 class="card-title text-white">{{ product.description }}</h3>
        </div>
        <div class="card-body overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <strong><i class="fas fa-book mr-1"></i> Name</strong>
            <p class="text-muted" v-html="product.description"></p>
            <hr>
            <strong><i class="fas fa-clipboard mr-1"></i> Description</strong>
            <p class="text-muted" v-html="product.details"></p>
            <hr>
            <strong><i class="fas fa-money-bill mr-1"></i> Price</strong>
            <p class="text-muted">{{ currency(product.unit_price) }}</p>
            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Created</strong>
            <p class="text-muted">{{ ExcelDate(product.created_at) }}</p>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            loading: false,
        }
    },
    methods:{
        addImage(){},
        getAllInitials(){
            axios.get('/api/ums/customers/'+this.$route.params.id)
            .then(response =>{
                this.reloadPage(response);
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Customers not loaded successfully',});
            });
        },
        reloadPage(response){
            this.customer = response.data.customer;
            this.staffs   = response.data.staffs;
        }
    },
    mounted() {},
    props:{
        product: Object,
    },
    watch:{
        product(){
            
        }
    }
}
</script>