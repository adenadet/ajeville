<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateOrderItem() : createOrderItem()">
        <div class="row"  v-if="purchase_order_item != null">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Purchase Order</label>
                    <div class="form-control">
                        {{ purchase_order_unique_id }}
                    </div>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Taxes</label>
                    <input type="number" class="form-control" id="package_id" name="package_id" v-model="additionalCostData.taxes" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Logistics</label>
                    <input class="form-control" type="number" id="logistics" name="logistics" v-model="purchaseOrderItemData.logistics" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Discount</label>
                    <input class="form-control" type="number" id="discount" name="discount" v-model="purchaseOrderItemData.discount" />
                </div>
            </div>
        </div>
        <!--div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Total Quantity in Units</label>
                    <div class="form-control" id="total_quantity" name="quantity">{{ total_quantity }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Total Price</label>
                    <div class="form-control" id="total_price" name="total_price">{{ currency(total_price) }}</div>
                </div>
            </div>    
        </div-->
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary">{{ editMode ? 'Update' : 'Submit'}}</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    computed:{
        total_price(){
            if ((this.purchaseOrderItemData.quantity == 0) || (this.purchaseOrderItemData.unit_price == 0)  ){
                return 0;
            }
            else{
                return this.purchaseOrderItemData.quantity * this.purchaseOrderItemData.unit_price;
            }
        },
        total_quantity(){
            if ((this.purchaseOrderItemData.package_quantity == 0) || (this.purchaseOrderItemData.quantity == 0)  ){
                return 0;
            }
            else{
                return this.purchaseOrderItemData.package_quantity * this.purchaseOrderItemData.quantity;
            }
        }
        
    },
    data(){
        return  {
            categories: [],
            loading: false,
            items: [],
            package_types: [],
            purchaseOrderItemData: new Form({
                id: '',
                po_id: '', 
                item_id: '',
                quantity: '', 
                approved_quantity: '',
                package_id: 1,
                package_quantity: 1, 
                unit_price: '',
                status: '',
            }),
        }
    },
    emits: ['vendorReload'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createOrderItem(){
            this.loading = true;
            this.purchaseOrderItemData.po_id = this.purchase_order.id;
            this.purchaseOrderItemData.post('/api/procurement/purchase_order_items')
            .then(response =>{
                this.loading = false;
                this.$emit('purchaseOrderReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Purchase Order Item has been created',
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
        getInitials(){
            this.loading = true;
            axios.get('/api/procurement/purchase_order_items/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Purchase Order Item Form did not load successfully',
                })
            });
        },
        refreshPage(response){
            this.package_types = response.data.package_types;
            this.items = response.data.items;
            //this.departments = response.data.departments;
        },
        updateOrderItem(){
            this.loading = true;
            this.purchaseOrderItemData.put('/api/procurement/purchase_order_items/'+this.purchaseOrderItemData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('purchaseOrderReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Purchase Order Item has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });              
        },
    },
    props:{
        editMode: Boolean,
        purchase_order: Object,
        purchase_order_item: Object,
        purchase_order_unique_id: String,
        source: String,
    },
    watch:{
        purchase_order_item(){
            this.purchaseOrderItemData.fill(this.purchase_order_item);
        }
    }
}
</script>