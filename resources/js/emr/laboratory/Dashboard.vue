<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box text-primary">
                        <div class="inner">
                            <h3>{{ pending.length }}</h3>
                            <p>New Requests</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-primary">
                        <div class="inner">
                            <h3>{{ pending_new.length }}</h3>
                            <p>Requests Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-danger">
                        <div class="inner"><h3>{{ emergency.length }}</h3><p>Emergency Requests</p></div>
                        <div class="icon"><i class="fa fa-first-aid text-danger"></i></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-success">
                        <div class="inner"><h3>{{ completed.length }}</h3><p>Completed Requests</p></div>
                        <div class="icon"><i class="fa fa-file-pdf text-success"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-success">
                        <div class="inner"><h3>{{ completed_referred_in.length }}</h3><p>Completed Referred In </p></div>
                        <div class="icon"><i class="fa fa-indent text-success"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-success">
                        <div class="inner"><h3>{{ completed_referred_out.length }}</h3><p>Completed Referred Out</p></div>
                        <div class="icon"><i class="fa fa-outdent  text-success"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ unapproved.length }}</h3><p>Unapproved Requests</p></div>
                        <div class="icon"><i class="fa fa-file text-info"></i></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ completed.length }}</h3><p>Awaiting Samples</p></div>
                        <div class="icon"><i class="fa fa-vial text-info"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ completed_referred_in.length }}</h3><p>Awaiting Result </p></div>
                        <div class="icon"><i class="fa fa-file text-info"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ completed_referred_out.length }}</h3><p>Awaiting Secondary Result</p></div>
                        <div class="icon"><i class="fa fa-file-pdf  text-info"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ unapproved.length }}</h3><p>Awaiting Confirmation</p></div>
                        <div class="icon"><i class="fa fa-certificate text-info"></i></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-warning">
                        <div class="inner"><h3>{{ pending_referred_in.length }}</h3><p>Pending Referred In </p></div>
                        <div class="icon"><i class="fa fas fa-indent text-warning"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-warning">
                        <div class="inner"><h3>{{ pending_referred_out.length }}</h3><p>Pending Referred Out</p></div>
                        <div class="icon"><i class="fas fa-outdent text-warning"></i></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h3 class="card-title">Pending Requests</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <EMRLaboratoryDetailRequestList source="laboratory" :requests="pending" />
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h3 class="card-title">Emergency Requests</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <EMRLaboratoryDetailRequestList source="laboratory" :requests="emergency" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRLaboratoryDetailRequestList from '@/emr/laboratory/details/RequestList.vue'
export default {
    components:{EMRLaboratoryDetailRequestList},
    data() {
        return {
            cancelled: [],
            completed: [],
            completed_referred_in: [],
            completed_referred_out: [],
            emergency: [],
            loading: false,
            pending: [],
            pending_new: [],
            pending_referred_in: [],
            pending_referred_out: [],
            pendings: [],
            transaction: [],
            transactions: [],
            unpaid: [],
            unapproved: [],
            editMode: true,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page=1) {
            this.loading = true;
            axios.get('/api/emr/laboratory/dashboard?page='+page)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',});
            })
            .finally(() => {
                this.loading = false;
            });
        },
        refreshDashboard(response) {
            this.cancelled = response.data.cancelled;
            this.completed = response.data.completed;
            this.completed_referred_in = response.data.completed_referred_in;
            this.completed_referred_out = response.data.completed_referred_out;
            this.emergency = response.data.emergency;
            this.pending = response.data.pending;
            this.pending_new = response.data.pending_new;
            this.pending_referred_in = response.data.pending_referred_in;
            this.pending_referred_out = response.data.pending_referred_out;
            this.unpaid = response.data.unpaid;
            this.unapproved = response.data.unapproved;
        }
    },
}
</script>