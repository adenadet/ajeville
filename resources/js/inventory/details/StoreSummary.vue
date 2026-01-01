<template>
<section class="overlay-wrapper">
    <div class="card">
        <div class="card-header bg-dark">
            Details
        </div>
        <div class="card-body">
            <div class="card-body p-0">
                <strong><i class="fas fa-warehouse mr-1"></i>Store Name:</strong><p class="text-muted">{{ store.name }}</p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i>Branch:</strong><p class="text-muted">{{ store.branch != null ? store.branch.name : (store.branch_id == 0 ? 'Headquarter': 'N/A') }}</p>
                <hr>
                <strong><i class="fas fa-pencil-alt mr-1"></i> Department</strong><p class="text-muted">{{ store.department != null ? store.department.name : 'N/A' }}</p>
                <hr>
                <strong><i class="far fa-file-alt mr-1"></i> Description</strong><p class="text-muted" v-html="store.description"></p>
            </div>
        </div>
    </div>
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