<template>
<section class="overlay-wrapper">
    <div class="modal fade" id="userFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ editMode ? 'Edit User: '+ user.unique_id : 'New User'}} </h4>
                    <button type="button" class="close" @click="closeModals"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <UmsFormBioData :editMode.sync="editMode" :user.sync="user" @reloadUser="$emit('refreshUserlist')"/>
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
                    <UmsFormAssignRole :user.sync="user"/>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap" v-if="view == 'list'">
        <thead>
            <tr>
                <th>User</th>
                <th>ID</th>
                <th>Joined Date</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody v-if="users.length > 0">
            <tr v-for="user in users" :key="user.id">

                <td>{{ user.first_name != null ? FullName(user) : user.name }}</td>
                <td>{{ user.unique_id }}</td>
                <td>{{ ExcelDate(user.created_at) }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.phone }}</td>
                
                <td>{{ user.city != null ? user.city : "No City"}}{{user.state != null ? ', '+user.state.name+' State': ''}}</td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="btn btn-sm btn-block dropdown-item" @click="setUserRole(user)" title="Set Staff Role"><i class="fa fa-user-cog text-success mr-1"></i>Set User Role</button>
                        <button class="btn btn-sm btn-block dropdown-item" @click="editUser(user)" title="Edit Staff"><i class="fa fa-edit text-primary mr-1"></i>Edit User</button>
                        <button class="btn btn-sm btn-block dropdown-item" @click="resendLink(user.id)" title="Resend Confirmation Link"><i class="fa fa-envelope text-warning mr-1"></i>Resend Confirmation Link</button>
                        <button class="btn btn-sm btn-block dropdown-item" @click="deleteUser(user.id)" title="Delete Staff"><i class="fa fa-trash text-danger mr-1"></i> Deactivate/Reactivate User</button>       
                        <!--router-link :to="'/settings/price_lists/'+price_list.price_list_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-1"></i> View Price List</router-link>
                        <button class="btn btn-block dropdown-item"><i class="fas fa-refresh mr-1"></i> Deactivate/Reactivate</button-->
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5" class="text-center">No user found</td>
            </tr>
        </tbody>
    </table>
    <div class="row p-3" v-else-if="view == 'grid'">
        <div class="col-lg-3 col-md-3 col-sm-6 d-flex align-items-stretch" v-for="user in users" :key="user.id">
            <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">&nbsp;</div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-7">
                            <h2 class="lead"><b>{{user.first_name == null ? user.name : FullName(user)}}</b></h2>
                        </div>
                        <div class="col-5 text-center">
                            <img style="height: 100px;" :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" alt="" class="img-circle img-fluid">
                        </div>
                        <div class="col-12">
                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> Email: {{user.email}}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> City: {{ user.city }} {{user.state != null ? ', '+user.state.name: ''}}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: {{user.phone}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-right">
                        <button class="btn btn-sm btn-warning" @click="resendLink(user.id)" title="Set Staff Role"><i class="fa fa-envelope"></i></button>
                        <button class="btn btn-sm btn-success" @click="setUserRole(user)" title="Set Staff Role"><i class="fa fa-user-cog"></i></button>
                        <button class="btn btn-sm btn-primary" @click="editUser(user)" title="Edit Staff"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)" title="Delete Staff"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
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
            user:{},
        }
    },
    emits:['refreshUserlist'],
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
            this.$swal.fire({
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
                    this.form.delete('/api/ums/users/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editUser(user){
            this.editMode = true;
            this.user = user;
            $('#userFormModal').modal('show');
        },
        resendLink(user){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.loading = true;
                    axios.get('/api/ums/users/resend_link/'+user)
                    .then(response=>{
                        this.$swal.fire('Done!', 'A confirmation mail has been sent to mail', 'success');
                        this.$emit('refreshUserlist');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                    this.loading = false;   
                    
                }
            });  
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
        //this.getAllInitials();
    },
    props:{
        users: Array,
        view: String,
    }
}
</script>