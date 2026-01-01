<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="card-body">
            <strong><i class="fas fa-book mr-1"></i> Training</strong>
            <p class="text-muted">{{ training.name }}</p>
            <hr>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Institution</strong>
            <p class="text-muted">{{ training.institution }}</p>
            <hr>
            <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

            <p class="text-muted">
                <span class="tag tag-danger">UI Design</span>
                <span class="tag tag-success">Coding</span>
                <span class="tag tag-info">Javascript</span>
                <span class="tag tag-warning">PHP</span>
                <span class="tag tag-primary">Node.js</span>
            </p>
            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            leave_request: {},
            loading: false,
        }
    },
    emits: ['refreshTrainingPage'],
    mounted() {},
    methods: {

    },
    props: {
        leave_request: Number,
        source: String,
    },
}
</script>