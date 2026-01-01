<template>
<section class="col-md-12">
    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">My Courses</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" v-model="query" placeholder="Search">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default" @click="getAllInitials()"><i class="fas fa-search"></i></button>
                        <button type="button" class="btn btn-primary ml-1"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 500px;">
            <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <LearnDetailUserCourseList source="student" :user_courses.data="user_courses.data" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="user_courses.per_page != null ? user_courses.per_page : 52" :records="user_courses.total != null ? user_courses.total : 550"></pagination>
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
            user_course: {},
            user_courses: {data:[], total: 0},
        }
    },
    methods:{
        editExam(exam){
            this.Exam = exam;
            this.editMode = true;
            this.$emit('ExamDataFill', exam);
            //Fire.$emit('editexam', exam);    
            $('#examModal').modal('show');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/learn/user_courses?type=student&query='+this.query)
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
        }
    },
    mounted(){ 
        this.getAllInitials();
    }
}
</script>