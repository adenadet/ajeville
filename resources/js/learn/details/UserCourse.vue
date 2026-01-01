<template>
<section class="overlay-wrapper">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-7 order-2 order-md-1">      
            <div class="row" v-if="course.lessons > 0">
                <div class="card col-lg-4 col-sm-6 col-md-6 d-flex align-items-stretch" v-for="(lesson, index) in course.lessons" :key="index">
                    <div class="card-header text-muted border-bottom-0"><h2 class="lead" :title="lesson.name">{{readMore(lesson.name, 20, '...')}}</h2></div>
                    <div class="card-body pt-0" style="height:100px;">
                        <div class="row">
                            <div class="col-9"><p class="text-muted text-sm">{{ readMore(lesson.content, 70, '...')}}</p></div>
                            <div class="col-3 text-center"><img src="" alt="" class="img-circle img-fluid"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right" v-if="index > user_course.level">
                            <button title="Finish The Previous Lesson" class="btn btn-sm btn-danger" @click="prevent"><i class="fa fa-lock"></i> Locked</button>
                        </div>
                        <div class="text-right" v-else-if="index == 0 && user_course.level == null">
                            <button @click="startCourse(lesson.id)" title="Read Course" class="btn btn-sm btn-success"><i class="fa fa-play"></i> Start Course</button>
                        </div>
                        <div class="text-right" v-else>
                            <a v-if="index == course.level" :href="'/student_area/lesson/'+lesson.id" title="Read Course" class="btn btn-sm btn-success"><i class="fa fa-play"></i> Continue</a>
                            <a v-else :href="'/learn/student_area/lesson/'+lesson.id" title="Repeat Course" class="btn btn-sm btn-success"><i class="fa fa-circle-o"></i> Read Again</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-else>
                <div class="col-lg-12"><div class="card"><div class="card-body" style="min-height: 400px;">No Lesson</div></div></div>
            </div>
        </div>

        <div class="col-12 col-md-12 col-lg-5 order-1 order-md-2">
            <LearnDetailCourse :course.sync="course" :source="source" />
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            course: {name: 'Test', lessons: []},
            editMode: false,
            form: new Form({}),
            loading: false,
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
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        source: String,
        user_course: Object,
    },
    watch:{
        user_course(){
            this.loading = true;
            this.course = this.user_course.course;
            this.loading = false;
        }
    }
}
</script>