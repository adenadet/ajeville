<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="storeItemModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Store Item Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryDetailStoreItem :editMode="editMode" :store_item.sync="store_item" @reloadStoreItem="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="storeItemFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Update Store Item Setting</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormStoreItemSetting :editMode.sync="editMode" :store_item_setting.sync="store_item" @reloadStoreItem="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <InventoryFormItemSearch @item-search="searchItems" search_type="all_products" />
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Store Level</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary mr-1" @click="downloadCSV"><i class="fa fa-file-excel mr-1"></i>Download Excel</button>
                        <!--button class="btn btn-xs btn-info mr-1" @click="downloadPDF"><i class="fa fa-file-pdf mr-1"></i>Download PDF</button -->
                        <button class="btn btn-xs btn-danger" @click="resetStoreItems"><i class="fa fa-window-restore mr-1"></i> Reset</button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <table class="table table-hover text-nowrap table-striped">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Reorder<br />Level</th>
                                <th>Maximum<br />Level</th>
                                <th>Expiry<br />Notification</th>
                                <th>Balance</th>
                                <th>Received</th>
                                <th>Sold</th>
                                <th>Transferred</th>
                                <th>Issued</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="store_item in store_items.data">
                                <td>{{ store_item.item != null ? readMore(store_item.item.name, 30, '...') : 'Deleted Item' }}</td>
                                <td>{{ store_item.reorder_level ?? 'N/A' }}</td>
                                <td>{{ store_item.maximum_level ?? 'N/A' }}</td>
                                <td>{{ store_item.expiry_notification ?? 'N/A' }}</td>
                                <td>{{ store_item.total_balance ?? 0 }}</td>
                                <td class="text-success">{{ store_item.total_received ?? 0}}</td>
                                <td class="text-danger">{{ store_item.total_sold ?? 0 }}</td>
                                <td class="text-danger">{{ store_item.total_transferred ?? 0 }}</td>
                                <td class="text-danger">{{ store_item.total_issued ?? 0 }}</td>
                                <td>
                                    <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-dark"></i></button>
                                    <div class="dropdown-menu">
                                        <button class="btn btn-block dropdown-item" type="button" @click="viewStoreItem(store_item)"><i class="fa fa-eye mr-1"></i> View </button>
                                        <button class="btn btn-block dropdown-item" type="button" @click="editStoreItem(store_item)"><i class="fa fa-edit mr-1"></i> Edit Settings </button>
                                        <!--button class="btn btn-block dropdown-item" type="button" @click="rejectTransferOrder(store_item.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Request</button-->
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="store_items.per_page != null ? store_items.per_page : 52" :records="store_items.total != null ? store_items.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</section>
</template>
<script>
import InventoryDetailStoreItem from '@/inventory/details/StoreItem.vue';
import InventoryFormItemSearch from '@/inventory/forms/ItemSearch.vue';
import InventoryFormStoreItemSetting from '@/inventory/forms/StoreItemSetting.vue';
export default {
    components:{
        InventoryDetailStoreItem, InventoryFormItemSearch, InventoryFormStoreItemSetting
    },
    data() {
        return {
            current_page: 1,
            editMode: false,
            filterQuery: {},
            form: new Form({}),
            loading: false,
            store_item: {},
            store_items: {data:[], total: 0},
        }
    },
    methods:{
        closeModals() {
            $('#storeItemFormModal').modal('hide');
            $('#storeItemModal').modal('hide');
        },
        deactivateStoreItem(id) {
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
                if (result.value) {
                    this.form.delete('/api/inventory/store_items/' + id)
                    .then(response => {
                        this.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        async downloadCSV(){
            this.loading = true;
            try {
                const response = await axios.put('/api/inventory/store_items/'+this.$route.params.id+'/report/csv', this.filterQuery, {responseType: 'blob',});
                const contentDisposition = response.headers['content-disposition'];
                const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'store_item_stock_level.csv';

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
        async downloadPDF(){
            this.loading = true;
            try {
                const response = await axios.put('/api/inventory/store_items/' + this.$route.params.id + '/report/pdf', this.filterQuery, { responseType: 'blob' });

                const contentDisposition = response.headers['content-disposition'];
                const fileName = contentDisposition ? contentDisposition.split('filename=')[1].replace(/"/g, '') : 'store_item_stock_level.pdf';

                // Create a blob URL and force download
                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
            } 
            catch (error) {
                console.error('PDF Download failed:', error);
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to generate PDF report',
                });
            }
            this.loading = false;
        },
        editStoreItem(store_item){
            this.loading = true;
            this.editMode = true;
            //console.log(store_item);
            this.store_item = store_item;
            $('#storeItemFormModal').modal('show');
            this.loading = false;
        },
        getAllInitials(page = 1) {
            this.loading = true;
            axios.get('/api/inventory/store_items/'+this.$route.params.id+'?page='+page)
            .then(response => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Transfer Requests loaded successfully',
                });
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Transfer Requests not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.store_items = response.data.store_items;
            this.closeModals();
        },
        resetStoreItems(){
            this.loading = true;
            axios.get('/api/inventory/store_items/'+this.$route.params.id+'/reset')
            .then(response => {
                this.refreshPage(response);
            })
            .catch(error => {});
            this.loading = false;
        },
        searchItems(filterQuery){
            this.loading = true;
            this.filterQuery = filterQuery;
            axios.put('/api/inventory/store_items/search/'+this.$route.params.id, filterQuery)
            .then(response => {
                this.refreshPage(response);
            })
            .catch(error => {});
            this.loading = false;
        },
        viewStoreItem(store_item){
            this.loading = true;
            this.store_item = store_item;
            $('#storeItemModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>