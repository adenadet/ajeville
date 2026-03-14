const FinanceAsset                  = () => import('../../finance/Asset.vue');
const FinanceAssets                 = () => import('../../finance/Assets.vue');
const FinanceBranchAccounts         = () => import('../../finance/BranchAccounts.vue');
const FinanceBranchPriceList        = () => import('../../finance/BranchPriceList.vue');
const FinanceBranchPriceLists       = () => import('../../finance/BranchPriceLists.vue');
const FinanceCashRegister           = () => import('../../finance/CashRegister.vue');
const FinanceChartAccounts          = () => import('../../finance/ChartAccounts.vue');
const FinanceCustomers              = () => import('../../finance/Customers.vue');
const FinanceDashboard              = () => import('../../finance/Dashboard.vue');
const FinanceDeposits               = () => import('../../finance/Deposits.vue');
const FinanceExpense                = () => import('../../finance/Expense.vue');
const FinanceExpenses               = () => import('../../finance/Expenses.vue');
const FinanceExpenseTypes           = () => import('../../finance/ExpenseTypes.vue');
const FinanceInsurance              = () => import('../../finance/Insurance.vue');
const FinanceIncome                 = () => import('../../finance/Income.vue');
const FinanceIncomes                = () => import('../../finance/Incomes.vue');
const FinanceInvoice                = () => import('../../finance/Invoice.vue');
const FinanceInvoices               = () => import('../../finance/Invoices.vue');
const FinancePatient                = () => import('../../finance/Patient.vue');
const FinancePayment                = () => import('../../finance/Payment.vue');
const FinancePayments               = () => import('../../finance/Payments.vue');
const FinancePayOut                 = () => import('../../finance/PayOut.vue');
const FinancePayOuts                = () => import('../../finance/PayOuts.vue');
const FinancePricelist              = () => import('../../finance/Pricelist.vue');
const FinancePricelists             = () => import('../../finance/Pricelists.vue');
const FinancePaymentModes           = () => import('../../finance/PaymentModes.vue');
const FinanceTransaction            = () => import('../../finance/Transaction.vue');
const FinanceTransactions           = () => import('../../finance/Transactions.vue');
const FinanceReports                = () => import('../../finance/Reports.vue');
const FinanceVisit                  = () => import('../../finance/Visit.vue');

    
    const FinanceDetailAssetList                        = () => import('../../finance/details/AssetList.vue');
    const FinanceDetailBranchAccount                    = () => import('../../finance/details/BranchAccount.vue');
    const FinanceDetailBranchAccountList                = () => import('../../finance/details/BranchAccountList.vue');
    const FinanceDetailBranchPricelistList              = () => import('../../finance/details/BranchPricelistList.vue');
    const FinanceDetailExpense                          = () => import('../../finance/details/Expense.vue');
    const FinanceDetailExpenseList                      = () => import('../../finance/details/ExpenseList.vue');
    const FinanceDetailChartAccountList                 = () => import('../../finance/details/ChartAccountList.vue');
    const FinanceDetailCustomerTransaction              = () => import('../../finance/details/CustomerTransaction.vue');
    const FinanceDetailCustomerTransactions             = () => import('../../finance/details/CustomerTransactions.vue');
    const FinanceDetailIncome                           = () => import('../../finance/details/Income.vue');
    const FinanceDetailIncomeList                       = () => import('../../finance/details/IncomeList.vue');
    const FinanceDetailInvoice                          = () => import('../../finance/details/Invoice.vue');
    const FinanceDetailInvoiceList                      = () => import('../../finance/details/InvoiceList.vue');
    const FinanceDetailPayment                          = () => import('../../finance/details/Payment.vue');
    const FinanceDetailPaymentAllocationList            = () => import('../../finance/details/PaymentAllocationList.vue');
    const FinanceDetailPaymentList                      = () => import('../../finance/details/PaymentList.vue');
    const FinanceDetailPayOut                           = () => import('../../finance/details/PayOut.vue');
    const FinanceDetailPayOutAllocationList             = () => import('../../finance/details/PayOutAllocationList.vue');
    const FinanceDetailPayOutList                       = () => import('../../finance/details/PayOutList.vue');
    const FinanceDetailPaymentModeList                  = () => import('../../finance/details/PaymentModeList.vue');
    const FinanceDetailPricelist                        = () => import('../../finance/details/Pricelist.vue');
    const FinanceDetailPricelistList                    = () => import('../../finance/details/PricelistList.vue');
    const FinanceDetailPricelistPlanList                = () => import('../../finance/details/PricelistPlanList.vue');
    const FinanceDetailReportAgingAnalysisReceivables   = () => import('../../finance/details/ReportAgingAnalysisReceivables.vue');
    const FinanceDetailReportBalanceSheet               = () => import('../../finance/details/ReportBalanceSheet.vue');
    const FinanceDetailTransaction                      = () => import('../../finance/details/Transaction.vue');
    const FinanceDetailTransactionList                  = () => import('../../finance/details/TransactionList.vue');  

    const FinanceFormBranchAccount                      = () => import('../../finance/forms/BranchAccount.vue');
    const FinanceFormBranchPricelist                    = () => import('../../finance/forms/BranchPriceList.vue');
    const FinanceFormExpense                            = () => import('../../finance/forms/Expense.vue');
    const FinanceFormExpenseType                        = () => import('../../finance/forms/ExpenseType.vue');
    const FinanceFormDeposit                            = () => import('../../finance/forms/Deposit.vue');
    const FinanceFormIncome                             = () => import('../../finance/forms/Income.vue');
    const FinanceFormInvoice                            = () => import('../../finance/forms/Invoice.vue');
    const FinanceFormPayment                            = () => import('../../finance/forms/Payment.vue');
    const FinanceFormPaymentMode                        = () => import('../../finance/forms/PaymentMode.vue');
    const FinanceFormPayOut                             = () => import('../../finance/forms/PayOut.vue');
    const FinanceFormPricelist                          = () => import('../../finance/forms/Pricelist.vue');
    const FinanceFormPricelistItemBulk                  = () => import('../../finance/forms/PricelistItemBulk.vue');
    const FinanceFormTransaction                        = () => import('../../finance/forms/Transaction.vue');

export default[
    {path: '/finance',                                          component: FinanceDashboard},
    {path: '/finance/assets',                                   component: FinanceAssets},
    {path: '/finance/assets/:id',                               component: FinanceAsset},
    {path: '/finance/dashboard',                                component: FinanceDashboard},
    {path: '/finance/cash_register',                            component: FinanceCashRegister},
    {path: '/finance/deposits',                                 component: FinanceDeposits},
    {path: '/finance/expenses',                                 component: FinanceExpenses},
    {path: '/finance/expenses/:id',                             component: FinanceExpense},
    {path: '/finance/incomes',                                  component: FinanceIncomes},
    {path: '/finance/incomes/:id',                              component: FinanceIncome},
    {path: '/finance/invoices',                                 component: FinanceInvoices},
    {path: '/finance/invoices/:id',                             component: FinanceInvoice},
    {path: '/finance/payments',                                 component: FinancePayments},
    {path: '/finance/pay_outs',                                 component: FinancePayOuts},
    {path: '/finance/pay_outs/:id',                             component: FinancePayOut},
    {path: '/finance/reports',                                  component: FinanceReports},
    {path: '/finance/transactions',                             component: FinanceTransactions},
    {path: '/finance/transactions/:id',                         component: FinanceTransaction},
    {path: '/finance/visit/:id',                                component: FinanceVisit},
    
    {path: '/finance/settings/branch_accounts',                 component: FinanceBranchAccounts}, 
    {path: '/finance/settings/branch_accounts/:id',             component: FinanceDetailBranchAccount},
    {path: '/finance/settings/branch_price_lists',              component: FinanceBranchPriceLists},   
    {path: '/finance/settings/branch_price_lists/:id',          component: FinanceBranchPriceList}, 
    {path: '/finance/settings/expense_types',                   component: FinanceExpenseTypes}, 
    {path: '/finance/settings/payment_modes',                   component: FinancePaymentModes},
    {path: '/finance/settings/price_lists',                     component: FinancePricelists}, 
    {path: '/finance/settings/price_lists/:id',                 component: FinancePricelist}, 
];