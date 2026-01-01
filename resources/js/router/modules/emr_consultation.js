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