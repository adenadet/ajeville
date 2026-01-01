<template>
<section class="overlay-wrapper p-0">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">CPU Traffic</span>
                    <span class="info-box-number">
                    10
                    <small>%</small>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Likes</span>
                    <span class="info-box-number">41,410</span>
                </div>
            </div>
        </div>
        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Sales</span>
                    <span class="info-box-number">760</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">New Members</span>
                    <span class="info-box-number">2,000</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Assigned Courses</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height:300px;">
                    <LearnDetailUserCourseList source="admin" :user_courses.sync="user_courses.data" @refreshUserCourseList="getAllInitials" />
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Assigned Exams</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height:300px;">
                    <LearnDetailUserExamList :user_exams.sync="user_exams.data" @refreshUserCourseList="getAllInitials" />
                </div>
            </div>
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
            loading: false,
            user_courses: {data:[], total: 0},
            user_exams: {data:[], total: 0},
        }
    },
    methods:{
        addExam(exam){
            this.editMode = false;
            Fire.$emit('ExamDataFill', exam);    
            $('#examModal').modal('show');
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
        getAllInitials(){
            this.loading = true;
            axios.get('/api/learn/dashboard?type=student')
            .then(response =>{
                this.refresh(response);
                this.$toast.fire({icon: 'success', title: 'Learn Dashboard loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Learn Dashboard not loaded successfully',})
            });
            this.loading = false;
        },
        refresh(response){
            this.user_courses = response.data.user_courses;
            //this.user_exams = response.data.user_exams;  
        }
    },
    mounted(){ 
        this.getAllInitials();
    }
}
</script>