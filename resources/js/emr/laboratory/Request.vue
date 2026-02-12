<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <LaboratoryDetailSummary :request="request" :show_status="editMode" :print_label="!editMode"/>
            </div>
            <div class="col-md-8 card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activity</h4>
                </div>
                <div class="card-body">
                    <div class="post">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" :src="request.creator | profilePicture" alt="user image">
                            <span class="username"><a href="#">{{ request.creator | FullName }}</a></span>
                            <span class="description">Requested By - {{ request.created_at | excelTimestamp }}
                                {{request.creator | profilePicture}}</span>
                        </div>
                        <p>{{request.create_remark != null ? request.create_remark : 'No Special Remark Added'}}</p>
                    </div>
                    <div class="post">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" :src="request.creator | profilePicture" alt="user image">
                            <span class="username"><a href="#">{{ request.creator | FullName }}</a></span>
                            <span class="description">Requested By - {{ request.created_at | excelTimestamp }}
                                {{request.creator | profilePicture}}</span>
                        </div>
                        <p>{{request.create_remark != null ? request.create_remark : 'No Special Remark Added'}}</p>
                    </div>
                    <div class="post clearfix" v-if="request.status >= 2">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="dist/img/user1-128x128.jpg" alt="user image">
                            <span class="username"><a href="#">{{ request.reporter | FullName }}</a></span>
                            <span class="description">Collected sample at {{ request.reported_at | excelTimestamp }}</span>
                        </div>
                     
                        <p>{{request.sample_remark != null ? request.sample_remark : 'No Special Remark Added'}}</p>
                    </div>
                    <div class="post clearfix" v-if="request.status >= 3">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="dist/img/user7-128x128.jpg" alt="User Image">
                            <span class="username"><a href="#">{{ request.patient | FullName }}</a></span>
                            <span class="description">Result Entered at {{ request.sample_collected_at | excelTimestamp }}</span>
                        </div>
                        <p>{{request.report_remark != null ? request.report_remark : 'No Special Remark Added'}}</p>
                        <p>
                            <button href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> View Report</button>
                        </p>
                    </div>
                    <div class="post clearfix" v-if="request.secondary_report_by != null">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="dist/img/user1-128x128.jpg" alt="user image">
                            <span class="username"><a href="#">{{ request.secondary_reporter | FullName }}</a></span>
                            <span class="description">Approved Result at {{ request.secondary_report_at | excelTimestamp }}</span>
                        </div>
                        <p>{{request.secondary_report_remark != null ? request.secondary_report_remark : 'No Special Remark Added'}}</p>
                        <p>
                            <button href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> View Report</button>
                        </p>
                    </div>
                    <div class="post clearfix" v-if="request.status >= 5">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="dist/img/user1-128x128.jpg" alt="user image">
                            <span class="username">
                            <a href="#">{{ request.approver | FullName }}</a>
                            </span>
                            <span class="description">Approved Result at {{ request.approved_at | excelTimestamp }}</span>
                        </div>
                        <p>{{request.approval_remark != null ? request.approval_remark : 'No Special Remark Added'}}</p>
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
            editMode: true,
            request: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
            Fire.$emit('ApplicantDataFill', {});
            $('#applicantModal').modal('show');
            this.$Progress.finish();
        },
        addAppointment(){
            this.$Progress.start();
            this.editMode = false;
            this.appointment = {};
            Fire.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/requests/'+this.$route.params.id)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },

        refreshDashboard(response) {
            this.request = response.data.request;
        }
    },
    props: {}
}
</script>