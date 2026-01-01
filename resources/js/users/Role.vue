<template>
    
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
            role: {},
            roles:{},
            savings:{},
            states:[],
            user:{},
            users:{},
            form: new Form({}),
        }
    },
    methods:{
        add(){
            this.editMode = false;
            this.user = {};
            Fire.$emit('BioDataFill', this.user);
            $('#userModal').modal('show');

            this.$Progress.finish();
        },
        deleteRole(id){
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
                    this.form.delete('/api/ums/roles/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editRole(role){
            this.$Progress.start();
            this.editMode = true;
            this.role = role;
            Fire.$emit('RoleDataFill', role);
            $('#roleModal').modal('show');

            this.$Progress.finish();
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/ums/roles').then(response =>{
                this.roles = response.data.roles
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Roles loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
        },
        getRoles(page=1){
            axios.get('/api/ums/roles?page='+page)
            .then(response=>{
                this.roles = response.data.roles;   
            });
        },
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('/api/ums/roles/search?q='+query)
            .then((response ) => {
                this.users = response.data.users;
            })
            .catch(()=>{

            });
        });
    },
}
</script>
