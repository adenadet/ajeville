<template>
    <section class="container-fluid">
        <div class="modal fade" id="itemModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title" v-show="editMode">Edit Item: {{package.name}}</h4>
                        <h4 class="modal-title" v-show="!editMode">New Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <InventoryFormItem :editMode="editMode" :item.sync="package" @itemReload="getInitials(current_page)" item_type="package"/> 
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">All Packages</h3>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-xs" @click="addItem">Add New Item</button>
                        </div>
                    </div>
                    <InventoryDetailItemList view="package" :items="packages.data" />
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="packages.per_page != null ? packages.per_page : 52" :records="packages.total != null ? packages.total : 550" >
                        </pagination>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            loading: false,
            packages: {data:[]},
            package: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addPackage(){
            this.loading = true;
            this.editMode = false;
            this.item = {};
            //Fire.$emit('ItemDataFill', {});
            $('#itemModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#itemModal').modal('hide');
        },
        editItem(item){
            this.loading = true;
            this.editMode = true;
            this.item = item;
            $('#itemModal').modal('show');
            this.loading = false;  
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/inventory/items?type=package&page='+this.current_page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Items loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Items not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.packages = response.data.items;
            this.closeModals();
        },
    },
}
</script>