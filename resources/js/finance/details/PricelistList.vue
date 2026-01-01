<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="priceListFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Price List Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormPricelist :editMode.sync="editMode" :price_list.sync="price_list" @refreshPriceListForm="refreshList" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Status</th>
                <th><button class="btn btn-sm btn-primary" @click="addPriceList" :disabled="loading"><i class="fas fa-plus mr-1"></i> Add</button></th>
            </tr>
        </thead>
        <tbody v-if="price_lists.length > 0">
            <tr v-for="(price_list, index) in price_lists" :key="price_list.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ price_list.name }}</td>
                <td>{{ price_list.type_name }}</td>
                <td :title="price_list.description" v-html="readMore(price_list.description, 25, '...')"></td>
                <td>{{ FullName(price_list.creator) }}<br /><span class="text-muted">{{ ExcelDate(price_list.created_at) }}</span></td>
                <td>{{ FullName(price_list.updater) }}<br /><span class="text-muted">{{ ExcelDate(price_list.updated_at) }}</span></td>
                <td>{{ price_list.status == 1 ? 'Active' : 'Deactivated' }}</td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link v-if="source == 'emr'" class="btn btn-block dropdown-item" :to="'/emr/operations/price_lists/'+price_list.id"><i class="fas fa-eye mr-1"></i> View Price List</router-link>
                        <router-link v-else-if="source == 'ops'" class="btn btn-block dropdown-item" :to="'/operations/settings/price_lists/'+price_list.id"><i class="fas fa-eye mr-1"></i> View Price List</router-link>
                        <router-link v-else class="btn btn-block dropdown-item" :to="'/finance/settings/price_lists/'+price_list.id"><i class="fas fa-eye mr-1"></i> View Price List</router-link>
                        <button class="btn btn-block dropdown-item" @click="editPricelist(price_list)"><i class="fas fa-edit mr-1 text-primary"></i> Update Pricelist</button>
                        <button class="btn btn-block dropdown-item" v-if="price_list.status == 0" @click="deactivatePricelist(price_list)"><i class="fas fa-recycle mr-1 text-success"></i> Reactivate Pricelist</button>
                        <button class="btn btn-block dropdown-item" v-if="price_list.status == 1" @click="deactivatePricelist(price_list)"><i class="fas fa-trash mr-1 text-danger"></i> Deactivate Pricelist</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Price List meets your requirement</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            price_list: {},
        }
    },
    emits:['refreshPriceLists'],
    mounted() {},
    methods: {
        addPriceList(){
            this.loading = true;
            this.editMode = false;
            this.price_list = {};
            $('#priceListFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#priceListModal').modal('hide');  
            $('#priceListFormModal').modal('hide');  
        },
        deactivatePricelist(price_list){
            alert(price_list.status);
            this.$swal.fire({
                title: 'Are you sure?',
                text: price_list.status == 1 ? "This Pricelist would be deactivated and not available for assignment" : "This Pricelist would be reactivated and now be available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/price_lists/'+price_list.id)
                    .then(response=>{
                        this.$emit('refreshPriceLists');
                        this.$swal.fire('Success!', response.data.message, response.data.icon);
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editPricelist(price_list){
            this.loading = true;
            this.editMode = true;
            this.price_list = price_list;
            $('#priceListFormModal').modal('show');
            this.loading = false;  
        },
        refreshList(){
            this.closeModal();
            this.$emit('refreshPriceLists');            
        },
        viewPricelist(price_list){
            this.price_list = price_list;
            $('#branchAccountModal').modal('show');
        },
    },
    props:{
        source: String,
        price_lists: {type: Array, default: () => [],}
    }
}
</script>