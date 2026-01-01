<template>
<section>
    <div class="card card-primary">
        <div class="card-header"><h3>{{ assessment_type.name }}</h3></div>
        <div class="card-body">
            <div class="row">
                <div class="col-4">Name:<br /><b>{{ assessment_type.name }}</b></div>
                <div class="col-8">Description:<br /><span class="b-1" v-html="assessment_type.description"></span></div>
                <div class="col-12">Individual Assessments Items <br />
                    <ul><li v-for="item in assessment_type.assessments">{{item.name}}</li></ul>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    methods: {
        addType(request){
            this.$Progress.start();
            this.request = request;
            this.editMode = false;
            Fire.$emit('requestDataFill', {});
            $('#requestModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            axios.get('/api/emr/assessments/types?page='+page)
            .then(response=>{
                this.refreshAssessmentTypes(response); 
            });
        },
    },
    props: {
        assessment_type: Object,
        items: Array,
    }
}
</script>