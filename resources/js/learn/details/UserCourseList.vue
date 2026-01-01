<template>
<section class="overlay-wrapper p-0">
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Course Name</th>
                <th v-if="source == 'admin'">Assigned To</th>
                <th>Start Date</th>
                <th>Expiry Date</th>
                <th>Assigned Date</th>
                <th>Level</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody v-if="user_courses.length >= 1">
            <tr v-for="user_course in user_courses">
                <td>{{ user_course.course != null ? user_course.course.name : 'Deleted Course'}}</td>
                <td v-if="source == 'admin'">{{ FullName(user_course.user) }}</td>
                <td>{{ ExcelDate(user_course.start_date) }}</td>
                <td>{{ ExcelDate(user_course.expiry_date) }}</td>
                <td>{{ ExcelDate(user_course.assigned_date) }}</td>
                <td>{{ user_course.level }}</td>
                <td>
                    <span v-if="user_course.status == 10" class="badge badge-success">Completed</span>
                    <span v-else-if="user_course.status < 10" class="badge badge-primary" >Ongoing</span>
                    <span v-else-if="user_course.status == 100" class="badge badge-warning">Queried</span>
                    <span v-else class="badge badge-danger">Expired</span>
                </td>
                <td>
                    <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu" v-if="source == 'admin'">
                        <router-link :to="'/learn/admin/user_courses/'+user_course.id" class="btn btn-block dropdown-item" ><i class="fa fa-eye mr-1"></i> View User Course</router-link>
                        <button class="btn btn-block dropdown-item" @click="cancelUserCourse(user_course.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel User Course </button>
                    </div>
                    <div class="dropdown-menu" v-if="source == 'mine'">
                        <router-link :to="'/learn/student/user_courses/'+user_course.id" class="btn btn-block dropdown-item" ><i class="fa fa-book mr-1"></i> Start Course</router-link>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="8">No User Course has been assigned</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            courses: [],
            Exam:{},
            exam: {},
            exams:{},
            exam_types: [],
            question_types:[],
            editMode: false,
            form: new Form({}),
        }
    },
    emits:['refreshUserCourseList'],
    methods:{
        addExam(exam){
            this.editMode = false;
            Fire.$emit('ExamDataFill', exam);    
            $('#examModal').modal('show');
        },
        cancelUserCourse(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/learn/user_courses/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'User Course has been deactivated.', 'success');
                        this.$emit('refreshUserCourseList');
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        examLoad(exam){
            this.exam = exam;
            Fire.$emit('examLoad', exam);
        },
        deleteExam(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/lms/exams/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Exam has been deleted.', 'success');
                        Fire.$emit('refresh', response);
                        this.refresh(response);   
                        })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                        });
                    }
                });  
            },
        editExam(exam){
            this.Exam = exam;
            this.editMode = true;
            Fire.$emit('ExamDataFill', exam);
            //Fire.$emit('editexam', exam);    
            $('#examModal').modal('show');
        },
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        source: String,
        user_courses: Array,
    }
}
</script>