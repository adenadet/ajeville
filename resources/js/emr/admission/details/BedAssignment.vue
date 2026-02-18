<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row p-0">
        <div class="col-md-4">
            <div class="form-group">
                <label>Ward</label>
                <div class="form-control" v-html="bed_assignment?.bed?.ward?.name"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Room</label>
                <div class="form-control" v-html="bed_assignment?.bed?.room?.name"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Bed</label>
                <div class="form-control" v-html="bed_assignment?.bed?.name"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Assigned By</label>
                <div class="form-control" v-html="FullName(bed_assignment?.assigner)"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Assigned At</label>
                <div class="form-control" v-html="ExcelDate(bed_assignment?.assigned_at)"></div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
        }
    },
    emits:['refreshRequestDetail'],
    methods: {
        
    },
    mounted() {
        
    },
    props:{
        bed_assignment: Object,
    }
}
</script>