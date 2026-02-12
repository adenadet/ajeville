<template>
<section class="">
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Admission Details</h3></div>
    <div class="card-body">
        <strong><i class="fas fa-book mr-1"></i> Patient</strong>
        <p class="text-muted">{{ patientName(request?.patient) }}</p>

        <hr>
        <strong><i class="fas fa-map-marker-alt mr-1"></i> Requested By</strong>
        <p class="text-muted">{{ FullName(request?.creator) }}</p>

        <hr>

        <strong><i class="fas fa-pencil-alt mr-1"></i> Pre Admission Checks</strong>

        <p class="text-muted" v-for="precheck in request?.prechecks" :key="precheck.id">
            <span class="tag tag-danger">UI Design</span>
            <span class="tag tag-success">Coding</span>
            <span class="tag tag-info">Javascript</span>
            <span class="tag tag-warning">PHP</span>
            <span class="tag tag-primary">Node.js</span>
        </p>

        <hr>

        <strong><i class="far fa-file-alt mr-1"></i> Clinical Notes</strong>

        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
        </div>
        <!-- /.card-body -->
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
        }
    },
    emits:['refreshRequestDetail'],
    methods: {
        
    },
    mounted() {
        
    },
    props:{
        request: Object,
    }
}
</script>