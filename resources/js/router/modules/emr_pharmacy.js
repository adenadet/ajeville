const EMRPharmacyDashboard                     = () => import('../../emr/pharmacy/Dashboard.vue');
const EMRPharmacyDrugForms                     = () => import('../../emr/pharmacy/DrugForms.vue');
const EMRPharmacyDrugItems                     = () => import('../../emr/pharmacy/DrugItems.vue');
const EMRPharmacyDrugs                         = () => import('../../emr/pharmacy/Drugs.vue');
const EMRPharmacyInsurance                     = () => import('../../emr/pharmacy/Insurance.vue');
const EMRPharmacyPrescription                  = () => import('../../emr/pharmacy/Prescription.vue');
const EMRPharmacyPrescriptions                 = () => import('../../emr/pharmacy/Prescriptions.vue');
const EMRPharmacyPointOfSale                   = () => import('../../emr/pharmacy/PointOfSale.vue');
//const EMRPharmacyReferredOut                   = () => import('../../emr/pharmacy/ReferredOut.vue');
//const EMRPharmacyRequest                       = () => import('../../emr/pharmacy/Request.vue');

    /*const EMRPharmacyDetailReferralList            = () => import('../../emr/pharmacy/details/ReferralList.vue');
    const EMRPharmacyDetailRequest                 = () => import('../../emr/pharmacy/details/Request.vue');
    const EMRPharmacyDetailRequestList             = () => import('../../emr/pharmacy/details/RequestList.vue');

    const EMRPharmacyFormAction                    = () => import('../../emr/pharmacy/forms/Action.vue');
    const EMRPharmacyFormReferral                  = () => import('../../emr/pharmacy/forms/Referral.vue');
    const EMRPharmacyFormRequest                   = () => import('../../emr/pharmacy/forms/Request.vue');*/

export default[
    {path: '/emr/pharmacy',                                    component: EMRPharmacyDashboard},
    {path: '/emr/pharmacy/dashboard',                          component: EMRPharmacyDashboard},
    {path: '/emr/pharmacy/insurance',                          component: EMRPharmacyInsurance},
    {path: '/emr/pharmacy/point_of_sale',                      component: EMRPharmacyPointOfSale},
    {path: '/emr/pharmacy/prescriptions',                      component: EMRPharmacyPrescriptions},
    {path: '/emr/pharmacy/prescriptions/:id',                  component: EMRPharmacyPrescription},
    {path: '/emr/pharmacy/settings/drugs',                     component: EMRPharmacyDrugs},
    {path: '/emr/pharmacy/settings/drug_forms',                component: EMRPharmacyDrugForms},
    {path: '/emr/pharmacy/settings/drug_items',                component: EMRPharmacyDrugItems},
    //{path: '/emr/pharmacy/referred_out',                       component: EMRPharmacyReferredOut},
    //{path: '/emr/pharmacy/requests/:id',                       component: EMRPharmacyRequest},
];