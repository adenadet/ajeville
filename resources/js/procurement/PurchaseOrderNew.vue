<template>
    <ProcurementDetailPurchaseOrder :purchase_order.sync="purchase_order" view="initials" @purchaseOrderReload="getAllInitials" />
</template>
<script>
import ProcurementDetailPurchaseOrder from './details/PurchaseOrder.vue';
export default {
    components:{ProcurementDetailPurchaseOrder},
    data(){
        return  {
            purchase_order: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        getInitials(){
            this.loading = true;
            axios.get('/api/procurement/purchase_orders/initiate')
            .then(response =>{
                this.purchase_order = response.data.purchase_order; 
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Form not loaded successfully',
                })
            });
            this.loading = false;
        },
        getAllInitials(){
             this.loading = true;
            axios.get('/api/procurement/purchase_orders/'+this.purchase_order.id)
            .then(response =>{
                this.purchase_order = response.data.purchase_order; 
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Form not loaded successfully',
                })
            });
            this.loading = false;
        }
    },
}
</script>