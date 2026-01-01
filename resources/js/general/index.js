import {createRouter, createWebHistory} from 'vue-router';

import EscrowAdminDisputes                      from '../escrows/AdminDisputes.vue';
import EscrowAdminPartners                      from '../escrows/AdminPartners.vue';
import EscrowAdminPayments                      from '../escrows/AdminPayments.vue';
import EscrowAdminTransactions                  from '../escrows/AdminTransactions.vue';
import EscrowDashboard                          from '../escrows/Dashboard.vue';
import EscrowDispute                            from '../escrows/Dispute.vue';
import EscrowDisputes                           from '../escrows/Disputes.vue';
import EscrowPartner                            from '../escrows/Partner.vue';
import EscrowPartners                           from '../escrows/Partners.vue';
import EscrowPayment                            from '../escrows/Payment.vue';
import EscrowPayments                           from '../escrows/Payments.vue';
import EscrowProduct                            from '../escrows/Product.vue';
import EscrowProducts                           from '../escrows/Products.vue';
import EscrowTransaction                        from '../escrows/Transaction.vue';
import EscrowTransactions                       from '../escrows/Transactions.vue';

    import EscrowDetailDispute                  from '../escrows/details/Dispute.vue';
    import EscrowDetailDisputeList              from '../escrows/details/DisputeList.vue';
    import EscrowDetailPartner                  from '../escrows/details/Partner.vue';
    import EscrowDetailPartnerList              from '../escrows/details/PartnerList.vue';
    import EscrowDetailPayment                  from '../escrows/details/Payment.vue';
    import EscrowDetailPaymentList              from '../escrows/details/PaymentList.vue';
    import EscrowDetailProduct                  from '../escrows/details/Product.vue';
    import EscrowDetailProductList              from '../escrows/details/ProductList.vue';
    import EscrowDetailProductSummary           from '../escrows/details/ProductSummary.vue';
    import EscrowDetailReviewList               from '../escrows/details/ReviewList.vue';
    import EscrowDetailTransaction              from '../escrows/details/Transaction.vue';
    import EscrowDetailTransactionActivity      from '../escrows/details/TransactionActivity.vue';
    import EscrowDetailTransactionContract      from '../escrows/details/TransactionContract.vue';
    import EscrowDetailTransactionList          from '../escrows/details/TransactionList.vue';
    import EscrowDetailTransactionSummary       from '../escrows/details/TransactionSummary.vue';

    import EscrowFormAccept                     from '../escrows/forms/Accept.vue';
    import EscrowFormDispute                    from '../escrows/forms/Dispute.vue';
    import EscrowFormDelivery                   from '../escrows/forms/Delivery.vue';
    import EscrowFormDeliveryAccept             from '../escrows/forms/DeliveryAccept.vue';
    import EscrowFormExternalTransaction        from '../escrows/forms/ExternalTransaction.vue';
    import EscrowFormPartner                    from '../escrows/forms/Partner.vue';
    import EscrowFormPayment                    from '../escrows/forms/Payment.vue';
    import EscrowFormProduct                    from '../escrows/forms/Product.vue';
    import EscrowFormProductTransaction         from '../escrows/forms/ProductTransaction.vue';
    import EscrowFormQuickPayment               from '../escrows/forms/QuickPayment.vue';
    import EscrowFormTransaction                from '../escrows/forms/Transaction.vue';
    import EscrowFormTransactionContract        from '../escrows/forms/TransactionContract.vue';
    import EscrowFormTransactionProduct         from '../escrows/forms/TransactionProduct.vue';
    import EscrowFormTransactionRequest         from '../escrows/forms/TransactionRequest.vue';

import FinanceBranchPriceList       from '../finance/BranchPriceList.vue';
import FinanceCashRegister          from '../finance/CashRegister.vue';
import FinanceDashboard             from '../finance/Dashboard.vue';
import FinanceDeposits              from '../finance/Deposits.vue';
import FinanceInsurance             from '../finance/Insurance.vue';
import FinancePatient               from '../finance/Patient.vue';
import FinanceTransactions          from '../finance/Transactions.vue';
import FinanceVisit                 from '../finance/Visit.vue';

    import FinanceDetailPatientTransactions             from '../finance/details/PatientTransactions.vue';
    import FinanceDetailPatientTransaction              from '../finance/details/PatientTransaction.vue';  

    import FinanceFormDeposit                           from '../finance/forms/Deposit.vue';


import GeneralChartBar              from '../general/charts/Bar.vue';
import GeneralChartDonut            from '../general/charts/Donut.vue';
import GeneralFormTab               from '../general/forms/Tab.vue';
import GeneralFormWizard            from '../general/forms/Wizard.vue';

import NoticeAdmin                  from '../notices/Admin.vue';
import NoticeAll                    from '../notices/All.vue';
import NoticeBoard                  from '../notices/Board.vue';
import NoticeSingle                 from '../notices/Single.vue';
        
    import NoticeDetailList             from '../notices/details/List.vue';    

    import NoticeForm    from '../notices/forms/New.vue';

import PaymentSingle                        from '../payment/Single.vue';
    import PaymentFormQuickPayment          from '../payment/forms/QuickPayment.vue';

import TicketAdmin              from '../ticketing/Admin.vue';

import TicketDepartment         from '../ticketing/Department.vue';
import TicketPersonal           from '../ticketing/Personal.vue';
import TicketSetting            from '../ticketing/Setting.vue';
import TicketSingle             from '../ticketing/Single.vue';

    import TicketDetailDashboard        from '../ticketing/details/Dashboard.vue';
    import TicketDetailList             from '../ticketing/details/List.vue';

    import TicketFormAssign     from '../ticketing/forms/Assign.vue';
    import TicketFormAccept     from '../ticketing/forms/Accept.vue';
    import TicketFormComplete   from '../ticketing/forms/Complete.vue';
    import TicketFormNew        from '../ticketing/forms/New.vue';
    import TicketFormReply      from '../ticketing/forms/Reply.vue';

import UserBirthday         from '../users/Birthday.vue';
import UserBirthdays        from '../users/Birthdays.vue';
import UserContacts         from '../users/Contacts.vue';
import UserContact          from '../users/Contact.vue';
import UserProfile          from '../users/Profile.vue';
import UserRole             from '../users/Role.vue';
import UserRoles            from '../users/Roles.vue';
import UserStaffs           from '../users/Staffs.vue';
import UserStaffsLatest     from '../users/StaffsLatest.vue';

    import UserDetailAccount        from '../users/details/Account.vue';    
    import UserDetailBioData        from '../users/details/BioData.vue';
    import UserDetailCompany        from '../users/details/Company.vue';
    import UserDetailNOK            from '../users/details/NOK.vue';


    import UserFormCompanyKYC       from '../users/forms/CompanyKYC.vue';
    import UserFormAccount          from '../users/forms/Account.vue';
    import UserFormAssignRole       from '../users/forms/AssignRole.vue';
    import UserFormBioData          from '../users/forms/BioData.vue';
    import UserFormKYC              from '../users/forms/KYC.vue';
    import UserFormNOK              from '../users/forms/NextOfKin.vue';
    import UserFormPassword         from '../users/forms/Password.vue';
    import UserFormRole             from '../users/forms/Role.vue';
    import UserFormStaff            from '../users/forms/Staff.vue';    

import Error404 from '../errors/404.vue';

const routes = [
    {path: '/',                                                 component: EscrowDashboard},
    
    {path: '/dashboard',                                        component: EscrowDashboard},
    /*{path: '/emr/front_office/patients',                        component: EMRPatientAll},*/
    {path: '/escrows',                                          component: EscrowDashboard},

    {path: '/escrows/admin/disputes',                           component: EscrowAdminDisputes},
    {path: '/escrows/admin/partners',                           component: EscrowDashboard},
    {path: '/escrows/admin/payments',                           component: EscrowAdminPayments},
    {path: '/escrows/admin/tickets',                            component: TicketPersonal},
    {path: '/escrows/admin/transactions',                       component: EscrowAdminTransactions},
    {path: '/escrows/dashboard',                                component: EscrowDashboard},
    {path: '/escrows/disputes',                                 component: EscrowDisputes},
    {path: '/escrows/disputes/:id',                             component: EscrowDispute},
    {path: '/escrows/partners',                                 component: EscrowPartners},
    {path: '/escrows/partners/:id',                             component: EscrowPartner},
    {path: '/escrows/payments',                                 component: EscrowPayments},
    {path: '/escrows/payments/:id',                             component: EscrowPayment},
    {path: '/escrows/products',                                 component: EscrowProducts},
    {path: '/escrows/products/:id/:owner',                      component: EscrowProduct},
    {path: '/escrows/transactions',                             component: EscrowTransactions},
    {path: '/escrows/transactions/:id',                         component: EscrowTransaction},
    
    {path: '/finance',                                          component: FinanceDashboard},
    {path: '/finance/dashboard',                                component: FinanceDashboard},
    {path: '/finance/cash_register',                            component: FinanceCashRegister},
    {path: '/finance/deposits',                                 component: FinanceDeposits},
    {path: '/finance/transactions',                             component: FinanceTransactions},
    {path: '/finance/visit/:id',                                component: FinanceVisit},

    {path: '/home',                                             component: EscrowDashboard},

    //{path: '/pay',                                              component: PaymentFormPayment},
    {path: '/payments/:vendor_id',                              component: PaymentSingle},
    {path: '/purchase/:product_id',                             component: EscrowFormProductTransaction},
    
    {path:'/profile',                                           component: UserProfile},

    //Notices
    {path: '/notices',                                          component: NoticeAll},
    {path: '/notices/admin',                                    component: NoticeAdmin},
    {path: '/notices/:id',                                      component: NoticeSingle},

    //Ticketing
    {path: '/ticketing',                                        component: TicketPersonal},
    {path: '/ticketing/admin',                                  component: TicketAdmin},
    {path: '/ticketing/department',                             component: TicketDepartment},
    {path: '/ticketing/settings',                               component: TicketSetting},
    {path: '/ticketing/:id',                                    component: TicketSingle},
    
    {path: '/users',                                            component: UserStaffs},
    {path: '/:pathMatch(.*)*',                                  component: Error404},
];
const router = createRouter({
    history: createWebHistory(),
    routes
});

export function registerGlobalComponents(app) {
    app.component('EscrowAdminDisputes',                                EscrowAdminDisputes);
    app.component('EscrowAdminPartners',                                EscrowAdminPartners);
    app.component('EscrowAdminTransactions',                            EscrowAdminTransactions);
    app.component('EscrowDashboard',                                    EscrowDashboard);
    app.component('EscrowDispute',                                      EscrowDispute);
    app.component('EscrowDisputes',                                     EscrowDisputes);
    app.component('EscrowPayment',                                      EscrowPayment);
    app.component('EscrowPayments',                                     EscrowPayments);
    app.component('EscrowProduct',                                      EscrowProduct);
    app.component('EscrowProducts',                                     EscrowProducts);
    app.component('EscrowTransaction',                                  EscrowTransaction);
    app.component('EscrowTransactions',                                 EscrowTransactions);

        app.component('EscrowDetailDispute',                            EscrowDetailDispute);
        app.component('EscrowDetailDisputeList',                        EscrowDetailDisputeList);
        app.component('EscrowDetailPartner',                            EscrowDetailPartner);
        app.component('EscrowDetailPartnerList',                        EscrowDetailPartnerList);
        app.component('EscrowDetailPayment',                            EscrowDetailPayment);
        app.component('EscrowDetailPaymentList',                        EscrowDetailPaymentList);
        app.component('EscrowDetailProduct',                            EscrowDetailProduct);
        app.component('EscrowDetailProductList',                        EscrowDetailProductList);
        app.component('EscrowDetailProductSummary',                     EscrowDetailProductSummary);
        app.component('EscrowDetailReviewList',                         EscrowDetailReviewList);
        app.component('EscrowDetailTransaction',                        EscrowDetailTransaction);
        app.component('EscrowDetailTransactionActivity',                EscrowDetailTransactionActivity);
        app.component('EscrowDetailTransactionContract',                EscrowDetailTransactionContract);
        app.component('EscrowDetailTransactionList',                    EscrowDetailTransactionList);
        app.component('EscrowDetailTransactionSummary',                 EscrowDetailTransactionSummary);

        app.component('EscrowFormAccept',                               EscrowFormAccept);
        app.component('EscrowFormDelivery',                             EscrowFormDelivery);
        app.component('EscrowFormDeliveryAccept',                       EscrowFormDeliveryAccept);
        app.component('EscrowFormDispute',                              EscrowFormDispute);
        app.component('EscrowFormExternalTransaction',                  EscrowFormExternalTransaction);
        app.component('EscrowFormPartner',                              EscrowFormPartner);
        app.component('EscrowFormPayment',                              EscrowFormPayment);
        app.component('EscrowFormProduct',                              EscrowFormProduct);
        app.component('EscrowFormProductTransaction',                   EscrowFormProductTransaction);
        app.component('EscrowFormQuickPayment',                         EscrowFormQuickPayment);
        app.component('EscrowFormTransaction',                          EscrowFormTransaction);
        app.component('EscrowFormTransactionContract',                  EscrowFormTransactionContract);
        app.component('EscrowFormTransactionProduct',                   EscrowFormTransactionProduct); 
        app.component('EscrowFormTransactionRequest',                   EscrowFormTransactionRequest);

    app.component('GeneralChartBar',            GeneralChartBar);
    app.component('GeneralChartDonut',          GeneralChartDonut);
    app.component('GeneralFormTab',             GeneralFormTab);
    app.component('GeneralFormWizard',          GeneralFormWizard);
    
    app.component('NoticeAll',                  NoticeAll);
    app.component('NoticeAdmin',                NoticeAdmin);
    app.component('NoticeBoard',                NoticeBoard);
    app.component('NoticeSingle',               NoticeSingle);

        app.component('NoticeDetailList',           NoticeDetailList);
        
        app.component('NoticeForm',                 NoticeForm);  
        
    app.component('PaymentSingle',              PaymentSingle)
        app.component('PaymentFormQuickPayment',            PaymentFormQuickPayment);
        
    app.component('TicketAdmin',            TicketAdmin);
    app.component('TicketDepartment',       TicketDepartment);
    app.component('TicketPersonal',         TicketPersonal);
    app.component('TicketSingle',           TicketSingle);

        app.component('TicketDetailDashboard',      TicketDetailDashboard);
        app.component('TicketDetailList',           TicketDetailList);

        app.component('TicketFormAccept',   TicketFormAccept);
        app.component('TicketFormAssign',   TicketFormAssign);
        app.component('TicketFormComplete', TicketFormComplete);
        app.component('TicketFormNew',      TicketFormNew);
        app.component('TicketFormReply',    TicketFormReply);
    
    app.component('UserBirthday',            UserBirthday);
    app.component('UserBirthdays',           UserBirthdays);
    app.component('UserContact',             UserContact);
    app.component('UserContacts',            UserContacts);
    app.component('UserProfile',             UserProfile);
    app.component('UserRole',                UserRole);
    app.component('UserRoles',               UserRoles);
    app.component('UserStaffs',              UserStaffs);
    app.component('UserStaffsLatest',        UserStaffsLatest);

        app.component('UserDetailAccount',          UserDetailAccount);
        app.component('UserDetailBioData',          UserDetailBioData);
        app.component('UserDetailCompany',          UserDetailCompany);
        app.component('UserDetailNOK',              UserDetailNOK);

        app.component('UserFormAccount',            UserFormAccount);
        app.component('UserFormCompanyKYC',         UserFormCompanyKYC);
        app.component('UserFormAssignRole',         UserFormAssignRole);
        app.component('UserFormBioData',            UserFormBioData);
        app.component('UserFormKYC',                UserFormKYC);
        app.component('UserFormNOK',                UserFormNOK);
        app.component('UserFormPassword',           UserFormPassword);
        app.component('UserFormRole',               UserFormRole);
        app.component('UserFormStaff',              UserFormStaff);
}

export default router;