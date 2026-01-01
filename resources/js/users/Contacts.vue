<template>
    <section class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Contacts</h3>
                </div>
                <div class="card-body overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch" v-for="user in users.data" :key="user.id">
                            <div class="card bg-light">
                                <div class="card-header text-muted border-bottom-0">&nbsp;</div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="lead"><b>{{user.first_name}} {{user.middle_name}} {{user.last_name}}</b></h2>
                                        </div>
                                        <div class="col-5 text-center">
                                            <img :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" alt="" class="img-circle img-fluid">
                                        </div>
                                        <div class="col-12">
                                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> Email: {{user.email}}</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Dept: {{((typeof user.department != 'undefined') && (user.department !== null))? user.department.name: ''}}</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: {{user.phone}}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-right">
                                        <router-link :to="'/contacts/'+user.id" class="btn btn-sm btn-primary" title="View Staff" ><i class="fa fa-eye"></i></router-link>
                                        <button class="btn btn-sm btn-success" title="Chat with Staff" @click="chatUser(user.id)"><i class="fa fa-comment-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card-footer">
                        <!--pagination :data="users" @pagination-change-page="getUser">
                            <span slot="prev-nav">&lt; Previous </span>
                            <span slot="next-nav">Next &gt;</span>
                        </pagination-->
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
        addUser(){
            this.editMode = false;
            this.user = {};
            //Fire.$emit('BioDataFill', this.user);
            $('#userModal').modal('show');

            this.$Progress.finish();
        },
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
            axios.get('/api/ums/staffs').then(response =>{
                this.areas = response.data.areas;
                this.branches = response.data.branches;
                this.departments = response.data.departments;
                this.states = response.data.states;
                this.users = response.data.users;
                this.loading = false;
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