<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>    
    <div class="row">
        <div class="col-5 col-sm-3">
            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                <a v-for="precheck in prechecks" :key="precheck.id" class="nav-link" :id="precheck.code+'-tab'" data-toggle="pill" :href="'#'+precheck.code" role="tab" :aria-controls="precheck.code" aria-selected="true">{{precheck.name}}</a>
            </div>
        </div>
        <div class="col-7 col-sm-9">
            <div class="tab-content" id="vert-tabs-tabContent">
                <div class="tab-pane fade" v-for="precheck in prechecks" :key="precheck.id" :id="precheck.code" role="tabpanel" :aria-labelledby="precheck.code+'-tab'">
                    {{ precheck }}
                </div>
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
        prechecks: Array,
    }
}
</script>