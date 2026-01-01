<template>
    <section class="overlay-wrapper">
        <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form @submit.prevent="updateAdditionalCost()">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Taxes</label>
                        <input class="form-control" v-model="additionalCostData.taxes" type="number" name="taxes" id="taxes">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Logistics</label>
                        <input class="form-control" v-model="additionalCostData.logistics" type="number" name="logistics" id="logistics">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Discount</label>
                        <input class="form-control" v-model="additionalCostData.discount" type="number" name="discount" id="discount">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-sm btn-primary" type="submit">Edit</button>
                </div>
            </div>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            additionalCostData: new Form({
                additional_cost: '',
                taxes: '',
                logistics: '',
                discount: '',
            }),
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        updateAdditionalCost(){
            this.loading = true;
            this.additionalCostData.put('/api/procurement/purchase_orders/update/'+this.purchase_order.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'Additional Cost has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
    },
    props: {
        editMode: Boolean,
        item_type: String,
        item: Object,
        purchase_order: Object,
    },
    watch:{
        item(){
            if (this.item_type == 'purchase_order'){this.additionalCostData.po_id = this.item.id;}
            else if (this.item_type == 'work_order'){this.additionalCostData.wo_id = this.item.id;}
        },
        purchase_order(){
            this.additionalCostData.additional_cost = this.purchase_order.additional_cost;
            this.additionalCostData.taxes = this.purchase_order.taxes;
            this.additionalCostData.logistics = this.purchase_order.logistics;
            this.additionalCostData.discount = this.purchase_order.discount;
        }
    }
}
</script>