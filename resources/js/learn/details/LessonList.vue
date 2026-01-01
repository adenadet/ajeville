<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="lessonFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Lesson: {{lesson.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Lesson</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LmsFormLesson :lesson="lesson" :course="course" :editMode="editMode" />
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed table-striped table-hover text-nowrap">
        <thead>
            <tr>
                <th>Lesson Name</th>
                <th>Lesson Type</th>
                <th>Content</th>
                <th>Serial Number</th>
                <th>Created</th>
                <th>Last Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(lesson, index) in lessons">
                <td>{{ lesson.name }}</td>
                <td>{{ lesson.type != null ? lesson.type.name : 'None Selected' }}</td>
                <td :title="lesson.content" v-html=" readMore(lesson.content, 70, '...')"></td>
                <td>{{ lesson.serial_number ?? addOne(index) }}</td>
                <td>{{ FullName(lesson.creator) }} <br /><span class="text-muted" v-html="ExcelDate(lesson.created_at)"></span></td>
                <td>{{ FullName(lesson.updater) }} <br /><span class="text-muted" v-html="ExcelDate(lesson.updated_at)"></span></td>
                <td>
                    <button type="button" class="btn btn-sm btn-tool text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu" v-if="source == 'admin'">
                        <router-link :to="'/learn/admin/lessons/'+lesson.id" class="btn btn-block dropdown-item"><i class="fa fa-eye text-primary"></i> View</router-link>
                        <button class="btn btn-block dropdown-item" @click="deactivateLesson(lesson.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Lesson</button>
                    </div>
                    <div class="dropdown-menu" v-if="source == 'student'">
                        <router-link class="btn btn-block dropdown-item" :to="'/learn/student/lessons/'+lesson.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                        <button class="btn btn-block dropdown-item" @click="unregisterCourse()"><i class="fa fa-list mr-1 text-danger"></i> Delete Loan Request</button>
                    </div>
                    <div class="dropdown-menu" v-if="source == 'tutor'">
                        <router-link :to="'/learn/tutor/lesson/'+lesson.id" class="btn btn-block dropdown-item"><i class="fa fa-eye text-primary"></i> View</router-link>
                        <button class="btn btn-block dropdown-item" @click="deactivateCourse(lesson.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Lesson</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <!--div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lesson Details</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" title="Collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" title="Remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <h3 class="text-primary">Student's View</h3>
                        <StudentLessonDetail :lesson="lesson" />
                    </div>
                    <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                        <h3 class="text-primary"><i class="fas fa-paint-brush"></i> Reports</h3>
                        <p class="text-muted"></p>
                        <br>
                        <div class="text-muted">
                            <p class="text-sm">Created By: <b class="d-block">{{lesson.creator.first_name+' '+lesson.creator.last_name}}</b></p>
                            <p class="text-sm">Created On: <b class="d-block">{{lesson.created_at | ExcelDate}}</b></p>
                            <p class="text-sm">Exam: <b class="d-block">{{((lesson.exam !== null)&&(typeof (lesson.exam) !== 'undefined' )) ? 'Yes' : 'No'}}</b></p>
                        </div>
                        <h5 class="mt-5 text-muted">Lesson files</h5>
                        <ul class="list-unstyled">
                            <li v-show="lesson.video !== null"><a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-video"></i> Functional-requirements.docx</a></li>
                            <li v-show="lesson.file !== null"><a target="_blank" :href="lesson.file" class="btn-link text-secondary"><i class="far fa-fw fa-file-pdf"></i> </a></li>
                        </ul>
                        <div class="text-center mt-5 mb-3">
                            <a href="#" class="btn btn-sm btn-primary">Add files</a>
                            <button class="btn btn-sm btn-warning" @click="editLesson">Edit Module</button>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div-->
    </section>
</template>
<script>
export default {
    data(){
        return {
            categories:[],
            certificate_types:[],
            course:{},
            courses:{},
            editMode: false,
            exam_types: [],
            form: new Form({}),
            lesson: {},
            lessons: {},
            loading: false,
            sub_categories:[],
            users:[],
        }
    },
    methods:{
        editLesson(){
            this.loading = true;
            this.editMode = true;
            $('#lessonModal').modal('show');
            this.loading = false
        },
        editCourse(course){
            this.loading = true;
            this.editMode = true;
            this.course = course;
            Fire.$emit('CourseDataFill', course);
            $('#courseModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/learn/lessons/'+this.$route.params.id)
            .then(response =>{
                this.lesson = response.data.lesson;
                toast.fire({
                    icon: 'success',
                    title: 'Lesson was loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Lesson was not loaded successfully',
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
        seeCourse(course){
            this.loading = true;
            this.course = course;
            this.loading = false
        },
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        lessons: Array,
        source: String,
    },
    watch:{

    }
}
</script>
