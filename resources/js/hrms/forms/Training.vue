<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="exampleInputEmail1">Training</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Training Name">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Institution</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Institution Name">
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="exampleInputPassword1" placeholder="Issue Date">
                </div>
                <div class="form-group">
                    <label for="exampleInputFile">Certificate</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <span class="input-group-text" id="">Upload</span>
                        </div>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                </div>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            trainingData : new Form({
                id: null,
                name: '',
                institution: '',
                date: '',
                certificate: null,
            }),
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