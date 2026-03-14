<template>
    Get the list of items to be delivered, 

    Also select staff that is to deliver to customer

    Also state the address of the customer that it will be delivered to.
</template>
<script>
export default {
    data(){
        return {
            customers: {total: 0,},
            loading: false,
        }
    },
    methods:{
        viewDeliveryNote(delivery_note){
            this.loading = true;
            this.delivery_note = delivery_note;
            $('#deliveryNoteModal').modal('show');
            this.loading = false;
        },
        scrollHanle(evt) {
            console.log(evt)
        },
    },
    mounted() {
    },
    props:{
        delivery_note: Object,
    },
}
</script>