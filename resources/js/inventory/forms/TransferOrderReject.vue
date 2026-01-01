<template>
<section class="overlay-wrapper">
    <form>
        <alert-error :form="transferOrderData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <div class="form-control">{{ transfer_order.name }} [{{ transfer_order.unique_id }}]</div>
                    <input type="hidden" id="id" name="id" v-model="transferOrderData.id" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Note</label>
                    <QuillEditor content-type="html" theme="snow" rows="5" id="description" name="description" v-model:content="transferOrderData.note" />
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="transferOrderData.id" />
        <button @click.prevent="rejectTransferOrder()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            categories: [],
            drugs: [],
            loading: true,
            transferOrderData: new Form({
                barcode: '',
                classification_id: '',
                category_id: '',
                description: '',
                id: '', 
                image: '',
                items:[],
                last_landing_cost: '',
                name: '', 
                specific_id: '',
                category_id: '', 
                quantity: 0, 
                status: '',
                type_id: '',     
                unique_id: '',
            }),
            types: [],
        }
    },
    emits: ['itemReload'],
    mounted() {},
    methods:{
        rejectTransferOrder(){
            this.loading = true;
            this.transferOrderData.put('/api/inventory/transfer_orders/reject/'+this.transfer_order.id)
            .then(response =>{
                this.loading = false;
                this.$emit('itemReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Order has been rejected',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });  
        },
    },
    props:{
        transfer_order: Object,
    },
    watch:{
        transfer_order(){
            if (this.transfer_order != null ){this.transferOrderData.fill(this.transfer_order);}
            else{this.transferOrderData.reset();}
        }
    }
}
</script>