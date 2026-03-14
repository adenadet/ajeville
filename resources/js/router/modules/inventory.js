const InventoryBrands              = () => import('../../inventory/Brands.vue');
const InventoryCategories          = () => import('../../inventory/Categories.vue');
const InventoryCategory            = () => import('../../inventory/Category.vue');
const InventoryClassification      = () => import('../../inventory/Classification.vue');
const InventoryClassifications     = () => import('../../inventory/Classifications.vue');
const InventoryDashboard           = () => import('../../inventory/Dashboard.vue');
const InventoryDirectPurchase      = () => import('../../inventory/DirectPurchase.vue');
const InventoryDirectPurchases     = () => import('../../inventory/DirectPurchases.vue');
const InventoryItem                = () => import('../../inventory/Item.vue');
const InventoryItems               = () => import('../../inventory/Items.vue');
const InventoryItemsBulk           = () => import('../../inventory/ItemsBulk.vue');
const InventoryItemTypes           = () => import('../../inventory/ItemTypes.vue');
const InventoryPackage             = () => import('../../inventory/Package.vue');
const InventoryPackages            = () => import('../../inventory/Packages.vue');
const InventoryStore               = () => import('../../inventory/Store.vue');
const InventoryStores              = () => import('../../inventory/Stores.vue');
const InventoryTransferOrder       = () => import('../../inventory/TransferOrder.vue');
const InventoryTransferOrdersIn    = () => import('../../inventory/TransferOrdersIn.vue');
const InventoryTransferOrdersOut   = () => import('../../inventory/TransferOrdersOut.vue');
const InventoryUserStore           = () => import('../../inventory/UserStore.vue');

    const InventoryDetailBrand              = () => import('../../inventory/details/Brand.vue');    
    const InventoryDetailItem                  = () => import('../../inventory/details/Item.vue'); 
    const InventoryDetailItemList              = () => import('../../inventory/details/ItemList.vue'); 
    const InventoryDetailStoreExpired          = () => import('../../inventory/details/StoreExpired.vue'); 
    const InventoryDetailStoreItem             = () => import('../../inventory/details/StoreItem.vue'); 
    const InventoryDetailStoreItemList         = () => import('../../inventory/details/StoreItemList.vue'); 
    const InventoryDetailStoreSoonToExpire     = () => import('../../inventory/details/StoreSoonToExpire.vue');
    const InventoryDetailStoreSummary          = () => import('../../inventory/details/StoreSummary.vue');
    const InventoryDetailTransferOrder         = () => import('../../inventory/details/TransferOrder.vue'); 
    const InventoryDetailTransferOrderList     = () => import('../../inventory/details/TransferOrderList.vue'); 
    
    const InventoryFormBrand                   = () => import('../../inventory/forms/Brand.vue');
    const InventoryFormCategory                = () => import('../../inventory/forms/Category.vue');
    const InventoryFormClassification          = () => import('../../inventory/forms/Classification.vue');
    const InventoryFormFulfill                 = () => import('../../inventory/forms/Fulfill.vue');
    const InventoryFormFulfillment             = () => import('../../inventory/forms/Fulfillment.vue');
    const InventoryFormItem                    = () => import('../../inventory/forms/Item.vue');
    const InventoryFormItemBulk                = () => import('../../inventory/forms/ItemBulk.vue');
    const InventoryFormItemGetter              = () => import('../../emr/front_office/forms/ServiceGetter.vue');
    const InventoryFormItemImport              = () => import('../../inventory/forms/ItemImport.vue');
    const InventoryFormItemSearch              = () => import('../../inventory/forms/ItemSearch.vue');
    const InventoryFormItemType                = () => import('../../inventory/forms/ItemType.vue');
    const InventoryFormSalesOrder              = () => import('../../inventory/forms/SalesOrder.vue');
    const InventoryFormStore                   = () => import('../../inventory/forms/Store.vue');
    const InventoryFormStoreIssue              = () => import('../../inventory/forms/StoreIssue.vue');
    const InventoryFormStoreItemSetting        = () => import('../../inventory/forms/StoreItemSetting.vue');
    const InventoryFormTransferOrder           = () => import('../../inventory/forms/TransferOrder.vue');
    const InventoryFormTransferOrderReject     = () => import('../../inventory/forms/TransferOrderReject.vue');

export default[
    {path: '/inventory',                                    component: InventoryDashboard},
    {path: '/inventory/dashboard',                          component: InventoryDashboard},
    {path: '/inventory/direct_purchases',                   component: InventoryDirectPurchases},
    {path: '/inventory/direct_purchases/:id',               component: InventoryDirectPurchase},
    {path: '/inventory/items',                              component: InventoryItems},
    {path: '/inventory/items/:id',                          component: InventoryItem},
    {path: '/inventory/items_bulk',                         component: InventoryItemsBulk},
    {path: '/inventory/packages',                           component: InventoryPackages},
    {path: '/inventory/packages/:id',                       component: InventoryPackage},
    {path: '/inventory/stores',                             component: InventoryStores},
    {path: '/inventory/stores/:id',                         component: InventoryStore},
    {path: '/inventory/transfer_orders/in',                 component: InventoryTransferOrdersIn},
    {path: '/inventory/transfer_orders/out',                component: InventoryTransferOrdersOut},
    {path: '/inventory/transfer_orders/:id',                component: InventoryTransferOrder},
    {path: '/inventory/user_stores/:id',                    component: InventoryUserStore},

    {path: '/inventory/settings/brands',                    component: InventoryBrands},
    {path: '/inventory/settings/categories',                component: InventoryCategories},
    {path: '/inventory/settings/categories/:id',            component: InventoryCategory},
    {path: '/inventory/settings/classifications',           component: InventoryClassifications},
    {path: '/inventory/settings/classifications/:id',       component: InventoryClassification},
    {path: '/inventory/settings/item_types',                component: InventoryItemTypes},
]