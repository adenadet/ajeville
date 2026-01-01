<template>
    <div class="user-profile">
        <div class="widget-content widget-content-area">
            <div class="text-center user-info">
                <img :src="(patient.user != null) && (patient.user.image != null) ? '/img/profile/'+patient.user.image : '/img/profile/default.png'" width="300" height="auto" alt="avatar">
                <p class=""></p>
            </div>
            <div class="user-info-list">
                <div class="">
                    <ul class="contacts-block list-unstyled">
                        <li class="contacts-block__item">
                            <i class="fa fa-user" width="24" height="24"></i> {{patientName(patient) }}
                        </li>
                        <li class="contacts-block__item">
                            <i class="fa fa-calendar" width="24" height="24"></i> {{patient.user != null ? patient.user.dob : ''}}
                        </li>
                        <li class="contacts-block__item">
                            <a :href="'mailto:'+(patient.user != null ? patient.user.email : '')"><i class="fa fa-envelope" width="24" height="24"></i> {{patient.user != null ? patient.user.email : ''}}</a>
                        </li>
                        <li class="contacts-block__item" v-if="patient.user != null">
                            <i class="fa fa-phone" width="24" height="24"></i> {{patient.user.phone}} {{patient.user.alt_phone ? ', '+patient.user.alt_phone: ''}} 
                        </li>
                        <li class="contacts-block__item" v-if="patient.user != null">
                            <i class="fa fa-money-bill" width="24" height="24"></i> {{patient.balance | currency}} 
                        </li>
                    </ul>
                </div>                                    
            </div>
        </div>
    </div>
</template>
<script>
export default {
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
            insurance: {},
            insurances: [], 
        }
    },
    mounted() {
        /*Fire.$on('patientReset', () => {
            this.getInitials(this.patient.id);
        });*/
    },
    methods:{
        closeModal(){
            $('#bioDataModal').modal('hide');
        },
        editUser(user){
            this.loading = true;
            this.editMode = true;
            $('#bioDataModal').modal('show');
            this.loading = false;
        },
    },
    props:{
        source: String,
    },
    watch:{}
}
</script>