export default[
    {path: '/approvals',                                        component: () => import ('@/approvals/Dashboard.vue')},
    {path: '/approvals/dashboard',                              component: () => import ('@/approvals/Dashboard.vue')}, //component: ApprovalDashboard},
    {path: '/approvals/purchase_orders',                        component: () => import ('@/approvals/PurchaseOrders.vue')}, //component: ApprovalPurchaseOrders},
    {path: '/approvals/purchase_requests',                      component: () => import ('@/approvals/PurchaseRequests.vue')}, //component: ApprovalPurchaseRequests},
    {path: '/approvals/work_orders',                            component: () => import ('@/approvals/WorkOrders.vue')}, //component: ApprovalWorkOrders},
];