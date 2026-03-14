const ApprovalBackups                      = () => import('../../approvals/Backups.vue');
const ApprovalBatches                      = () => import('../../approvals/Batches.vue');
const ApprovalDashboard                    = () => import('../../approvals/Dashboard.vue');
const ApprovalDocument                     = () => import('../../approvals/Document.vue');
const ApprovalDocuments                    = () => import('../../approvals/Documents.vue');
const ApprovalExpenses                     = () => import('../../approvals/Expenses.vue');
const ApprovalInvoices                     = () => import('../../approvals/Invoices.vue');
const ApprovalJobCompletions               = () => import('../../approvals/JobCompletions.vue');
const ApprovalOrderReturn                  = () => import('../../approvals/OrderReturn.vue');
const ApprovalOrderReturns                 = () => import('../../approvals/OrderReturns.vue');
const ApprovalPurchaseOrders               = () => import('../../approvals/PurchaseOrders.vue');
//const ApprovalPurchaseRequests             = () => import('../../approvals/PurchaseRequests.vue');

const ApprovalSalesOrder                   = () => import('../../approvals/SalesOrder.vue');
const ApprovalSalesOrders                  = () => import('../../approvals/SalesOrders.vue');

const ApprovalWorkOrders                   = () => import('../../approvals/WorkOrders.vue');


export default[
{path: '/approvals',                                        component: ApprovalDashboard},
    {path: '/approvals/batches',                                component: ApprovalBatches},
    {path: '/approvals/dashboard',                              component: ApprovalDashboard},
    //{path: '/approvals/categories',                              component: ApprovalCategories},
    //{path: '/approvals/categories/:id',                          component: ApprovalCategory},
    {path: '/approvals/documents',                              component: ApprovalDocuments},
    {path: '/approvals/expenses',                               component: ApprovalExpenses},
    {path: '/approvals/invoices',                               component: ApprovalInvoices},
    {path: '/approvals/job_completions',                        component: ApprovalJobCompletions},
    {path: '/approvals/returns',                                component: ApprovalOrderReturns},
    {path: '/approvals/returns/:id',                            component: ApprovalOrderReturn},
    {path: '/approvals/purchase_orders',                        component: ApprovalPurchaseOrders},
    //{path: '/approvals/purchase_orders/:id',                     component: ApprovalPurchaseOrder},
    //{path: '/approvals/purchase_requests',                      component: ApprovalPurchaseRequests},
    //{path: '/approvals/purchase_requests/:id',                   component: ApprovalPurchaseRequest},
    {path: '/approvals/sales_orders',                           component: ApprovalSalesOrders},
    {path: '/approvals/sales_orders/:id',                       component: ApprovalSalesOrder},
];