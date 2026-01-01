<template>
<section class="overlay-wrapper">
    <div class="overlay dark"></div>
    A stock level should show the Store Name, Item Name, Quantity In Store, 
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            item: {},
            items: {},
            purchase_orders: {},
            transfer_orders: {},
            locations: {},
        }
    },
    mounted() {
        //this.getInitials()
    },
    methods:{
        refreshPage(response){
            //this.item = response.data.item;
            this.transfer_orders = response.data.transfer_orders;
            this.locations = response.data.locations;
            this.purchase_orders = response.data.purchase_orders;
        },
    },
    props:{
        store: Object,
    }
}
</script>