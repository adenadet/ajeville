<template>
<section class="content">
    <div class="container-fluid overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner"><h3>{{ user_stores != null ? user_stores.total : 0 }}</h3><p>Active Stores</p></div>
                    <div class="icon"><i class="fa fa-warehouse"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner"><h3>{{ pending_in.total + pending_out.total }}</h3><p>Pending Transfer Orders</p></div>
                    <div class="icon"><i class="fab fa-stack-overflow"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner"><h3>{{ soon_to_expire_items != null ? soon_to_expire_items.total : 0 }}</h3><p>Soon to Expire</p></div>
                    <div class="icon"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner"><h3>{{ expired_items != null ? expired_items.total : 0 }}</h3><p>Expired Goods</p></div>
                    <div class="icon"><i class="fa fa-calendar-times"></i></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Soon To Expire Items</h3>
                        <!--div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div-->
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <InventoryDetailStoreItemList :store_items.sync="soon_to_expire_items.data" source="Soon To Expire" view="dashboard"/>
                    </div>
                </div>   
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Expired Items</h3>
                        <!--div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div-->
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <InventoryDetailStoreItemList :store_items.sync="expired_items.data" source="expired" view="dashboard"/>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Incoming Transfer Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 500px;">
                        <InventoryDetailTransferOrderList :transfer_orders.sync="expired_items.data" source="expired" view="dashboard"/>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Outgoing Transfer Orders</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 500px;">
                        <InventoryDetailTransferOrderList :transfer_orders.sync="expired_items.data" source="expired" view="dashboard"/>
                    </div>
                </div>
            </div>
        </div-->
    </div>
</section>
</template>
<script>
import InventoryDetailStoreItemList from '@/inventory/details/StoreItemList.vue';
export default {
    components:{
        InventoryDetailStoreItemList
    },
    data(){
        return {
            current_page: 1,
            editMode: false,
            expired_items: {},
            form: new Form({}),
            loading: false,
            soon_to_expire_items: {},
            user_stores: {},
            expired_items: {data: [],},
            pending_in: {data: [],},
            pending_out: {data: [],},
        }
    },
    methods:{
        createNotice(){
            this.editMode = false;
            this.notice = {};
            $('#noticeModal').modal('show');
        },
        deleteNotice(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/notices/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Inventory dashboard has been deleted.', 'success');
                        //Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/inventory/dashboard?t=all&page='+page)
            .then(response =>{
                this.reset(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Inventory dashboard loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Inventory dashboard not loaded successfully',});
            });
        },
        reset(response){
            this.expired_items = response.data.expired_items
            this.soon_to_expire_items = response.data.soon_to_expire_items;
            this.user_stores = response.data.my_stores;
            this.pending_in = response.data.pending_in;
            this.pending_out = response.data.pending_out;
        },
    },
    mounted() {
        this.getAllInitials();
    }   
}
</script>