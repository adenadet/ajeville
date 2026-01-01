<template>
    <div class="row clearfix overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="courseFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h4 class="modal-title" v-show="editMode">Update Course Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <LearnFormCourse :course="course" :editMode="editMode" @refreshCourseForm="refreshPage" />
                    </div>
                </div>
            </div>
        </div>
        <table class="table m-b-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="course in courses" :key="course.id">
                    <td>{{course.name}}</td>
                    <td :title="course.description">{{readMore(course.description, 25, '...')}}</td>
                    <td>{{course.category_id !== null && course.category != null ? course.category.name : ''}}</td>
                    <td>{{course.sub_category_id !== null  && course.sub_category != null? course.sub_category.name: ''}}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu" v-if="source == 'admin'">
                            <router-link class="btn btn-block dropdown-item" :to="'/learn/admin/courses/'+course.id"><i class="fa fa-eye mr-1"></i> View </router-link>
                            <button class="btn btn-block dropdown-item" @click="editCourse(course)"><i class="fa fa-edit mr-1 text-primary"></i> Update Course</button>
                        </div>
                        <div class="dropdown-menu" v-if="source == 'student'">
                            <router-link class="btn btn-block dropdown-item" :to="'/learn/student/courses/'+course.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                            <button class="btn btn-block dropdown-item" @click="unregisterCourse()"><i class="fa fa-list mr-1 text-danger"></i> Delete Loan Request</button>
                        </div>
                        <div class="dropdown-menu" v-if="source == 'tutor'">
                            <router-link :to="'/learn/admin_area/course/'+course.id" class="btn btn-block dropdown-item"><i class="fa fa-eye text-primary"></i> View</router-link>
                            <button class="btn btn-block dropdown-item" @click="deactivateCourse(course.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Course</button>
                        </div>          
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data(){
        return {
            course:{},
            courses:{},
            editMode: false,
            form: new Form({}),
            loading: false,   
        }
    },
    emits: ['refreshCourseList'],
    methods:{
        addCourse(){
            this.loading = true;
            this.editMode = false;
            this.course = {};
            Fire.$emit('CourseDataFill', {});
            $('#courseModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#courseFormModal').modal('hide');
        },
        deactivateCourse(id){
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
                    this.form.delete('/api/lms/courses/'+id)
                    .then(response=>{
                    this.$swal.fire('Done!', 'Course has been deactivated/reactivated.', 'success');
                    this.$emit('refreshCourseList');   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        deleteCourse(id){
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
                    this.form.delete('/api/learn/courses/'+id)
                    .then(()=>{
                        this.$emit('refreshCourseList');   
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editCourse(course){
            this.loading = true;
            this.editMode = true;
            this.course = course;
            $('#courseFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
            this.$emit('refreshCourseList');
        },
        seeCourse(course){
            this.$Progress.start();
            this.course = course;
            //Fire.$emit('CourseRefresh', course)
            Fire.$emit('AssignUsers', course.assignees);
            Fire.$emit('CourseRefresh', this.course);
            this.$Progress.finish();
        },
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        courses: Array,
        source: String,
    },
}
</script>