<template>
<section class="content p-0 m-0">
    <div class="card">
        <div class="card-body overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Leave Type</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{ leave_request.leave_type != null ? leave_request.leave_type.name : 'Invalid Leave Type' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Amount of Days</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{ timeDifference(leave_request.from_date, leave_request.to_date, 
                                    'days', 
                                    (leave_request != null) && (leave_request.leave_type != null) && (leave_request.leave_type.leave_category != null) ? leave_request.leave_type.leave_category : 'Calendar') }}  
                                    ({{leave_request.from_date}} - {{leave_request.to_date}})
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Status</span>
                                    <span class="info-box-number text-center text-muted mb-0"> 
                                        {{leave_request.status == 0 ? 'Unapproved' : 
                                        (leave_request.status == 1 ? 'Approved' : 
                                        (leave_request.status == 2 ? (dateGreaterThanToday(leave_request.to_date) ? 'Ongoing' : 'Approved') :
                                        (leave_request.status == 3 ? 'Completed ': 'Rejected')))}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h4>Recent Activity</h4>
                            <div class="timeline p-0">
                                <div>
                                    <i class="fas fa-envelope bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time text-white"><i class="fas fa-clock"></i> {{ shortDate(leave_request.created_at) }}</span>
                                        <h3 class="timeline-header bg-blue"><a href="#" class="text-white">{{leave_request.employee != null ? FullName(leave_request.employee.user) : 'Old Staff' }}</a> requested a leave</h3>

                                        <div class="timeline-body">
                                            <p class="text-justify"><strong>Reason:</strong>{{ leave_request.reason }}</p>
                                            <p class="text-justify"><strong>Details:</strong><br />{{ leave_request.remarks }}</p>
                                        </div>
                                        <div class="timeline-footer" v-if="source == 'self'">
                                            <a class="btn btn-primary btn-sm">Read more</a>
                                            <a class="btn btn-danger btn-sm">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="leave_request.approved_by != null">
                                    <i class="fas fa-user bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ ExcelDate(leave_request.approved_at) }}</span>
                                        <h3 class="timeline-header no-border"><a href="#">{{leave_request.approver != null ? FullName(leave_request.approver.user): 'No Approver Found'}}</a> approved the leave request</h3>
                                        <div class="timeline-body">
                                            <p class="text-justify"><strong>Details:</strong>
                                                <span v-html="leave_request.approval_remarks"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
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
            leave_request: {},
            loading: false,
        }
    },
    mounted() {},
    methods: {
        getInitials(leave_request_id) {
            this.loading = true;
            axios.get('/api/hrms/leaves/'+leave_request_id+'?type='+this.source)
            .then(response => {
                this.leave_request = response.data.leave_request;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
    },
    props: {
        leave_request_id: Number,
        source: String,
    },
    watch:{
        leave_request_id(){
            this.getInitials(this.leave_request_id);
        }
    }
}
</script>