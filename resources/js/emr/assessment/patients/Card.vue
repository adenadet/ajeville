<template>
<section>
    <div class="user-profile layout-spacing">
        <div class="widget-content widget-content-area">
            <div class="text-center user-info">
                <img :src="(patient.image) ? '/img/profile/'+patient.image : '/img/profile/default.png'" width="300" height="auto" alt="avatar">
                <p class=""></p>
            </div>
            <div class="patient-info-list">
                <div class="">
                    <ul class="contacts-block list-unstyled">
                        <li class="contacts-block__item">
                            <i class="fa fa-patient" width="24" height="24"></i> {{patient.first_name}} {{patient.middle_name}} {{patient.last_name}}
                        </li>
                        <li class="contacts-block__item">
                            <i class="fa fa-calendar" width="24" height="24"></i> {{patient.dob | ExcelDate}}
                        </li>
                        <li class="contacts-block__item">
                            <i class="fa fa-map-marker" width="24" height="24"></i> 
                            {{patient.street}} {{patient.street2 ? ', '+patient.street2: ''}}<br />
                            {{patient.city}}, {{patient.area_id ? patient.area.name : ''}}, {{patient.state_id ? patient.state.name: ''}}.  
                        </li>
                        <li class="contacts-block__item">
                            <a :href="'mailto:'+patient.email"><i class="fa fa-envelope" width="24" height="24"></i> {{patient.email}}</a>
                        </li>
                        <li class="contacts-block__item">
                            <i class="fa fa-phone" width="24" height="24"></i> {{patient.phone}} {{patient.alt_phone ? ', '+patient.alt_phone: ''}} 
                        </li>
                    </ul>
                </div>                                    
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            areas:[],  
            branches:[],  
            departments:[], 
            editMode: true, 
            nok:{},
            states:[],  
            user:{}, 
        }
    },
    mounted() {
        console.log('Component mounted.')
    },
    created() {
        //this.getInitials();
    },
    methods:{
        getInitials(){
            axios.get('/api/ums/profile').then(response =>{
                this.user = response.data.user;
                this.areas = response.data.areas;
                this.branches = response.data.branches;
                this.departments = response.data.departments;
                this.states = response.data.states;
                this.nok = response.data.nok;
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
                Fire.$emit('BioDataFill', this.user);
                Fire.$emit('NextOfKinFill', this.nok);
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Profile not loaded successfully',
                })
            });
        },
        getProfilePic(){
            let  photo = (this.form.image.length >= 150) ? this.form.image : "./"+this.form.image;
            return photo;
        },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.form.image = reader.result
                    }
                reader.readAsDataURL(file)
            }
            else{
                swal.fire({
                    type: 'error',
                    title: 'File is too large'
                });
            }
        }
    },
    props:{
        patient: Object,
    }
}
</script>