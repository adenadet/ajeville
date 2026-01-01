<template>
    <div class="ribbon-wrapper ribbon-xl" v-if="order.status == 0">
        <div class="ribbon bg-info text-xl">Draft</div>
    </div>
    <div class="ribbon-wrapper ribbon-xl" v-if="order.status == 1">
        <div class="ribbon bg-warning text-xl">Unapproved</div>
    </div>
    <div class="ribbon-wrapper ribbon-xl" v-if="order.status == 2">
        <div class="ribbon bg-primary text-xl">
            Approved
        </div>
    </div>
    <div class="ribbon-wrapper ribbon-xl" v-if="order.status == 3">
        <div class="ribbon bg-warning text-xl">
            Ongoing
        </div>
    </div>
    <div class="ribbon-wrapper ribbon-xl" v-if="order.status == 4">
        <div class="ribbon bg-primary text-xl">
            Completed
        </div>
    </div>
    <div class="ribbon-wrapper ribbon-xl" v-if="order.status == 10">
        <div class="ribbon bg-danger text-xl">
            Rejected
        </div>
    </div>                  
</template>
<script>
export default {
    data() {
        return {
        };
    },
    methods: {
        
    },
    mounted(){
        //this.getAllInitials();
    },
    props:{
        order: Object,
    },
    watch:{
        order() {
        
        },
    },
};
</script>