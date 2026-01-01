<template>
<div class="row clearfix">
    <div class="modal fade" id="courseModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Course: {{course.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Course</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LearnFormCourse :course="course" :editMode="editMode" />
                </div>
            </div>
        </div>
    </div>    
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{course.name}} Detail</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-default" title="Edit Course" @click="editCourse(course)"><i class="fas fa-edit"></i></button>
                    <button type="button" class="btn btn-sm btn-primary" title="Assign Tutors" @click="addTutors(course.id)"><i class="fas fa-chalkboard-teacher"></i></button>
                    <button type="button" class="btn btn-sm btn-success" title="Assign new User" @click="addAssignees(course.id)"><i class="fas fa-user-plus"></i></button>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <div class="small-box bg-light">
                                    <div class="inner"><h3>{{ typeof course.tutors != 'undefined' && course.tutors != null && course.tutors.length != 0 ? course.tutors.length : 0}}</h3><p>Tutors </p></div>
                                    <div class="icon"><i class="fa fa-chalkboard-teacher"></i></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="small-box bg-light">
                                    <div class="inner"><h3>{{ course.price | currency}}</h3><p>Estimated Cost</p></div>
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
                                <h3 class="text-primary"><i class="fas fa-book"></i> {{course.name}}</h3>
                                <p class="text-muted">{{course.description}}</p>
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
                            <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h3 class="card-title">Tutors</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-sm btn-primary" @click="addTutors(course)"><i class="fas fa-chalkboard-teacher"></i>Add Tutors</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <LearnDetailAssignUsers />
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <LearnDetailAssignTutor />
                            </div>
                        </div>
                        <div class="row">
                            <TutorLessons :lessons="course.lessons" />
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
            assignees: {},
            categories:[],
            certificate_types:[],
            course:{},
            departments: [],
            editMode: false,
            exam_types: [],
            form: new Form({}),
            lessons: {},
            sub_categories:[],
            tutors: [],
            users:[],   
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
            this.$Progress.start();
            this.editMode = true;
            this.course = course;
            Fire.$emit('CourseDataFill', course);
            $('#courseModal').modal('show');
            this.$Progress.finish();
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/lms/courses/'+this.$route.params.id)
            .then(response =>{
                this.courseReload(response);
                this.$Progress.finish();
                toast.fire({icon: 'success', title: 'Course was loaded successfully',});
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Course was not loaded successfully',})
            });
        },
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('CourseUpdate', course=>{
            this.course = course;
        });
        Fire.$on('reload', response =>{
            this.courseReload(response);
            $('#assigneeModal').modal('hide');
            $('#courseModal').modal('hide');
            $('#tutorModal').modal('hide');
        });        
        Fire.$on('CourseRefresh', course =>{
            //this.courseReload(response);
            this.course = course;
            $('#assigneeModal').modal('hide');
            $('#courseModal').modal('hide');
            $('#tutorModal').modal('hide');
        });        
    }
}
</script>