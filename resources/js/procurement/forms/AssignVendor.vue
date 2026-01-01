<template>
    <section>
        <form @submit.prevent="assignVendor()">
            <div class="row">
                <div class="col-md-12" v-if="item_type == 'purchase_order'">
                    <div class="form-group">
                        <label>Purchase Order</label>
                        <select v-if="item == null || item.id == null" class="form-control" v-model="assignVendorData.po_id">
                            <option value="">--Select Purchase Order </option>
                            <option v-for="purchase_order in purchase_orders" :value="purchase_order.id">{{ purchase_order.name }} [{{ purchase_order.unique_id }}]</option>
                        </select>
                        <div class="form-control">
                            {{ item.name }} [{{ item.unique_id }}]
                            <input type="hidden" v-model="assignVendorData.po_id">
                        </div>
                    </div>
                </div>
                <div class="col-md-12" v-else-if="item_type == 'work_order'">
                    <div class="form-group">
                        <label>Work Order</label>
                        <select v-if="item == null || item.id == null" class="form-control" v-model="assignVendorData.wo_id">
                            <option value="">--Select Work Order </option>
                            <option v-for="work_order in work_orders" :value="work_order.id">{{ work_order.name }} [{{ work_order.unique_id }}]</option>
                        </select>
                        <div class="form-control">
                            {{ item.name }} [{{ item.unique_id }}]
                            <input type="hidden" v-model="assignVendorData.wo_id">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" v-model="assignVendorData.category_id">
                            <option value="">--Select Category --</option>
                            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Vendor</label>
                        <select class="form-control" v-model="assignVendorData.vendor_id">
                            <option value=""></option>
                            <option v-for="vendor in all_vendors" :key="vendor.id" :value="vendor.id">{{vendor.name}}</option>
                        </select>
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
            assignVendorData: new Form({
                category_id: '',
                po_id: '',
                vendor_id: '',
                wo_id: '',
            }),
            categories: [],
            purchase_orders: [],
            vendors: [],
            work_orders: [],
        }
    },
    emits:['refreshAssignVendor'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        assignVendor(){
            this.loading = true;
            let address = this.item_type == 'purchase_order' ? '/api/procurement/purchase_orders/assign_vendor' : '/api/procurement/work_orders/assign_vendor'
            this.assignVendorData.post(address)
            .then(response =>{
                this.$emit('refreshAssignVendor');
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Vendor has been assigned', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
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
        
    },
    props: {
        editMode: Boolean,
        item_type: String,
        item: Object,
        //purchase_order: Object,
    },
    watch:{
        item(){
            if (this.item.vendor != null){
                this.assignVendorData.vendor_id = this.item.vendor.id;
                this.assignVendorData.category_id = this.item.vendor.category_id;
            }
            if (this.item_type == 'purchase_order'){this.assignVendorData.po_id = this.item.id;}
            else if (this.item_type == 'work_order'){this.assignVendorData.wo_id = this.item.id;}
        },
    }
}
</script>