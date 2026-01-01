<template>
    <div class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12 layout-top-spacing">
                <div class="user-profile layout-spacing">
                    <div class="widget-content widget-content-area">
                        <div class="text-center user-info">
                            <img :src="(user.image) ? '/img/profile/'+user.image : ''" width="300" height="auto" alt="avatar">
                            <p class=""></p>
                        </div>
                        <div class="user-info-list">
                            <div class="">
                                <ul class="contacts-block list-unstyled">
                                    <li class="contacts-block__item">
                                        <i class="fa fa-user mr-1" width="24" height="24"></i> {{FullName(user)}}
                                    </li>
                                    <li class="contacts-block__item">
                                        <i class="fa fa-calendar mr-1" width="24" height="24"></i> {{ExcelDate(user.dob) }}
                                    </li>
                                    <li class="contacts-block__item">
                                        <i class="fa fa-map-marker mr-1" width="24" height="24"></i> 
                                        {{user.street}} {{user.street2 ? ', '+user.street2: ''}}<br />
                                        {{user.city}}, {{user.area_id ? user.area.name : ''}}, {{user.state_id ? user.state.name: ''}}.  
                                    </li>
                                    <li class="contacts-block__item">
                                        <a :href="'mailto:'+user.email"><i class="fa fa-envelope mr-1" width="24" height="24"></i> {{user.email}}</a>
                                    </li>
                                    <li class="contacts-block__item">
                                        <i class="fa fa-phone mr-1" width="24" height="24"></i> {{user.phone}} {{user.alt_phone ? ', '+user.alt_phone: ''}} 
                                    </li>
                                </ul>
                            </div>                                    
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#bio-data" data-toggle="tab">Bio Data</a></li>
                        <li class="nav-item"><a class="nav-link" href="#next-of-kin" data-toggle="tab">Next of Kin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">Password</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="bio-data">
                                <UmsFormBioData :editMode.sync="editMode" :user.sync="user"/>
                            </div>
                            <div class="tab-pane" id="next-of-kin">
                                <UmsFormNOK :nok="nok"/>
                            </div>
                            <div class="tab-pane" id="password">
                                <UmsFormPassword />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                           
    </div>
</template>
<script>
import UserFormBioData      from './forms/BioData.vue';
import UserFormNOK    from './forms/NextOfKin.vue';
import UserFormPassword     from './forms/Password.vue';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
    data(){
        return  {
            areas:[],  
            branches:[],  
            departments:[], 
            editMode: true, 
            loading: false,
            nok:{},
            states:[],
            nations: [],  
            user:{}, 
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/profile').then(response =>{
                this.reloadProfile(response);
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
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
        reloadProfile(response){
            this.user = response.data.user;
            this.areas = response.data.areas;
            this.branches = response.data.branches;
            this.departments = response.data.departments;
            this.states = response.data.states;
            this.nok = response.data.nok;
            this.nations = response.data.nations;
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
    }
}
</script>