<template>
<section class="col-md-12">
    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">My Courses</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <LearnDetailUserCourse :user_course.sync="user_course" source="student" />
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            form: new Form({}),
            lessons: [],
            loading: false,
            user_course: {course: {name: 'Test', lessons: []}},
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/learn/user_courses/'+this.$route.params.id+'?type=student&query='+this.query)
            .then(response =>{
                this.refresh(response);
                this.$toast.fire({icon: 'success', title: 'Learn User Course loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Learn User Course not loaded successfully',})
            });
            this.loading = false;
        },
        refresh(response){
            this.user_course = response.data.user_course;
        }
    },
    mounted(){ 
        this.getAllInitials();
    }
}
</script>