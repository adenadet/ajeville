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
                    <UmsFormBioData :editMode.sync="editMode" :user.sync="user" @reloadUser="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Users</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 350px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        <button class="btn btn-sm btn-primary ml-3" @click="addUser()">Add New User <i class="fa fa-user-add"></i></button>
                        <button class="btn btn-sm btn-default ml-1" @click="changeView('grid')" v-if="view == 'list'"><i class="fa fa-table"></i></button>
                        <button class="btn btn-sm btn-default ml-1" @click="changeView('list')" v-if="view == 'grid'"><i class="fa fa-list"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 500px;">
                <UmsDetailUserlist :users.sync="users.data" :view="view" @refreshPage="getAllInitials" />
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
            users:{data: [], total: 0},
            view: 'list', //list or grid
        }
    },
    methods:{
        addUser(){
            this.editMode = false;
            this.user = {};
            $('#userModal').modal('show');
        },
        changeView(type){
            this.view = type;
        },
        closeModals(){
            $('#userFormModal').modal('hide'); 
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
            $('#userModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true
            axios.get('/api/ums/users?page='+page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Users not loaded successfully',})
            });
        },
        refreshPage(response){
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
    },
}
</script>