<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="order == null">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>
    <strong><i class="fas fa-book mr-1"></i> Customer</strong>
    <p class="text-muted">{{ order.customer_id != 0 && order.customer_id != null && order.customer != null ? order.customer.name : 'Walk In Customer' }}</p>
    <hr>
    <strong><i class="fas fa-map-marker-alt mr-1"></i> Order Date</strong>
    <p class="text-muted">{{ ExcelDate(order.date) }}</p>

    <hr>
    <strong><i class="fas fa-pencil-alt mr-1"></i> Items</strong>
    <p class="text-muted">
        <span class="tag tag-danger">{{ order.order_items.length }} item(s)</span>
    </p>

    <hr>
    <strong><i class="fas fa-money-bill mr-1"></i> Total</strong>
    <p class="text-muted">
        <span class="tag tag-danger">{{ currency( sub_total + (sub_total * 0.075) + order.logistics - order.discount) }}</span>
    </p>

    <hr>

    <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

    <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
</section>
</template>
<script>
export default {
    computed:{
        sub_total(){
            if (!this.order.order_items?.length) return 0;
            return this.order.order_items.reduce(
                (sum, it) => sum + it.unit_price * it.quantity, 0);
        },
    },
    data(){
        return {
        }
    },
    methods:{
        
    },
    mounted() {
    
    },
    props:{
        order: Object,
    },
    watch:{
    
    },
}
</script>
