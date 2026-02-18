<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="card">
            <div class="card-header bg-navy">
                <h3 class="card-title">Delivery Notes</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height:600px;">
                <SalesDetailDeliveryNoteList :delivery_notes.sync="delivery_notes.data" source="main"/>
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="delivery_notes.per_page != null ? delivery_notes.per_page : 52" :records="delivery_notes.total != null ? delivery_notes.total : 550" ></pagination>
            </div>
        </div>
    </section>
</template>
<script>
import SalesDetailDeliveryNoteList from '@/sales_orders/details/DeliveryNoteList.vue';
import SalesFormDeliveryNote from '@/sales_orders/forms/DeliveryNote.vue';
export default {
    components: {
        SalesDetailDeliveryNoteList, SalesFormDeliveryNote 
    },
    data(){
        return {
            current_page: 1,
            delivery_notes: {data:[], total: 0,},
            items: 0,
            loading: false,
            orders: {total: 0,},
            returns: 0,
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/sales/delivery_notes')
            .then(response =>{
                this.delivery_notes = response.data.delivery_notes;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Dashboard not loaded successfully',});
            });
            this.loading = false;
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>