const EMRConsultantConsultation            = () => import('../../emr/consultant/Consultation.vue');
const EMRConsultantDashboard               = () => import('../../emr/consultant/Dashboard.vue');
const EMRConsultantQueue                   = () => import('../../emr/consultant/Queue.vue');
const EMRConsultantQueueDepartment         = () => import('../../emr/consultant/Queue.vue');
const EMRConsultantQueueMy                 = () => import('../../emr/consultant/Queue.vue');
const EMRConsultantQueueDoctor             = () => import('../../emr/consultant/QueueDoctor.vue');
const EMRConsultantMyPastConsultations     = () => import('../../emr/consultant/MyPastConsultations.vue');

    const EMRConsultantDetailConsultation    = () => import('../../emr/consultant/details/Consultation.vue');
    const EMRConsultantDetailQueue           = () => import('../../emr/consultant/details/Queue.vue');
    const EMRConsultantDetailQueueList       = () => import('../../emr/consultant/details/QueueList.vue');
    const EMRConsultantDetailReview          = () => import('../../emr/consultant/details/Review.vue');  
    const EMRConsultantDetailSummary         = () => import('../../emr/consultant/details/Summary.vue');
    const EMRConsultantDetailResultQueue     = () => import('../../emr/consultant/details/ResultQueue.vue');

    const EMRConsultantFormConsult             = () => import('../../emr/consultant/forms/Consult.vue');
    const EMRConsultantFormConsultant          = () => import('../../emr/consultant/forms/Consultant.vue');
    const EMRConsultantFormConsultation        = () => import('../../emr/consultant/forms/Consultation.vue');
    const EMRConsultantFormHistory             = () => import('../../emr/consultant/forms/History.vue');
    const EMRConsultantFormLaboratory          = () => import('../../emr/consultant/forms/Laboratory.vue');
    const EMRConsultantFormPrescription        = () => import('../../emr/consultant/forms/Prescription.vue');
    const EMRConsultantFormRadiology           = () => import('../../emr/consultant/forms/Radiology.vue');
    const EMRConsultantFormSoapNote            = () => import('../../emr/consultant/forms/SoapNote.vue');


export default[
    {path: '/emr/consultations',                                component: EMRConsultantDashboard},
    {path: '/emr/consultations/dashboard',                      component: EMRConsultantDashboard},
    {path: '/emr/consultations/detailed/:id',                   component: EMRConsultantDetailConsultation},
    {path: '/emr/consultations/department_queue/:id',           component: EMRConsultantQueueDepartment},
    {path: '/emr/consultations/doctor_queue',                   component: EMRConsultantQueueDoctor},
    {path: '/emr/consultations/my_previous_consultations',      component: EMRConsultantMyPastConsultations},
    {path: '/emr/consultations/my_queue',                       component: EMRConsultantQueueMy},
    {path: '/emr/consultations/start/:id',                      component: EMRConsultantConsultation},
];