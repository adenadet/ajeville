<template>
<section>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-calendar mr-1"></i>Appointments</h3>
        </div>
        <div class="card-body">
            <ul class="todo-list" data-widget="todo-list">
                <li>
                    <span class="handle">
                        <i class="fas fa-ellipsis-v"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div  class="icheck-primary d-inline ml-2">
                        <input type="checkbox" value="" name="todo1" id="todoCheck1">
                        <label for="todoCheck1"></label>
                    </div>
                    <span class="text">Design a nice theme</span>
                    <small class="badge badge-danger"><i class="far fa-clock"></i> 2 mins</small>
                    <div class="tools"><i class="fas fa-edit"></i><i class="fas fa-trash-o"></i></div>
                </li>
            </ul>                
        </div>
        <div class="card-footer clearfix"><button type="button" class="btn btn-primary float-right"><i class="fas fa-eye"></i> See All</button></div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
                
        }
    },
    methods:{
        addCourse(){
            this.$Progress.start();
            this.editMode = false;
            this.course = {};
            Fire.$emit('CourseDataFill', {});
            $('#courseModal').modal('show');
            this.$Progress.finish();
        },
        deleteCourse(id){

        },
        editCourse(course){
            this.$Progress.start();
            this.editMode = true;
            this.course = course;
            Fire.$emit('CourseDataFill', course);
            $('#courseModal').modal('show');
            this.$Progress.finish();
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/lms/tut_courses').then(response =>{
                this.categories = response.data.categories;
                //this.certificate_types = response.data.certificate_types;
                this.courses = response.data.courses;
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Courses were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Courses were not loaded successfully',
                })
            });
        },
        getCourses(page=1){
            axios.get('/api/lms/courses?page='+page)
            .then(response=>{
                this.courses = response.data.courses;   
            });
        },
        seeCourse(course){
            this.$Progress.start();
            this.course = course;
            this.$Progress.finish();
        },
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{},
}
</script>