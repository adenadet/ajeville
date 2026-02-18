const EMRRadiologyDashboard                     = () => import('../../emr/radiology/Dashboard.vue');
const EMRRadiologyInsurance                     = () => import('../../emr/radiology/Insurance.vue');
const EMRRadiologyQueue                         = () => import('../../emr/radiology/Queue.vue');
const EMRRadiologyReferredIn                    = () => import('../../emr/radiology/ReferredIn.vue');
const EMRRadiologyReferredOut                   = () => import('../../emr/radiology/ReferredOut.vue');
const EMRRadiologyRequest                       = () => import('../../emr/radiology/Request.vue');

    const EMRRadiologyDetailReferralList            = () => import('../../emr/radiology/details/ReferralList.vue');
    const EMRRadiologyDetailRequest                 = () => import('../../emr/radiology/details/Request.vue');
    const EMRRadiologyDetailRequestList             = () => import('../../emr/radiology/details/RequestList.vue');

    const EMRRadiologyFormAction                    = () => import('../../emr/radiology/forms/Action.vue');
    const EMRRadiologyFormReferral                  = () => import('../../emr/radiology/forms/Referral.vue');
    const EMRRadiologyFormRequest                   = () => import('../../emr/radiology/forms/Request.vue');

export default[
    {path: '/emr/radiology',                                    component: EMRRadiologyDashboard},
    {path: '/emr/radiology/dashboard',                          component: EMRRadiologyDashboard},
    {path: '/emr/radiology/insurance',                          component: EMRRadiologyInsurance},
    {path: '/emr/radiology/queues',                             component: EMRRadiologyQueue},
    {path: '/emr/radiology/referred_in',                        component: EMRRadiologyReferredIn},
    {path: '/emr/radiology/referred_out',                       component: EMRRadiologyReferredOut},
    {path: '/emr/radiology/requests/:id',                       component: EMRRadiologyRequest},
];