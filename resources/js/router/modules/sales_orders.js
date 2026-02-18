const SalesCustomer                = () => import('../../sales_orders/Customer.vue');
const SalesCustomers               = () => import('../../sales_orders/Customers.vue');
const SalesDashboard               = () => import('../../sales_orders/Dashboard.vue');
const SalesDeliveryNote            = () => import('../../sales_orders/DeliveryNote.vue');
const SalesDeliveryNotes           = () => import('../../sales_orders/DeliveryNotes.vue');
const SalesInvoice                 = () => import('../../sales_orders/Quotation.vue');
const SalesInvoices                = () => import('../../sales_orders/Invoices.vue');
const SalesOrder                   = () => import('../../sales_orders/Order.vue');
const SalesOrderFulfill            = () => import('../../sales_orders/OrderFulfill.vue');
const SalesOrders                  = () => import('../../sales_orders/Orders.vue');
const SalesReports                 = () => import('../../sales_orders/Reports.vue');
const SalesReturn                  = () => import('../../sales_orders/Return.vue');
const SalesReturns                 = () => import('../../sales_orders/Returns.vue');
const SalesQuotation               = () => import('../../sales_orders/Quotation.vue');
const SalesQuotations              = () => import('../../sales_orders/Quotations.vue');

    const SalesDetailDeliveryNote                      = () => import('../../sales_orders/details/DeliveryNote.vue');
    const SalesDetailDeliveryNoteList                  = () => import('../../sales_orders/details/DeliveryNoteList.vue');
    const SalesDetailOrder                             = () => import('../../sales_orders/details/Order.vue');
    const SalesDetailOrderList                         = () => import('../../sales_orders/details/OrderList.vue');
    const SalesDetailOrderItemList                     = () => import('../../sales_orders/details/OrderItemList.vue');
    const SalesDetailOrderSummary                      = () => import('../../sales_orders/details/OrderSummary.vue');
    const SalesDetailReceipt                           = () => import('../../sales_orders/details/Receipt.vue');
    const SalesDetailReturn                            = () => import('../../sales_orders/details/Return.vue');
    const SalesDetailReturnList                        = () => import('../../sales_orders/details/ReturnList.vue');
    const SalesDetailQuotation                         = () => import('../../sales_orders/details/Quotation.vue');
    const SalesDetailQuotationList                     = () => import('../../sales_orders/details/QuotationList.vue');

    const SalesDetailReportDailySales                  = () => import('../../sales_orders/details/ReportDailySales.vue');
    const SalesDetailReportSalesItemDetailed           = () => import('../../sales_orders/details/ReportSalesItemDetailed.vue');

    const SalesFormDeliveryNote                        = () => import('../../sales_orders/forms/DeliveryNote.vue');
    const SalesFormFulfill                             = () => import('../../sales_orders/forms/Fulfill.vue');
    const SalesFormFulfillOrderItem                    = () => import('../../sales_orders/forms/FulfillOrderItem.vue');
    const SalesFormOrder                               = () => import('../../sales_orders/forms/Order.vue');
    const SalesFormQuotation                           = () => import('../../sales_orders/forms/Quotation.vue');
    const SalesFormReturn                              = () => import('../../sales_orders/forms/Return.vue');
    //const SalesFormSales                               = () => import('../../sales_orders/forms/Sales.vue');


export default[
    //Sales Orders
    {path: '/sales_orders',                                  component: SalesDashboard},
    {path: '/sales_orders/customers',                        component: SalesCustomers},
    {path: '/sales_orders/customers/:id',                    component: SalesCustomer},
    {path: '/sales_orders/dashboard',                        component: SalesDashboard},
    {path: '/sales_orders/delivery_notes',                   component: SalesDeliveryNotes},
    {path: '/sales_orders/delivery_notes/:id',               component: SalesDeliveryNote},
    {path: '/sales_orders/invoices',                         component: SalesInvoices},
    {path: '/sales_orders/invoices/:id',                     component: SalesInvoice},
    {path: '/sales_orders/orders',                           component: SalesOrders},
    //{path: '/sales_orders/orders/new',                       component: SalesFormSales},
    {path: '/sales_orders/orders/fulfill/:id',               component: SalesOrderFulfill},
    {path: '/sales_orders/orders/:id',                       component: SalesOrder},
    {path: '/sales_orders/reports',                          component: SalesReports},
    {path: '/sales_orders/returns',                          component: SalesReturns},
    {path: '/sales_orders/returns/:id',                      component: SalesReturn},
    {path: '/sales_orders/quotations',                       component: SalesQuotations},
    {path: '/sales_orders/quotations/new',                   component: SalesFormQuotation},
    {path: '/sales_orders/quotations/:id',                   component: SalesQuotation},
    
];