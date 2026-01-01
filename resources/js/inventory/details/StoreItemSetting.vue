<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="storeItemSettingsFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Update Store Item Setting</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <InventoryFormStoreItemSetting :editMode="editMode" :store_item_setting.sync="item" @storeItemSettingReload="reloadPage"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header card-primary card-outline">
            <h3 class="card-title">Store Item Settings</h3>
        </div>
        <div class="card-body ">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                    Store Name <span class="float-right">{{ store_item_setting.store != null ? store_item_setting.store.name : 'Invalid Store' }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                    Reorder Level <span class="float-right">{{ store_item_setting.reorder_level != null ? store_item_setting.reorder_level : 'N/A' }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                    Maximum Level <span class="float-right">{{ store_item_setting.maximum_level != null ? store_item_setting.maximum_level : 'N/A' }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Expiry Notification <span class="float-right">{{ store_item_setting.expiry_notification != null ? store_item_setting.expiration_notification : 'N/A'  }}</span></a>
                </li>
            </ul>
            <button class="btn btn-primary col-12 mt-2" type="button" @click="editStoreItemSettings(store_item_setting)">Change Settings</button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            item: {},
            locations: [],
            purchase_orders: [],
            transfer_orders: [],
            store_item_setting: {},
        }
    },
    emits:['reloadStoreItemSettings'],
    methods:{
        closeModals(){
            $('#storeItemSettingFormModal').modal('hide');
        },
        editStoreItemSettings(){
            $('#storeItemSettingFormModal').modal('show');
        },
        reloadPage(){
            this.closeModals();
            this.$emits('reloadStoreItemSettings');
        },
        refreshPage(response){},
    },
    mounted() {
        this.getInitials();
    },
    props:{
        store_item_setting: Object
    },
}
</script>