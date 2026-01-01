<template>
<div class="row clearfix" @refreshPage="refreshPage">
    <div class="modal fade" id="userModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit User: {{user.unique_id}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <UserFormStaff :areas="areas" :branches="branches" :departments="departments" :editMode="editMode" :states="states" :user.sync="user"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="roleModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Assign User Roles' : 'Add User Roles' }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <UserFormAssignRole :user.sync="user"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Users</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-primary float-sm-right" @click="addUser()">Add New User <i class="fa fa-user-add"></i></button>
                </div>
            </div>
            <div class="card-body overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 d-flex align-items-stretch" v-for="user in users.data" :key="user.id">
                        <div class="card bg-light">
                            <div class="card-header text-muted border-bottom-0">&nbsp;</div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="lead"><b>{{user.first_name}} {{user.middle_name}} {{user.last_name}}</b></h2>
                                    </div>
                                    <div class="col-5 text-center">
                                        <img style="height: 100px;" :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" alt="" class="img-circle img-fluid">
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
                                    <button class="btn btn-sm btn-success" @click="setUserRole(user)" title="Set Staff Role"><i class="fa fa-user-cog"></i></button>
                                    <button class="btn btn-sm btn-primary" @click="editUser(user)" title="Edit Staff"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)" title="Delete Staff"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="users.per_page != null ? users.per_page : 52" :records="users.total != null ? users.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</div>
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
            current_page: 1,
            areas:[],
            branches:[],
            departments:[],
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
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
            $('#userModal').modal('show');
        },
        closeModals(){
            $('#userModal').modal('hide'); 
            $('#roleModal').modal('hide');
            //this.users = response.data.users;
        },
        deleteUser(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/ums/staffs/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editUser(user){
            this.editMode = true;
            this.user = user;
            $('#userModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true
            axios.get('/api/ums/staffs?page='+page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                toast.fire({icon: 'success', title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Users not loaded successfully',})
            });
        },
        refreshPage(response){
            this.areas = response.data.areas;
            this.branches = response.data.branches;
            this.current_page = response.data.users.current_page;
            this.departments = response.data.departments;
            this.states = response.data.states;
            this.users = response.data.users;
            this.closeModals();
        },
        searchUser(){
            //let query = this.$parent.search;
            axios.get('/api/ums/users/search?q='+query)
            .then((response ) => {this.users = response.data.users;})
            .catch(()=>{});
        },
        setUserRole(user){
            this.user = user;
            this.editMode = true;
            $('#roleModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
        /*Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('/api/ums/users/search?q='+query)
            .then((response ) => {this.users = response.data.users;})
            .catch(()=>{});
        });
        Fire.$on('userRoleReload', response =>{});
        Fire.$on('Reload', response =>{
            $('#userModal').modal('hide'); 
            $('#roleModal').modal('hide');
            this.users = response.data.users;
        });*/
    },
}
</script>