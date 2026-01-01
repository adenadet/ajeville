<template>
<div class="row clearfix">
    <!--div class="modal fade" id="courseModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Course: {{course.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Course</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LearnFormCourse :categories="categories" :course="course" :sub_categories="sub_categories" :editMode="editMode" :exam_types="exam_types" :certificate_types="certificate_types" :tutors="users" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assigneeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Users</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <LearnFormAssignUser aspire="u_course" :course="course" :departments="departments" :users="users"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tutorModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Tutors</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <LearnFormAssignTutor />
                </div>
            </div>
        </div>
    </div-->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">{{course.name}} Detail</h3>
                <div class="card-tools" v-if="source == 'admin' || source == 'tutor'">
                    <button type="button" class="btn btn-sm btn-default" title="Edit Course" @click="editCourse(course)"><i class="fas fa-edit"></i></button>
                    <button type="button" class="btn btn-sm btn-primary" title="Assign Tutors" @click="addTutors(course.id)"><i class="fas fa-chalkboard-teacher"></i></button>
                    <button type="button" class="btn btn-sm btn-success" title="Assign new User" @click="addAssignees(course.id)"><i class="fas fa-user-plus"></i></button>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                        <div class="row" v-if="source == 'admin'">
                            <div class="col-12 col-sm-4">
                                <div class="small-box bg-light">
                                    <div class="inner"><h3>{{ typeof course.tutors != 'undefined' && course.tutors != null && course.tutors.length != 0 ? course.tutors.length : 0}}</h3><p>Tutors </p></div>
                                    <div class="icon"><i class="fa fa-chalkboard-teacher"></i></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="small-box bg-light">
                                    <div class="inner"><h3>{{ currency(course.price) }}</h3><p>Estimated Cost</p></div>
                                    <div class="icon"><i class="fa fa-wallet"></i></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="small-box bg-light">
                                    <div class="inner"><h3> {{ typeof course.assignees != 'undefined' && course.assignees != null ? course.assignees.length : 0}}</h3><p>Enrollees</p></div>
                                    <div class="icon"><i class="fa fa-users"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="text-success"><i class="fas fa-book"></i> {{course.name}}</h3>
                                <p class="text-muted" v-html="course.description"></p>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">
                                    <p class="text-sm">Category: <b class="d-block">{{typeof course.category != 'undefined' && course.category != null ? course.category.name : ''}}</b></p>
                                    <p class="text-sm">Sub Category: <b class="d-block">{{typeof course.category != 'undefined' && course.category != null ? course.sub_category.name : ''}}</b></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">
                                    <p class="text-sm">Certificates: <b class="d-block">{{course.certificate_id > 0 ? 'Yes' : 'No'}}</b></p>
                                    <p class="text-sm">Final Quiz: <b class="d-block">{{course.exam_type_id > 0 ? 'Yes' : 'No'}}</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12"></div>
                        </div>
                        <div class="row" v-if="source == 'tutor' || source == 'admin'">
                            <div class="col-12">
                                <div class="card-header bg-success">
                                    <h3 class="card-title">Course Lessons</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-sm bg-dark" @click="addLesson(course)"><i class="fas fa-book mr-1"></i>Add Lesson</button>
                                    </div>
                                </div>
                                <div class="card-body table-responsive p-0" style="height: 300px;">
                                    <LearnDetailLessonList :lessons.sync="course.lessons" :source="source"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h3 class="card-title">Tutors</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-sm btn-primary" @click="addTutors(course)"><i class="fas fa-chalkboard-teacher mr-1"></i>Add Tutors</button>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0" style="height: 300px;">
                                        <LearnDetailAssignTutor :course_tutors.sync="assigned_tutors" />    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h3 class="card-title">Assigned To:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-sm btn-primary" @click="addUsers(course)"><i class="fas fa-users mr-1"></i>Add Users</button>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0" style="height: 300px;">
                                        <LearnDetailAssignUser :course_users.sync="assigned_users" />    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            editMode: false,
            form: new Form({}),
            lessons: {},   
        }
    },
    methods:{
        addAssignees(course){
            this.editMode = false;
            this.course = course;
            Fire.$emit('AssignUserDataFill', this.course);
            $('#assigneeModal').modal('show');
            this.$Progress.finish();
        },
        addTutors(){
            this.editMode = false;
            this.course = course;
            Fire.$emit('assignLoad', this.course);
            //Fire.$emit('CourseDataFill', {});
            $('#tutorModal').modal('show');
            this.$Progress.finish();
        },
        courseReload(response){
            this.categories         = response.data.categories;
            this.course             = response.data.course;
            this.departments        = response.data.departments;
            this.exam_types         = response.data.exam_types;
            this.lessons            = response.data.course.lessons;
            this.tutors             = response.data.tutors;
            this.users              = response.data.users;
            this.assignees          = this.course.assignees
            //this.certificate_types  = response.data.certificate_types;
            Fire.$emit('CourseRefresh', this.course);
            Fire.$emit('AssignUsers', this.course.assignees);
            Fire.$emit('LecturerFill', this.users);
        },
        editCourse(course){
            this.editMode = true;
            this.course = course;
            //Fire.$emit('CourseDataFill', course);
            $('#courseModal').modal('show');
        },
        getAllInitials(){
            //this.$Progress.start();
            axios.get('/api/lms/courses/'+this.$route.params.id)
            .then(response =>{
                this.courseReload(response);
                //this.$Progress.finish();
                this.$toast.fire({icon: 'success', title: 'Course was loaded successfully',});
            })
            .catch(()=>{
                //this.$Progress.fail();
                this.$toast.fire({icon: 'error', title: 'Course was not loaded successfully',})
            });
        },
    },
    mounted() {
        //this.getAllInitials();       
    },
    props:{
        course: Object,
        source: String,
    }
}
</script>