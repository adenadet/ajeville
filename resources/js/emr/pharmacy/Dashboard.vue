<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>150</h3>
                        <p>Pending Prescriptions</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-pills"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>53</h3><p>Refills</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-sync-alt"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>44</h3><p>Ongoing</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-pause-circle"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info disabled">
                        <h3 class="card-title">Pending Prescriptions</h3>
                        <div class="card-tools">
                            <ul class="pagination pagination-sm float-right">
                                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height:400px;">
                        <EMRPharmacyDetailPrescriptionList :prescriptions.sync="pending_prescriptions.data" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Low Stock</h3>
                        <div class="card-tools">
                            <ul class="pagination pagination-sm float-right">
                                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height:400px;">
                        <EMRPharmacyDetailPrescriptionList :prescriptions.sync="pending_prescriptions.data" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRPharmacyDetailPrescriptionList from '@/emr/pharmacy/details/PrescriptionList.vue';
//import EMRPharmacyDetailPrescriptionList from '@/emr/pharmacy/details/PrescriptionList.vue';
export default {
    components:{
        EMRPharmacyDetailPrescriptionList,
    },
    data() {
        return {
            current_page: 1,
            loading: false,
            ongoing_prescriptions:{data:[], total: 0},
            pending_prescriptions: {data: [], total: 0},
            query: '',
            type: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(){
            this.loading = true;
            axios.get('/api/emr/pharmacy/prescriptions?page='+this.current_page+'&query='+this.query+'&status='+this.type)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Dashboard did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },

        refreshDashboard(response) {
            this.cancelled_requests = response.data.cancelled_requests;
            this.completed_requests = response.data.completed_requests;
            this.completed_referred_in = response.data.completed_referred_in;
            this.completed_referred_out = response.data.completed_referred_out;
            this.emergency_requests = response.data.emergency_requests;
            this.new_requests = response.data.new_requests;
            this.pending_referred_in = response.data.pending_referred_in;
            this.pending_referred_out = response.data.pending_referred_out;
            this.unpaid_requests = response.data.unpaid_requests;
        }
    },
    props: {}
}
</script>