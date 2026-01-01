<template>
<section class="overlay-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Lesson Details</h3>
                </div>
                <div class="card-body p-0">
                    <LearnDetailLessonList :lessons.sync="lessons" @refreshLessonList="getAllInitials()" />
                </div>
                <!--div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="lessons.per_page != null ? lessons.per_page : 52" :records="lessons.total != null ? lessons.total : 550" ></pagination>
                </div-->
            </div>
        </div>
    </div>
</section>
</template>
<script>
    export default {
        data(){
            return {
                certificate_types:[],
                course:{},
                courses:{},
                categories:[],
                editMode: false,
                exam_types: [],
                form: new Form({}),
                sub_categories:[],
                users:[],   
            }
        },
        emits:['refreshLessonForm'],
        methods:{
            addLesson(){
                this.loading = true;
                this.editMode = false;
                this.lesson = {};
                $('#lessonModal').modal('show');
                this.loading = false;
            },
            deleteLesson(id){
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
                        this.form.delete('/api/learn/lessons/'+id)
                        .then(response=>{
                            this.$swal.fire('Deleted!', 'Lesson has been deactivated/reactivated.', 'success');
                            this.$emit('refreshLessonForm');
                        })
                        .catch(()=>{
                            this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                        });
                    }
                });
            },
            editLesson(lesson){
                this.loading = true;
                this.editMode = true;
                this.lesson = lesson;
                $('#lessonModal').modal('show');
                this.loading = false;
            },
            getAllInitials(){
                this.loading = true;
                axios.get('/api/learn/lessons').then(response =>{
                    this.refresh(response);
                    toast.fire({
                        icon: 'success',
                        title: 'Lessons were loaded successfully',
                    });
                })
                .catch(()=>{
                    this.$Progress.fail();
                    toast.fire({
                        icon: 'error',
                        title: 'Lessons were not loaded successfully',
                    })
                });
                this.loading = false;
            },
            getCourses(page=1){
                axios.get('/api/lms/courses?page='+page)
                .then(response=>{
                    this.courses = response.data.courses;   
                });
            },
            refresh(response){
                this.categories = response.data.categories;
                this.certificate_types = response.data.certificate_types;
                this.courses = response.data.courses;
                this.course = this.courses.data[0];
                this.departments = response.data.departments;
                this.exam_types = response.data.exam_types;
                this.users = response.data.users;
            },
            seeCourse(course){
                this.loading = true;
                this.course = course;
                this.loading = false;
            },
        },
        mounted() {
            this.getAllInitials();
        }
    }
</script>