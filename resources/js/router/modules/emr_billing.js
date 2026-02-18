const EMRFinanceCashRegister                   = () => import('../../emr/finance/CashRegister.vue');
const EMRFinanceDashboard                      = () => import('../../emr/finance/Dashboard.vue');
const EMRFinancePatient                        = () => import('../../emr/finance/Patient.vue');
const EMRFinancePayments                       = () => import('../../emr/finance/Payments.vue');
const EMRFinanceReport                         = () => import('../../emr/finance/Report.vue');

const EMRFinanceTransaction                    = () => import('../../emr/finance/Transaction.vue');             
const EMRFinanceTransactions                   = () => import('../../emr/finance/Transactions.vue');

export default [
    {path: '/emr/billings',                                     component: EMRFinanceDashboard},
    {path: '/emr/billings/patient',                             component: EMRFinancePatient},
    {path: '/emr/billings/transactions',                        component: EMRFinanceDashboard}, 
]