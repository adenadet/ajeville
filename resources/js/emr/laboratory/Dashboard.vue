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
                        <a href="/laboratory/requests/new" class="small-box-footer text-primary">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                        <a href="/laboratory/requests/today" class="small-box-footer text-primary">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-danger">
                        <div class="inner"><h3>{{ emergency.length }}</h3><p>Emergency Requests</p></div>
                        <div class="icon"><i class="fa fa-first-aid text-danger"></i></div>
                        <a href="/laboratory/requests/emergency" class="small-box-footer  text-danger">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-success">
                        <div class="inner"><h3>{{ completed.length }}</h3><p>Completed Requests</p></div>
                        <div class="icon"><i class="fa fa-file-pdf text-success"></i></div>
                        <a href="/laboratory/requests/completed" class="small-box-footer text-success">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-success">
                        <div class="inner"><h3>{{ completed_referred_in.length }}</h3><p>Completed Referred In </p></div>
                        <div class="icon"><i class="fa fa-indent text-success"></i></div>
                        <a href="/laboratory/requests/referred_in_completed" class="small-box-footer text-success">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-success">
                        <div class="inner"><h3>{{ completed_referred_out.length }}</h3><p>Completed Referred Out</p></div>
                        <div class="icon"><i class="fa fa-outdent  text-success"></i></div>
                        <a href="/laboratory/requests/referred_out_completed" class="small-box-footer text-success">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ unapproved.length }}</h3><p>Unapproved Requests</p></div>
                        <div class="icon"><i class="fa fa-file text-info"></i></div>
                        <a href="/laboratory/requests/unapproved" class="small-box-footer text-info">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ completed.length }}</h3><p>Awaiting Samples</p></div>
                        <div class="icon"><i class="fa fa-vial text-info"></i></div>
                        <a href="/laboratory/requests/completed" class="small-box-footer text-info">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ completed_referred_in.length }}</h3><p>Awaiting Result </p></div>
                        <div class="icon"><i class="fa fa-file text-info"></i></div>
                        <a href="/laboratory/requests/referred_in_completed" class="small-box-footer text-info">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ completed_referred_out.length }}</h3><p>Awaiting Secondary Result</p></div>
                        <div class="icon"><i class="fa fa-file-pdf  text-info"></i></div>
                        <a href="/laboratory/requests/referred_out_completed" class="small-box-footer text-info">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-info">
                        <div class="inner"><h3>{{ unapproved.length }}</h3><p>Awaiting Confirmation</p></div>
                        <div class="icon"><i class="fa fa-certificate text-info"></i></div>
                        <a href="/laboratory/requests/unapproved" class="small-box-footer text-info">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-warning">
                        <div class="inner"><h3>{{ pending_referred_in.length }}</h3><p>Pending Referred In </p></div>
                        <div class="icon"><i class="fa fas fa-indent text-warning"></i></div>
                        <a href="/laboratory/requests/referred_in" class="small-box-footer text-warning">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-warning">
                        <div class="inner"><h3>{{ pending_referred_out.length }}</h3><p>Pending Referred Out</p></div>
                        <div class="icon"><i class="fas fa-outdent text-warning"></i></div>
                        <a href="/laboratory/requests/referred_out" class="small-box-footer text-warning">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                            <LaboratoryDetailQueue source="laboratory" :requests="pending" />
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
                            <LaboratoryDetailQueue source="laboratory" :requests="emergency" />
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
            cancelled: [],
            completed: [],
            completed_referred_in: [],
            completed_referred_out: [],
            emergency: [],
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
            axios.get('/api/emr/laboratory/dashboard?page='+page)
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
    props: {}
}
</script>