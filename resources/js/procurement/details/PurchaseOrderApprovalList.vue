<template>
<section class="row">
    <div class="col-md-12">
        <div class="timeline">
            <div class="time-label">
                <span class="bg-red">10 Feb. 2014</span>
            </div>
            <div>
                <i class="fas fa-envelope bg-blue"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> 12:05</span>
                    <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                    <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                        jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                        quora plaxo ideeli hulu weebly balihoo...
                    </div>
                    <div class="timeline-footer">
                        <a class="btn btn-primary btn-sm">Read more</a>
                        <a class="btn btn-danger btn-sm">Delete</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-user bg-green"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                  <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                </div>
            </div>
            <div>
                <i class="fas fa-comments bg-yellow"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> 27 mins ago</span>
                    <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                    <div class="timeline-body">
                        Take me to your leader!
                        Switzerland is small and neutral!
                        We are more like Germany, ambitious and misunderstood!
                    </div>
                    <div class="timeline-footer">
                        <a class="btn btn-warning btn-sm">View comment</a>
                    </div>
                </div>
            </div>
            <div class="time-label">
                <span class="bg-green">3 Jan. 2014</span>
            </div>
            <div>
                <i class="fa fa-camera bg-purple"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> 2 days ago</span>
                    <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                    <div class="timeline-body">
                        <img src="http://placehold.it/150x100" alt="...">
                        <img src="http://placehold.it/150x100" alt="...">
                        <img src="http://placehold.it/150x100" alt="...">
                        <img src="http://placehold.it/150x100" alt="...">
                        <img src="http://placehold.it/150x100" alt="...">
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-video bg-maroon"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> 5 days ago</span>
                    <h3 class="timeline-header"><a href="#">Mr. Doe</a> shared a video</h3>
                    <div class="timeline-body">
                        <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tMWkeBIohBs" frameborder="0" allowfullscreen=""></iframe>
                        </div>
                    </div>
                    <div class="timeline-footer">
                        <a href="#" class="btn btn-sm bg-maroon">See comments</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-clock bg-gray"></i>
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
            employee: {},
            form: new Form({}),
            loading: false,
            query: '',
            savings:{},
            states:[],
            employee:{},
            users:{},
        }
    },
    emits: ['refreshPage'],
    methods:{
        addUser(){
            this.editMode = false;
            this.user = {};
            $('#userModal').modal('show');
        },
        assignLeaveType(employee){
            //this.editMode = false;
            this.employee = employee;
            $('#leaveTypeModal').modal('show');
        },
        closeModals(){
            $('#employeeModal').modal('hide');
            $('#employeeStatusModal').modal('hide');
            $('#userModal').modal('hide'); 
            $('#roleModal').modal('hide');
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
                    this.form.delete('/api/ums/staffs/'+id)
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
            axios.get('/api/ums/staffs?page='+page).then(response =>{
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
        modifyEmployee(employee){
            this.employee = employee;
            this.editMode = true;
            $('#employeeModal').modal('show');
        },
        refreshPage(response){
            this.$emit('refreshPage', response);
            this.closeModals();
        },
        searchUser(){
            axios.get('/api/hrms/staffs/search/'+this.query)
            .then((response ) => {this.users = response.data.users;})
            .catch(()=>{});
        },
        setUserRole(user){
            this.user = user;
            this.editMode = true;
            $('#roleModal').modal('show');
        },
        updateEmployeeStatus(employee){
            this.employee = employee;
            $('#employeeStatusModal').modal('show');
        }
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        items: Array,
        source: String,
    },
    watch(){
        
    },
}
</script>