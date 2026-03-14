const EMRLaboratoryAnalytes                     = () => import('../../emr/laboratory/Analytes.vue');
const EMRLaboratoryAnalyte                      = () => import('../../emr/laboratory/Analyte.vue');
const EMRLaboratoryBottles                      = () => import('../../emr/laboratory/Bottles.vue');
const EMRLaboratoryDashboard                    = () => import('../../emr/laboratory/Dashboard.vue');
const EMRLaboratoryInsurance                    = () => import('../../emr/laboratory/Insurance.vue');
const EMRLaboratoryLabelPrint                   = () => import('../../emr/laboratory/LabelPrint.vue');
const EMRLaboratoryPOS                          = () => import('../../emr/laboratory/POS.vue');
const EMRLaboratoryPanelInvestigations          = () => import('../../emr/laboratory/PanelInvestigations.vue');
const EMRLaboratoryQueue                        = () => import('../../emr/laboratory/Queue.vue');
const EMRLaboratoryReferredIn                   = () => import('../../emr/laboratory/ReferredIn.vue');
const EMRLaboratoryReferredOut                  = () => import('../../emr/laboratory/ReferredOut.vue');
const EMRLaboratoryReports                      = () => import('../../emr/laboratory/Reports.vue');
//const EMRLaboratoryRequests                     = () => import('../../emr/laboratory/Requests.vue');
const EMRLaboratoryRequest                      = () => import('../../emr/laboratory/Request.vue');
const EMRLaboratoryResultTemplates              = () => import('../../emr/laboratory/ResultTemplates.vue');
const EMRLaboratoryServices                     = () => import('../../emr/laboratory/Services.vue');
const EMRLaboratoryService                      = () => import('../../emr/laboratory/Service.vue');
const EMRLaboratorySpecimens                    = () => import('../../emr/laboratory/Specimens.vue');
const EMRLaboratorySpecimenTypes                = () => import('../../emr/laboratory/SpecimenTypes.vue');

    const EMRLaboratoryDetailReferenceRangeList     = () => import('../../emr/laboratory/details/ReferenceRangeList.vue');
    const EMRLaboratoryDetailRequest                = () => import('../../emr/laboratory/details/Request.vue');
    const EMRLaboratoryDetailRequestList            = () => import('../../emr/laboratory/details/RequestList.vue');
    const EMRLaboratoryDetailResultTemplatePreview  = () => import('../../emr/laboratory/details/ResultTemplatePreview.vue');
    const EMRLaboratoryDetailService                = () => import('../../emr/laboratory/details/Service.vue');
    
    const EMRLaboratoryFormAnalyte                  = () => import('../../emr/laboratory/forms/Analyte.vue');
    const EMRLaboratoryFormBottle                   = () => import('../../emr/laboratory/forms/Bottle.vue');
    const EMRLaboratoryFormCollect                  = () => import('../../emr/laboratory/forms/Collect.vue');
    const EMRLaboratoryFormReferenceRange           = () => import('../../emr/laboratory/forms/ReferenceRange.vue');
    const EMRLaboratoryFormResultTemplate           = () => import('../../emr/laboratory/forms/ResultTemplate.vue');
    const EMRLaboratoryFormService                  = () => import('../../emr/laboratory/forms/Service.vue');
    const EMRLaboratoryFormSpecimenType             = () => import('../../emr/laboratory/forms/SpecimenType.vue');


export default[
    {path: '/emr/laboratory',                                   component: EMRLaboratoryDashboard},
    {path: '/emr/laboratory/dashboard',                         component: EMRLaboratoryDashboard},
    {path: '/emr/laboratory/insurance',                         component: EMRLaboratoryInsurance},
    {path: '/emr/laboratory/point_of_sale',                     component: EMRLaboratoryPOS},
    {path: '/emr/laboratory/queues',                            component: EMRLaboratoryQueue},
    {path: '/emr/laboratory/referred_in',                       component: EMRLaboratoryReferredIn},
    {path: '/emr/laboratory/referred_out',                      component: EMRLaboratoryReferredOut},
    //{path: '/emr/laboratory/requests',                          component: EMRLaboratoryRequests},
    {path: '/emr/laboratory/requests/:id',                      component: EMRLaboratoryRequest},
    {path: '/emr/laboratory/requests/:id/print',                component: EMRLaboratoryLabelPrint},

    {path: '/emr/laboratory/specimens',                         component: EMRLaboratorySpecimens},
    {path: '/emr/laboratory/settings/analytes',                 component: EMRLaboratoryAnalytes},
    {path: '/emr/laboratory/settings/analytes/:id',             component: EMRLaboratoryAnalyte},
    {path: '/emr/laboratory/settings/bottles',                  component: EMRLaboratoryBottles},
    {path: '/emr/laboratory/settings/panel_investigations',     component: EMRLaboratoryPanelInvestigations},
    {path: '/emr/laboratory/settings/result_templates',         component: EMRLaboratoryResultTemplates},
    {path: '/emr/laboratory/settings/services',                 component: EMRLaboratoryServices},
    {path: '/emr/laboratory/settings/services/:id',             component: EMRLaboratoryService},
    {path: '/emr/laboratory/settings/specimen_types',           component: EMRLaboratorySpecimenTypes},
    
]