const EMRAdmissionCategories               = () => import('../../emr/admission/Categories.vue');
const EMRAdmissionDashboard                = () => import('../../emr/admission/Dashboard.vue');    
const EMRAdmissionRequest                  = () => import('../../emr/admission/Request.vue');
const EMRAdmissionRequests                 = () => import('../../emr/admission/Requests.vue');
const EMRAdmissionRoom                     = () => import('../../emr/admission/Room.vue');
const EMRAdmissionRoomType                 = () => import('../../emr/admission/RoomType.vue');
const EMRAdmissionRoomTypes                = () => import('../../emr/admission/RoomTypes.vue');
const EMRAdmissionServices                 = () => import('../../emr/admission/Services.vue');
const EMRAdmissionWard                     = () => import('../../emr/admission/Ward.vue');
const EMRAdmissionWards                    = () => import('../../emr/admission/Wards.vue');

    const EMRAdmissionDetailBed                = () => import('../../emr/admission/details/Bed.vue');
    const EMRAdmissionDetailBedAssignment      = () => import('../../emr/admission/details/BedAssignment.vue');
    const EMRAdmissionDetailPrechecks          = () => import('../../emr/admission/details/Prechecks.vue');
    const EMRAdmissionDetailRequest            = () => import('../../emr/admission/details/Request.vue');
    const EMRAdmissionDetailRequestList        = () => import('../../emr/admission/details/RequestList.vue');
    const EMRAdmissionDetailRoom               = () => import('../../emr/admission/details/Room.vue');
    const EMRAdmissionDetailRoomList           = () => import('../../emr/admission/details/RoomList.vue');
    const EMRAdmissionDetailRoomType           = () => import('../../emr/admission/details/RoomType.vue');
    const EMRAdmissionDetailRoomTypeList       = () => import('../../emr/admission/details/RoomTypeList.vue');
    const EMRAdmissionDetailService            = () => import('../../emr/admission/details/Service.vue');
    const EMRAdmissionDetailServiceList        = () => import('../../emr/admission/details/ServiceList.vue');
    const EMRAdmissionDetailWard               = () => import('../../emr/admission/details/Ward.vue');
    const EMRAdmissionDetailWardList           = () => import('../../emr/admission/details/WardList.vue');

    const EMRAdmissionFormAdmit                = () => import('../../emr/admission/forms/Admit.vue');
    const EMRAdmissionFormBed                  = () => import('../../emr/admission/forms/Bed.vue');
    const EMRAdmissionFormBedAssignment        = () => import('../../emr/admission/forms/BedAssignment.vue');
    const EMRAdmissionFormCategory             = () => import('../../emr/admission/forms/Category.vue');
    const EMRAdmissionFormRequest              = () => import('../../emr/admission/forms/Request.vue');
    const EMRAdmissionFormPrecheck             = () => import('../../emr/admission/forms/Precheck.vue');
    const EMRAdmissionFormRoom                 = () => import('../../emr/admission/forms/Room.vue');
    const EMRAdmissionFormRoomType             = () => import('../../emr/admission/forms/RoomType.vue');
    const EMRAdmissionFormService              = () => import('../../emr/admission/forms/Service.vue');
    const EMRAdmissionFormWard                 = () => import('../../emr/admission/forms/Ward.vue');

    export default[
        {path: '/emr/admission',                                    component: EMRAdmissionDashboard},
        {path: '/emr/admission/categories',                         component: EMRAdmissionCategories},
        {path: '/emr/admission/requests',                           component: EMRAdmissionRequests},
        {path: '/emr/admission/requests/:id',                       component: EMRAdmissionRequest},
        {path: '/emr/admission/rooms/:id',                          component: EMRAdmissionRoom},
        {path: '/emr/admission/room_types',                         component: EMRAdmissionRoomTypes},
        {path: '/emr/admission/room_types/:id',                     component: EMRAdmissionRoomType},
        {path: '/emr/admission/services',                           component: EMRAdmissionServices},
        {path: '/emr/admission/wards',                              component: EMRAdmissionWards},
        {path: '/emr/admission/wards/:id',                          component: EMRAdmissionWard},
    ];