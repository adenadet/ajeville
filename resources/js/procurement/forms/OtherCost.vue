<template>
<section>
    <form @submit.prevent="updateOtherCost()">
        <div class="row">
            <div class="col-md-12" v-if="item_type == 'purchase_order'">
                <div class="form-group">
                    <label>Purchase Order</label>
                    <select v-if="item == null || item.id == null" class="form-control" v-model="otherCostData.id">
                        <option value="">--Select Purchase Order </option>
                        <option v-for="purchase_order in purchase_orders" :value="purchase_order.id">{{ purchase_order.name }} [{{ purchase_order.unique_id }}]</option>
                    </select>
                    <div class="form-control">
                        {{ item.name }} [{{ item.unique_id }}]
                        <input type="hidden" v-model="otherCostData.id">
                    </div>
                </div>
            </div>
            <div class="col-md-12" v-else-if="item_type == 'work_order'">
                <div class="form-group">
                    <label>Work Order</label>
                    <select v-if="item == null || item.id == null" class="form-control" v-model="otherCostData.id">
                        <option value="">--Select Work Order </option>
                        <option v-for="work_order in work_orders" :value="work_order.id">{{ work_order.name }} [{{ work_order.unique_id }}]</option>
                    </select>
                    <div class="form-control">
                        {{ item.name }} [{{ item.unique_id }}]
                        <input type="hidden" v-model="otherCostData.id">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tax</label>
                    <input type="number" class="form-control" name="taxes" id="taxes"  v-model="otherCostData.taxes">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Logistics</label>
                    <input type="number" class="form-control" name="logistics" id="logistics" v-model="otherCostData.logistics">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Discount</label>
                    <input type="number" class="form-control" name="taxes" id="taxes"  v-model="otherCostData.discount">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" type="submit">Update</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    computed:{
        all_vendors(){
            if (this.assignVendorData.category_id != '') {
                return this.vendors.filter(vendor => vendor.category_id === this.assignVendorData.category_id);
            }
    
            else{ return this.vendors}
        }
    },
    data() {
        return {
            otherCostData: new Form({
                change: 'other_costs',
                id: '',
                discount: '',
                logistics: '',
                taxes: '',
                additional_cost: {},
            }),
            categories: [],
            purchase_orders: [],
            vendors: [],
            work_orders: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {        
        getAllInitials() {
            this.loading = true;
            axios.get('/api/procurement/vendors/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.vendors = response.data.vendors;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        updateOtherCost(){
            this.loading = true;
            let address = this.item_type == 'purchase_order' ? '/api/procurement/purchase_orders/'+this.item.id : '/api/procurement/work_orders/'+this.item.id
            this.otherCostData.put(address)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Other Cost has been updated', showConfirmButton: false, timer: 1500});
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
    },
    watch:{
        item(){
            /*if (this.item_type == 'purchase_order'){this.otherCostData.id = this.item.id;}
            else if (this.item_type == 'work_order'){this.otherCostData.id = this.item.id;}*/
            this.otherCostData.fill(this.item);
        },
    }
}
</script>