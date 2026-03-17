<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="itemFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-show="editMode">Edit Item: {{item.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormItem :editMode="editMode" :item.sync="item" @itemReload="refreshPage"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap table-hover table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Classification</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Last Landing Cost</th>
                <th>Avg. Landing Cost</th>
                <th>Status</th>
                <th><button type="button" class="btn btn-primary btn-xs ml-1" @click="addItem" title="Add New Item"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="items.length != 0 && items != null">
            <tr v-for="item in items" :key="item.id">
                <td :title="item.name">{{ readMore(item.name, 50, '...') }}</td>
                <td>{{ item.item_type?.name || 'Not Assigned' }}</td>
                <td>{{  serviceModule(item.service?.referenceable_type) || 'Not Assigned' }}</td>

                <td>{{ item.brand != null ? item.brand.name : 'Unbranded' }}</td>
                <td>{{ currency(item.last_landing_cost) }}</td>
                <td>{{ currency(item.current_cost_price) }}</td>
                <td>{{ firstUp(item.status) }}</td>
                <td>
                    <button type="button" class="btn btn-tool text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <router-link class="btn btn-block dropdown-item" :to="'./items/'+item.id"><i class="fa fa-eye mr-1"></i> View</router-link>
                        <button class="btn btn-block dropdown-item" @click="editItem(item)"><i class="fa fa-edit mr-1 text-primary"></i> Edit Item</button>
                        <button class="btn btn-block dropdown-item" @click="deactivateItem(item)"><i class="fa fa-recycle mr-1 text-danger"></i> Deactivate Item</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan=8 style="height: 550px;">No Item has been created here</td>
            </tr>
        </tbody>
    </table>
</section>

</template>
<script>
import InventoryFormItem from '@/inventory/forms/Item.vue';
export default {
    components:{
        InventoryFormItem
    },
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            item: {},
            loading: false,
        }
    },
    emits: ['itemsReload'],
    mounted() {
        //this.getInitials();
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
            $('#itemFormModal').modal('hide');
        },
        deactivateItem(item){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Do you want to "+(item.status == 'Active' ? "deactivate" : "reactivate")+" this item?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes, "+(item.status == 'Active' ? 'deactivate' : 'reactivate')+" it!",
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/inventory/items/' + item.id)
                    .then(response => {
                        this.$emit('itemsReload', response);
                        this.$swal.fire('Deleted!', 'Item has been reactivated/deactivated.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        editItem(item){
            this.loading = true;
            this.editMode = true;
            this.item = item;
            //Fire.$emit('ItemDataFill', item);
            $('#itemFormModal').modal('show');
            this.loading = false;  
        },
        refreshPage(response){
            //this.items = response.data.items;
            this.closeModals();
            this.$emit('itemsReload', response);
            this.closeModals();
        },
    },
    props:{
        items: Array,
    },
    watch:{

    }
}
</script>