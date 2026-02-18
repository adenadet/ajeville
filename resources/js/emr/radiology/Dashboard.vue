<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ new_requests }}</h3>
                            <p>New Requests</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-x-ray"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ unpaid_requests }}</h3>
                            <p>Unpaid Requests</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-money-bill"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ pending_referred_in }}</h3>
                            <p>Pending Referred In </p>
                        </div>
                        <div class="icon">
                            <i class="fa fas fa-indent"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ pending_referred_out }}</h3>
                            <p>Pending Referred Out</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-outdent"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ cancelled_requests }}</h3>
                            <p>Cancelled Requests</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-times"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ completed_requests }}</h3>
                            <p>Completed Requests</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-file"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ completed_referred_in }}</h3>
                            <p>Completed Referred In </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-indent"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ completed_referred_out }}</h3>
                            <p>Completed Referred Out</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-outdent"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Requests</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <EMRRadiologyDetailRequestList :requests="requests.data" />
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h3 class="card-title">Emergency Requests</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <EMRRadiologyDetailRequestList :requests="emergency_requests" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRRadiologyDetailRequestList from '@/emr/radiology/details/RequestList.vue'
export default {
    components:{EMRRadiologyDetailRequestList},
    data() {
        return {
            cancelled_requests: 0,
            completed_requests: 0,
            completed_referred_in: 0,
            completed_referred_out: 0,
            editMode: true,
            emergency_requests: [],
            new_requests: 0,
            pending_referred_in: 0,
            pending_referred_out: 0,
            request: {},
            requests: {data:[], total:0},
            transaction: {},
            transactions: {},
            unpaid_requests: 0,
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
            axios.get('/api/emr/radiology/dashboard?page='+page)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        makePayment(appointment){
            //this.$Progress.start();
            this.paySpecific = true;
            //Fire.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            //this.$Progress.finish();
        },
        refreshDashboard(response) {
            this.cancelled_requests = response.data.cancelled_requests;
            this.completed_requests = response.data.completed_requests;
            this.completed_referred_in = response.data.completed_referred_in;
            this.completed_referred_out = response.data.completed_referred_out;
            this.new_requests = response.data.new_requests;
            this.requests = response.data.requests;
            this.pending_referred_in = response.data.pending_referred_in;
            this.pending_referred_out = response.data.pending_referred_out;
            this.unpaid_requests = response.data.unpaid_requests;
        }
    },
    props: {}
}
</script>