const CRMCustomer                          = () => import('../../crm/Customer.vue');
const CRMCustomers                         = () => import('../../crm/Customers.vue');
const CRMDashboard                         = () => import('../../crm/Dashboard.vue');
const CRMLead                              = () => import('../../crm/Lead.vue');
const CRMLeads                             = () => import('../../crm/Leads.vue');

    const CRMDetailContactList                             = () => import('../../crm/details/ContactList.vue');
    const CRMDetailCustomer                                = () => import('../../crm/details/Customer.vue');
    const CRMDetailCustomerList                            = () => import('../../crm/details/CustomerList.vue');
    const CRMDetailCustomerSummary                         = () => import('../../crm/details/CustomerSummary.vue');
    const CRMDetailLead                                    = () => import('../../crm/details/Lead.vue');
    const CRMDetailLeadList                                = () => import('../../crm/details/LeadList.vue');

    const CRMFormContact                                   = () => import('../../crm/forms/Contact.vue');
    const CRMFormCustomer                                  = () => import('../../crm/forms/Customer.vue');
    const CRMFormCustomerUpload                            = () => import('../../crm/forms/CustomerUpload.vue');
    const CRMFormLead                                      = () => import('../../crm/forms/Lead.vue');

export default[
    {path: '/customer_relations',                               component: CRMDashboard},
    {path: '/customer_relations/customers',                     component: CRMCustomers},
    {path: '/customer_relations/customers/:id',                 component: CRMCustomer},
    {path: '/customer_relations/dashboard',                     component: CRMDashboard},
    {path: '/customer_relations/leads',                         component: CRMLeads},
    {path: '/customer_relations/leads/:id',                     component: CRMLead},
];