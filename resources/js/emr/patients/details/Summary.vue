<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="user-profile">
        <div class="card card-outline" :class="'card-'+variant">
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
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
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
            $('#visitFormModal').modal('hide');
        },
        editUser(user){
            this.loading = true;
            this.editMode = true;
            $('#bioDataModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.$emit('refreshPatientCard')
            this.closeModal();
        }
    },
    mounted() {},
    props:{
        patient: Object,
        variant: String,
        view: String,
    },
    watch:{}
}
</script>