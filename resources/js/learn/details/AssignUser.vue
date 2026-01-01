<template>
<section class="overlay-wrapper">
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
                    <LmsFormAssignUser aspire="u_course" :course="course" :departments="departments" :users="users"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date</th>
                <th>Status</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="assignee in assignees">
                <td>{{ FullName(assignee.user) }}</td>
                <td>{{ FullName(assignee.user) }}</td>
            </tr>
        </tbody>
    </table>
</section>
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
        addAssignees(){
            this.editMode = false;
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
        },
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        assignees: Array,
        course: Object,
    }
}
</script>