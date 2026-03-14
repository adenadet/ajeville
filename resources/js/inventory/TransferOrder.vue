<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-12 p-0 overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <InventoryDetailTransferOrder :transfer_order.sync="transfer_order" @transferOrderReload="getAllInitials"/>
        </div>
    </div>
</section>
</template>
<script>
import InventoryDetailTransferOrder from '@/inventory/details/TransferOrder.vue';
export default {
    components:{InventoryDetailTransferOrder},
    data(){
        return  {
            editMode: false,
            form_type: '',
            loading: false,
            transfer_order: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        acceptRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "accept";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        approveRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "approve";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        closeModal(){},
        createItem(){
            this.loading = true;
            this.ItemData.post('/api/inventory/transfer_orders')
            .then(response =>{
                this.loading = false;
                this.$emit('Reload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/inventory/transfer_orders/'+this.$route.params.id+'?t=unique_id')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Transfer Orders loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transfer Orders not loaded successfully',
                })
            });
        },
        issueRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "issue";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        refreshPage(response){
            this.transfer_order = response.data.transfer_order;
        },
    },
}
</script>