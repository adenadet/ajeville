<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-sm-8 col-md-8 offset-md-2 offset-sm-2 d-flex align-items-stretch flex-column">
                <div class="card bg-light d-flex flex-fill">
                    <div class="card-header bg-navy">Staff Details</div>
                    <div v-if="employee.user == null" class="card-body pt-0 overlay-wrapper" style="height: 500px">
                        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    </div>
                    <div v-else class="card-body pt-0 overlay-wrapper" style="height: 500px">
                        <!--div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div-->
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b>{{FullName(employee.user)}}</b></h2>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> Email: {{employee.user.email}}</li>
                                    <!--li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Branch: {{((typeof employee.user.branch != 'undefined') && (user.branch !== null))? user.branch.name: ''}}</li-->
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Department: {{((typeof employee.department != 'undefined') && (employee.department !== null))? employee.department.name: ''}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone-square-alt"></i></span> Office Phone: {{((typeof employee.department != 'undefined') && (employee.department !== null))? employee.department.phone: ''}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone-alt"></i></span> Phone #: {{employee.user.phone}}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-birthday-cake"></i></span> Birthday: {{ExcelDateMonth(employee.user.dob) }}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-sign-in-alt"></i></span> Joined: {{getAge(employee.user.joined_at)}} ago</li>
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img :src="(employee.user.image) ? '/img/profile/'+employee.user.image : '/img/profile/default.png'" alt="" class="img-circle img-fluid">
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
export default {
    data(){
        return {
            editMode: false,
            loading:false,
            employee:{},
        }
    },
    methods:{
       chatUser(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "A new chat would be created between you and the user",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, create it!'
            })
            .then((result) => {
                if(result.value){
                    axios.get('/api/chats/rooms/check/'+id)//check if there is an existing Room with only these two people
                    .then(response =>{this.$router.push('/chats');})
                    .catch(()=>{this.$toast.fire({icon: 'error', title: 'Chats not loaded successfully',})})
                }
            });  
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/employees/user/'+this.$route.params.id).then(response =>{
                this.employee = response.data.employee;
                this.loading = false;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Employee was not loaded successfully',})
            });
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>