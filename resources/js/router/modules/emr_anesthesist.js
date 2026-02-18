const EMRAnesthesistCase                   = () => import('../../emr/anesthesia/Case.vue');
const EMRAnesthesistCases                  = () => import('../../emr/anesthesia/Cases.vue');
const EMRAnesthesistDashboard              = () => import('../../emr/anesthesia/Dashboard.vue');    

    const EMRAnesthesistDetailCaseList         = () => import('../../emr/anesthesia/details/CaseList.vue');

export default [
    {path: '/emr/anesthesist',                                  component: EMRAnesthesistDashboard},
    {path: '/emr/anesthesist/cases',                            component: EMRAnesthesistCases},
    {path: '/emr/anesthesist/cases/:id',                        component: EMRAnesthesistCase},
];