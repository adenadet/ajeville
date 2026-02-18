<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-4">
            <EMRRadiologyDetailRequest :request.sync="request" />
        </div>
        <div class="col-md-8">
            <div class="timeline">
                <div>
                    <i class="fas fa-user bg-purple"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(request?.created_at)}}</span>
                        <h3 class="timeline-header no-border"><a href="#">{{ FullName(request?.creator) }}</a> requested this service</h3>
                        <div class="timeline-body" v-html="request?.remarks || 'Auto request details'"></div>
                    </div>
                </div>
                <div v-if="request?.sample_at != null">
                    <i class="fas fa-user bg-green"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(request?.sample_at)}}</span>
                        <h3 class="timeline-header no-border"><a href="#">{{ FullName(request?.collector) }}</a> collected the sample</h3>
                        <div class="timeline-body" v-html="request?.sample_remarks"></div>
                    </div>
                </div>
                <div v-if="request?.sample_at != null">
                    <i class="fas fa-user bg-green"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(request?.sample_at)}}</span>
                        <h3 class="timeline-header no-border"><a href="#">{{ FullName(request?.collector) }}</a> collected the sample</h3>
                        <div class="timeline-body" v-html="request?.sample_remarks"></div>
                    </div>
                </div>
                <div v-if="request?.reported_at != null">
                    <i class="fas fa-clipboard bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(request?.reported_at)}}</span>
                        <h3 class="timeline-header"><a href="#">{{ FullName(request?.reporter) }}</a> sent you an email</h3>
                        <div class="timeline-body" v-html="request?.report_remark"></div>
                    </div>
                </div>
                <div v-if="request?.secondary_report_at != null">
                    <i class="fas fa-paste bg-yellow"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(request?.secondary_report_at)}}</span>
                        <h3 class="timeline-header"><a href="#">{{ FullName(request?.secondary_reporter) }}</a> sent you an email</h3>
                        <div class="timeline-body" v-html="request?.secondary_report"></div>
                    </div>
                </div>
                <div v-if="request?.approved_at != null">
                    <i class="fas fa-comments bg-yellow"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{timeAgo(request?.approved_at)}}</span>
                        <h3 class="timeline-header"><a href="#">{{ FullName(request?.approver) }}</a> sent you an email</h3>
                        <div class="timeline-body" v-html="request?.approver_remark"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRRadiologyDetailRequest from '@/emr/radiology/details/Request.vue'
export default {
    components:{EMRRadiologyDetailRequest},
    data() {
        return {
            editMode: true,
            loading: false,
            request: {},
        }
    },
    emits: ['radiologyRequestsRefresh'],
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            //this.loading = true;
            axios.get('/api/emr/radiology/requests/'+this.$route.params.id)
            .then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Requests did not loaded successfully',});
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        refreshPage(response) {
            this.request = response.data.request;
        },
    },
}
</script>