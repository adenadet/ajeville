<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-sm-8 col-md-8 offset-md-2 offset-sm-2 d-flex align-items-stretch flex-column">
                <div class="card bg-light d-flex flex-fill">
                    <div class="card-header text-muted border-bottom-0">Staff Details</div>
                    <div class="card-body pt-0 overlay-wrapper">
                        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b>{{user.first_name}} {{user.middle_name}} {{user.last_name}}</b></h2>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> Email: {{user.email}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Branch: {{((typeof user.branch != 'undefined') && (user.branch !== null))? user.branch.name: ''}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Department: {{((typeof user.department != 'undefined') && (user.department !== null))? user.department.name: ''}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone-square-alt"></i></span> Office Phone: {{((typeof user.department != 'undefined') && (user.department !== null))? user.department.phone: ''}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone-alt"></i></span> Phone #: {{user.phone}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-birthday-cake"></i></span> Birthday: {{ExcelDateMonth(user.dob) }}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-sign-in-alt"></i></span> Joined: {{getAge(user.joined_at)}} ago</li>
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" alt="" class="img-circle img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <button class="btn btn-sm btn-primary" @click="chatUser(user.id)"><i class="fas fa-comment-alt"></i> Chat with Staff</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import Form from 'vform';
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
        return {
            areas:[],
            branches:[],
            departments:[],
            editMode: false,
            loading:false,
            savings:{},
            states:[],
            user:{},
            users:{},
        }
    },
    methods:{
       chatUser(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "A new chat would be created between you and the user",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, create it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    axios.get('/api/chats/rooms/check/'+id)//check if there is an existing Room with only these two people
                    .then(response =>{this.$router.push('/chats');})
                    .catch(()=>{toast.fire({icon: 'error', title: 'Chats not loaded successfully',})})
                }
            });  
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/staffs/'+this.$route.params.id).then(response =>{
                this.areas = response.data.areas;
                this.branches = response.data.branches;
                this.departments = response.data.departments;
                this.states = response.data.states;
                this.users = response.data.users;
                this.loading = false;
                this.user = response.data.user;
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
        },
        getUser(page=1){
            axios.get('/api/ums/users?page='+page)
            .then(response=>{
                this.users = response.data.users;   
            });
        },
        setUserRole(user){
            this.$Progress.start();
            this.user = user;
            Fire.$emit('userRoleUpdate', user);
            $('#roleModal').modal('show');
            this.$Progress.finish();
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>