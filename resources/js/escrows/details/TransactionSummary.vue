<template>
<section>
    <div class="card bordered">
        <div class="card-header bg-success"><h3 class="card-title">Transaction Summary [{{ transaction.unique_code }}]</h3>
            <div class="card-tools">
                <button class="btn btn-tool"><i class="fa fa-edit"></i></button>
            </div>
        </div>
        <div class="card-body">
            <strong><i class="fa fa-user mr-1"></i> Buyer</strong>
            <p class="text-muted">{{ FullName(transaction.buyer) }}</p>
            <hr>
            <strong><i class="fa fa-user mr-1"></i> Seller</strong>
            <p class="text-muted">{{ FullName(transaction.seller) }}</p>
            <hr>
            <strong><i class="fa fa-box mr-1"></i> Product</strong>
            <p class="text-muted">{{ transaction.title }}</p>
            <hr>
            <strong><i class="fa fa-pencil-alt mr-1"></i> Details</strong>
            <div class="text-muted" v-html="transaction.product != null ? transaction.product.details : 'Default Product'"></div>
            <hr>
            <strong><i class="fa fa-file mr-1"></i> Other Specifics</strong>
            <div class="text-muted" v-html="transaction.product != null ? transaction.product.detailed : 'Default Product Specifics'"></div>
        </div>
    </div>    
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            owner_id: 1,
            style: 'grid',
        }
    },
    emits:['refreshPage'],
    methods:{
        
    },
    mounted() {},
    props:{
        transaction: Object,
        user_id: Number,
    },
    watch:{
        user_id(){
            if (this.user_id != null){
                this.owner_id = this.user_id;
            }
        }
    }
}
</script>