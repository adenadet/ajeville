<template>
    <section>
        <form @submit.prevent="assignStore()">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group" v-if="purchase_order != null">
                        <label>Purchase Order</label>
                        <select v-if="purchase_order.unique_id == null" class="form-control" v-model="assignStoreData.po_id">
                            <option value="">--Select Purchase Order </option>
                            <option v-for="purchase_order in purchase_orders" :value="purchase_order.id">{{ purchase_order.name }} [{{ purchase_order.unique_id }}]</option>
                        </select>
                        <div v-else class="form-control">
                            {{ purchase_order.unique_id }}
                            <input type="hidden" v-model="assignStoreData.po_id">
                        </div>
                    </div>
                    <div class="form-group" v-if="work_order != null">
                        <label>Work Order</label>
                        <select v-if="work_order.unique_id == null" class="form-control" v-model="assignStoreData.po_id">
                            <option value="">--Select Purchase Order </option>
                            <option v-for="work_order in work_orders" :value="work_order.id">{{ work_order.name }} [{{ work_order.unique_id }}]</option>
                        </select>
                        <div v-else class="form-control">
                            {{ work_order.unique_id }}
                            <input type="hidden" v-model="assignStoreData.wo_id">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Branch</label>
                        <select class="form-control" v-model="assignStoreData.branch_id">
                            <option value="">--Select Branch --</option>
                            <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Store</label>
                        <select class="form-control" v-model="assignStoreData.store_id">
                            <option value=""></option>
                            <option v-for="store in all_stores" :key="store.id" :value="store.id">{{store.name}}</option>
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
        all_stores(){
            if (this.assignStoreData.category_id != '') {
                return this.stores.filter(store => store.category_id === this.assignStoreData.category_id);
            }
    
            else{ return this.stores}
        }
    },
    data() {
        return {
            assignStoreData: new Form({
                branch_id: '',
                po_id: '',
                store_id: '',
                wo_id: '',
            }),
            branches: [],
            purchase_orders: [],
            stores: [],
            work_orders: [],
        }
    },
    emits:['refreshAssignStore'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        assignStore(){
            this.loading = true;
            this.assignStoreData.post('/api/procurement/purchase_orders/assign_store')
            .then( () =>{
                this.$emit('refreshAssignStore',);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Store has been assigned', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/inventory/stores/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.branches = response.data.branches;
            this.stores = response.data.stores;
        },
    },
    props: {
        editMode: Boolean,
        item_type: String,
        item: Object,
        purchase_order: Object,
        work_order: Object,
    },
    watch:{
        purchase_order(){
            if (this.purchase_order == null){
                return;
            }
            else{
            this.assignStoreData.branch_id = this.purchase_order.store != null ? this.purchase_order.store.branch_id : '';
            this.assignStoreData.wo_id = '';
            this.assignStoreData.po_id = this.purchase_order.unique_id;
            this.assignStoreData.store_id = this.purchase_order.store_id;
            }
        },
        work_order(){
            if (this.work_order == null){
                return;
            }
            else{
                this.assignStoreData.branch_id = this.work_order.store != null ? this.work_order.store.branch_id : '';
                this.assignStoreData.wo_id = this.work_order.unique_id;
                this.assignStoreData.po_id = '';
                this.assignStoreData.store_id = this.work_order.store_id;
            }
        }
    }
}
</script>