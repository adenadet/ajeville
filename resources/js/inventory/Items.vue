<template>
<section class="container-fluid">
    <div class="modal fade" id="itemImportModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Upload Items</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormItemImport @itemReload="getAllInitials(current_page)"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="itemModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Item: {{item.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormItem :editMode="editMode" :item.sync="item" @itemReload="getAllInitials(current_page)"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">All Items</h3>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 450px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary mr-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="source" @change="getAllInitials">
                                    <option value="inactive">Inactive</option>
                                    <option value="active">Active</option>
                                    <option value="all">All</option>
                                </select>
                                <button type="button" class="btn btn-primary ml-1" @click="addItem" title="Add New Item"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success ml-1" @click="uploadItems" title="Upload Items"><i class="fa fa-upload"></i></button>
                                <button type="button" class="btn btn-info ml-1" @click="downloadItems" title="Download All Items"><i class="fa fa-download"></i></button>
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="card-body overlay-wrapper table-responsive p-0" style="height: 600px;">
                    <InventoryDetailItemList :items.sync="items.data" @itemsReload="getAllInitials"/>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="items.per_page != null ? items.per_page : 52" :records="items.total != null ? items.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import InventoryDetailItemList from '@/inventory/details/ItemList.vue';
import InventoryFormItem from '@/inventory/forms/Item.vue';
export default {
    components:{
        InventoryDetailItemList, InventoryFormItem
    },
    data(){
        return  {
            current_page: 1,
            editMode: false,
            items: {data: [], total: 0},
            item: {},
            loading: false,
            query: '',
            source: 'all',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addItem(){
            this.loading = true;
            this.editMode = false;
            this.item = {};
            $('#itemModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#itemModal').modal('hide');
        },
        async downloadItems(){
            this.loading = true;
            try {
                const response = await axios.post('/api/inventory/items/report', {query: this.query, source: this.source, type: 'csv'}, {responseType: 'blob',});
                const contentDisposition = response.headers['content-disposition'];
                const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'all_items_list.csv';

                // Create a blob URL and force download
                const blob = new Blob([response.data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
            } 
            catch (error) {
                console.error('Download failed:', error);
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to generate report',
                });
            }
            this.loading = false;
        },
        editItem(item){
            this.loading = true;
            this.editMode = true;
            this.item = item;
            //Fire.$emit('ItemDataFill', item);
            $('#itemModal').modal('show');
            this.loading = false;  
        },
        getAllInitials(){
            this.loading = true;
            this.closeModals();
            axios.get('/api/inventory/items?query='+this.query+'&type='+this.source+'&page='+this.current_page)
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
            this.items = response.data.items;
            this.closeModals();
        },
        uploadItems(){
            this.loading = true;
            $('#itemImportModal').modal('show');
            this.loading = false;  
        },
    },
}
</script>