export default[
    {path: '/emr/front_office',                                 component: EMRFrontOfficeDashboard},
    {path: '/emr/front_office/dashboard',                       component: EMRFrontOfficeDashboard},
    {path: '/emr/front_office/patients',                        component: EMRPatientAll},
    {path: '/emr/front_office/patients/new',                    component: EMRPatientFormRegistration},
    {path: '/emr/front_office/patients/search',                 component: EMRPatientSearch},
    {path: '/emr/front_office/patients/:id',                    component: EMRPatientSingle},
];