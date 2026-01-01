<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="((work_order_item == null || work_order_item.id == null) ? ((work_order == null || work_order.id == null) ? outputOrderItem() : createOrderItem()) : (editMode  ? updateOrderItem() : createOrderItem()))">
        <div class="row"  v-if="((work_order != null) && (work_order.unique_id != null))">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Work Order</label>
                    <div class="form-control">
                        {{ work_order.unique_id }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Item</label>
                    <QuillEditor class="form-control" contentType="html" name="description" id="description" v-model:content="workOrderItemData.item" placeholder="Detailed Work/Service Description"></QuillEditor>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Requested Quantity</label>
                    <input class="form-control" id="quantity" name="quantity" v-model="workOrderItemData.quantity" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Unit Price</label>
                    <input class="form-control" id="quantity" name="quantity" v-model="workOrderItemData.unit_price" />
                </div>
            </div>
            <div class="col-md-4">
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
import { QuillEditor } from '@vueup/vue-quill';

export default {
    computed:{
        total_price(){
            if ((this.workOrderItemData.quantity == 0) || (this.workOrderItemData.unit_price == 0)){
                return 0;
            }
            else{
                return this.workOrderItemData.quantity * this.workOrderItemData.unit_price;
            }
        },
    },
    data(){
        return  {
            categories: [],
            loading: false,
            items: [],
            package_types: [],
            workOrderItemData: new Form({
                id: '',
                wo_id: '', 
                item: '',
                quantity: '', 
                unit_price: '',
                status: '',
            }),
        }
    },
    emits: ['workOrderReload', 'addItem'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createOrderItem(){
            this.loading = true;
            this.workOrderItemData.wo_id = this.work_order.id;
            this.workOrderItemData.post('/api/procurement/work_order_items')
            .then(response =>{
                this.loading = false;
                this.$emit('workOrderReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Work Order Item has been created',
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
            axios.get('/api/procurement/work_order_items/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Work Order Item Form did not load successfully',
                })
            });
        },
        outputOrderItem(){
            this.$emit('addItem', this.workOrderItemData);
        },
        refreshPage(response){
            this.package_types = response.data.package_types;
            this.items = response.data.items;
            //this.departments = response.data.departments;
        },
        updateOrderItem(){
            this.loading = true;
            this.workOrderItemData.put('/api/procurement/work_order_items/'+this.workOrderItemData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('workOrderReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Work Order Item has been updated',
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
        work_order: Object,
        work_order_item: Object,
        work_order_unique_id: String,
        source: String,
    },
    watch:{
        work_order_item(){
            this.workOrderItemData.fill(this.work_order_item);
        }
    }
}
</script>