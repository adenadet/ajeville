<template>
<div class="row clearfix">
    <div class="modal fade" id="courseModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Edit Course: '+course.name : 'New Course'}}</h4>
                    <button @click="closeModals" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LearnFormCourse :categories="categories" :course="course" :sub_categories="sub_categories" :editMode="editMode" :exam_types="exam_types" :certificate_types="certificate_types" :tutors="users" />
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
                <LearnDetailCourse :course.sync="course" source="student" />
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
    }
}
</script>