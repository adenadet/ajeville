const EMRFrontOfficeDashboard                  = () => import('../../emr/front_office/Dashboard.vue');
const EMRFrontOfficeAppointment                = () => import('../../emr/front_office/Appointment.vue');
const EMRFrontOfficeAppointments               = () => import('../../emr/front_office/Appointments.vue');
const EMRFrontOfficeVisitBill                  = () => import('../../emr/front_office/VisitBill.vue');
const EMRFrontOfficeVisit                      = () => import('../../emr/front_office/Visit.vue');
const EMRFrontOfficeVisits                     = () => import('../../emr/front_office/Visits.vue');

    const EMRFrontOfficeDetailAppointmentList      = () => import('../../emr/front_office/details/AppointmentList.vue');    
    const EMRFrontOfficeDetailTransactionList      = () => import('../../emr/front_office/details/TransactionList.vue');       
    const EMRFrontOfficeDetailVisit                = () => import('../../emr/front_office/details/Visit.vue'); 
    const EMRFrontOfficeDetailVisitList            = () => import('../../emr/front_office/details/VisitList.vue'); 
    
    const EMRFrontOfficeFormAppointment            = () => import('../../emr/front_office/forms/Appointment.vue');
    const EMRFrontOfficeFormCheckIn                = () => import('../../emr/front_office/forms/CheckIn.vue');
    const EMRFrontOfficeFormServiceGetter          = () => import('../../emr/front_office/forms/ServiceGetter.vue');
    const EMRFrontOfficeFormPatientService         = () => import('../../emr/front_office/forms/PatientService.vue');

const EMRPatientAll                             = () => import('../../emr/patients/All.vue');
const EMRPatientAllergies                       = () => import('../../emr/patients/Allergies.vue');  
const EMRPatientContacts                        = () => import('../../emr/patients/Contacts.vue'); 
const EMRPatientPrescriptions                   = () => import('../../emr/patients/Prescriptions.vue');  
const EMRPatientSearch                          = () => import('../../emr/patients/Search.vue');
const EMRPatientSingle                          = () => import('../../emr/patients/Single.vue');  
const EMRPatientVitals                          = () => import('../../emr/patients/Vitals.vue'); 

    //const EMRPatientFormAllergy                        = () => import('../../emr/patients/forms/Allergy.vue');
    //const EMRPatientFormContact                        = () => import('../../emr/patients/forms/Contact.vue');
    //const EMRFormPatientService               = () => import('../../emr/hims/forms/PatientService.vue');

    const EMRPatientDetailAllergies                    = () => import('../../emr/patients/details/Allergies.vue');
    const EMRPatientDetailBioData                      = () => import('../../emr/patients/details/BioData.vue');
    const EMRPatientDetailCard                         = () => import('../../emr/patients/details/Card.vue');
    const EMRPatientDetailFull                         = () => import('../../emr/patients/details/Full.vue');
    const EMRPatientDetailNextOfKin                    = () => import('../../emr/patients/details/NextOfKin.vue');
    const EMRPatientDetailPendingTransactions          = () => import('../../emr/patients/details/PendingTransactions.vue');
    const EMRPatientDetailPatientList                  = () => import('../../emr/patients/details/PatientList.vue');
    const EMRPatientDetailInsurances                   = () => import('../../emr/patients/details/Insurances.vue');
    
    const EMRPatientFormAllergy                         = () => import('../../emr/patients/forms/Allergy.vue');
    const EMRPatientFormContact                         = () => import('../../emr/patients/forms/Contact.vue');
    const EMRPatientFormInsurance                       = () => import('../../emr/patients/forms/Insurance.vue');
    const EMRPatientFormPassword                        = () => import('../../emr/patients/forms/Password.vue');
    const EMRPatientFormPatient                         = () => import('../../emr/patients/forms/Patient.vue');   
    const EMRPatientFormPrescription                    = () => import('../../emr/patients/forms/Prescription.vue');
    const EMRPatientFormRegistration                    = () => import('../../emr/patients/forms/Register.vue');
    const EMRPatientFormSearch                          = () => import('../../emr/patients/forms/Search.vue');
    const EMRPatientFormVital                           = () => import('../../emr/patients/forms/Vital.vue');

export default[
    {path: '/emr/front_office',                                 component: EMRFrontOfficeDashboard},
    {path: '/emr/front_office/dashboard',                       component: EMRFrontOfficeDashboard},
    {path: '/emr/front_office/patients',                        component: EMRPatientAll},
    {path: '/emr/front_office/patients/new',                    component: EMRPatientFormRegistration},
    {path: '/emr/front_office/patients/search',                 component: EMRPatientSearch},
    {path: '/emr/front_office/patients/:id',                    component: EMRPatientSingle},
];