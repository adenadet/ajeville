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
                    <LearnFormCourse :course="course" :editMode="editMode" @refreshCourseForm="getAllInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <LearnDetailCourse :course.sync="course" source="admin" />
        <!--div class="card">
            <div class="card-header">
                <h3 class="card-title">{{course.name}} Detail</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-default" title="Edit Course" @click="editCourse(course)"><i class="fas fa-edit"></i></button>
                    <button type="button" class="btn btn-sm btn-primary" title="Assign Tutors" @click="addTutors(course.id)"><i class="fas fa-chalkboard-teacher"></i></button>
                    <button type="button" class="btn btn-sm btn-success" title="Assign new User" @click="addAssignees(course.id)"><i class="fas fa-user-plus"></i></button>
                </div>
            </div>
            
            <div class="card-body overlay-wrapper">
                <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <LearnDetailCourse :course.sync="course" source="admin" />
            </div>
        </div-->  
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
            this.loading = true;
            this.editMode = false;
            this.course = course;
            
            $('#assigneeModal').modal('show');
            this.loading = false;
        },
        addTutors(){
            this.loading = true;
            this.editMode = false;
            this.course = course;
            //Fire.$emit('assignLoad', this.course);
            //Fire.$emit('CourseDataFill', {});
            $('#tutorModal').modal('show');
            this.$Progress.finish();
            this.loading = false;
        },
        courseReload(response){
            this.course             = response.data.course;
        },
        editCourse(){
            this.loading = true;
            this.editMode = true;
            //this.course = course;
            //Fire.$emit('CourseDataFill', course);
            $('#courseModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/learn/courses/'+this.$route.params.id)
            .then(response =>{
                this.courseReload(response);
                this.$toast.fire({icon: 'success', title: 'Course was loaded successfully',});
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Course was not loaded successfully',})
            });
            this.loading = false;
        },
    },
    mounted() {
        this.getAllInitials();        
    }
}
</script>