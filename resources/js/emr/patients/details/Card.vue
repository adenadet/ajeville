<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="appointmentFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Appointment' : 'Create Appointment'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><EMRFrontOfficeFormAppointment :patient="patient" :appointment.sync="{}" @refreshFormAppointment="refreshPage"/></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="endVisitFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title">End Visit</h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><EMRFrontOfficeFormEndVisit :patient="patient" :visit_id.sync="visit?.id " @refreshEndVisitForm="refreshPage"/></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="visitFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Visit' : 'Create Visit'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><EMRFrontOfficeFormVisit :patient.sync="patient" :visit.sync="{}" @refreshFormVisit="refreshPage"/></div>
            </div>
        </div>
    </div>

    <div class="user-profile">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img :src="(patient?.user != null) && (patient?.user.image != null) ? '/img/profile/'+patient?.user.image : '/img/profile/default.png'" width="300" height="auto" alt="avatar" class="profile-user-img img-fluid img-circle">
                </div>
                <h3 class="profile-username text-center">{{patientName(patient) }}</h3>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <i class="fa fa-calendar" width="24" height="24"></i> {{patient?.user != null ? ExcelDate(patient?.user.dob) : ''}}
                    </li>
                    <li class="list-group-item">
                        <a :href="'mailto:'+(patient?.user != null ? patient?.user.email : '')"><i class="fa fa-envelope" width="24" height="24"></i> {{patient?.user != null ? patient?.user.email : ''}}</a>
                    </li>
                    <li class="list-group-item" v-if="patient?.user != null">
                        <i class="fa fa-phone" width="24" height="24"></i> {{patient?.user.phone}} {{patient?.user.alt_phone ? ', '+patient?.user.alt_phone: ''}} 
                    </li>
                    <li class="list-group-item" v-if="patient?.user != null">
                        <i class="fa fa-money-bill" width="24" height="24"></i> <span :class="patient?.balance < 0 ? 'text-primary' : 'text-danger'">{{currency(patient?.balance)}}</span> 
                    </li>
                </ul>
                <button @click="endVisit(visit)" v-if="visit != null && visit.end_date == null" class="btn btn-danger btn-block"><b>End Visit</b></button>
                <button @click="createVisit" v-else class="btn btn-primary btn-block"><b>Create Visit</b></button>
                <button @click="bookAppointment" class="btn btn-success btn-block"><b>Book Appointment</b></button>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRFrontOfficeFormAppointment from '@/emr/front_office/forms/Appointment.vue';
import EMRFrontOfficeFormEndVisit from '@/emr/front_office/forms/EndVisit.vue';
import EMRFrontOfficeFormVisit from '@/emr/front_office/forms/Visit.vue';
export default {
    components:{
        EMRFrontOfficeFormAppointment, EMRFrontOfficeFormEndVisit, EMRFrontOfficeFormVisit 
    },
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data(){
        return  {
            editMode: false,
            loading: false,
            insurance: {},
            insurances: [], 
        }
    },
    emits:['refreshPatientCard'],
    methods:{
        bookAppointment(){
            this.loading = true;
            $('#appointmentFormModal').modal('show');
            this.loading = false;
        },
        createVisit(){
            this.loading = true;
            $('#visitFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#appointmentFormModal').modal('hide');
            $('#endVisitFormModal').modal('hide');
            $('#visitFormModal').modal('hide');
        },
        editUser(user){
            this.loading = true;
            this.editMode = true;
            $('#bioDataModal').modal('show');
            this.loading = false;
        },
        endVisit(visit){
            this.loading = true;
            $('#endVisitFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.$emit('refreshPatientCard')
            this.closeModal();
        }
    },
    mounted() {},
    props:{
        source: String,
    },
    watch:{}
}
</script>