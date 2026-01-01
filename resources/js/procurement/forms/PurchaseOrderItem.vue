<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateOrderItem() : createOrderItem()">
        <div class="row"  v-if="purchase_order_item != null">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Purchase Order</label>
                    <div class="form-control">
                        {{ po_id }}
                    </div>
                </div>
            </div>
                
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Item</label>
                    <div class="form-control" v-if="editMode">
                        {{ purchase_order_item.item != null ? purchase_order_item.item.name : 'Error' }}
                    </div>
                    <Multiselect v-else v-model="purchaseOrderItemData.item_id" :options="items" track-by="name" label="name" :searchable="true" :close-on-select="false" :show-labels="false" placeholder="Pick a item"></multiselect>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Package</label>
                    <select class="form-control" id="package_id" name="package_id" v-model="purchaseOrderItemData.package_id">
                        <option value="">--Select Package Type--</option>
                        <option v-for="package_type in package_types" :value="package_type.id">{{ package_type.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Package Quantity</label>
                    <input class="form-control" id="package_quantity" name="package_quantity" v-model="purchaseOrderItemData.package_quantity">
                </div>
            </div>
            <div class="col-md-6" v-if="source != 'approval'">
                <div class="form-group">
                    <label>Requested Quantity</label>
                    <input class="form-control" id="quantity" name="quantity" v-model="purchaseOrderItemData.quantity" />
                </div>
            </div>
            <div class="col-md-3" v-if="source == 'approval'">
                <div class="form-group">
                    <label>Requested Quantity</label>
                    <div class="form-control" id="quantity" name="quantity" v-html="purchaseOrderItemData.quantity"></div>
                </div>
            </div>
            <div class="col-md-3" v-if="source == 'approval'">
                <div class="form-group">
                    <label>Approved Quantity</label>
                    <input class="form-control" id="quantity" name="quantity" v-model="purchaseOrderItemData.approved_quantity" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Package Price</label>
                    <input class="form-control" id="quantity" name="quantity" v-model="purchaseOrderItemData.unit_price" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
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
        </div>
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
    emits: ['purchaseOrderItemFormReload'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createOrderItem(){
            this.loading = true;
            this.purchaseOrderItemData.po_id = this.po_id;
            this.purchaseOrderItemData.post('/api/procurement/purchase_order_items')
            .then(response =>{
                this.$emit('purchaseOrderItemFormReload');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Purchase Order Item has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
                this.purchaseOrderItemData.reset();
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            });  
            this.loading = false;
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
                this.$emit('purchaseOrderItemFormReload', response);
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
        purchase_order_item: Object,
        po_id: String,
        source: String,
    },
    watch:{
        purchase_order_item(){
            this.purchaseOrderItemData.fill(this.purchase_order_item);
        }
    }
}
</script>