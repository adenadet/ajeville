export default[
    {path: '/customer_relations',                               component: CRMDashboard},
    {path: '/customer_relations/customers',                     component: CRMCustomers},
    {path: '/customer_relations/customers/:id',                 component: CRMCustomer},
    {path: '/customer_relations/dashboard',                     component: CRMDashboard},
    {path: '/customer_relations/leads',                         component: CRMLeads},
    {path: '/customer_relations/leads/:id',                     component: CRMLead},
];