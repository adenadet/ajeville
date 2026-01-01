<template>
    <div class="modal fade" id="tutorModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Tutors</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!--<LmsFormAssignTutor />-->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            loading: false,
        }
    },
    methods:{
        addTutors(){
            this.editMode = false;
            this.loading = true;
            $('#tutorModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {     
    },
    props:{
        course: Object,
        tutors: Array,
    }
}
</script>