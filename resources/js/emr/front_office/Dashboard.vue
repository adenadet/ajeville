<template>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner"><h3>{{ patients.length }}</h3><p>All Patients</p></div>
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner"><h3>53<sup style="font-size: 20px">%</sup></h3><p>Bounce Rate</p></div>
                    <div class="icon"><i class="fa fa-user-plus"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner"><h3>{{ temporary_patients.length }}</h3><p>Temporary Patient</p></div>
                    <div class="icon"><i class="fa fa-user-circle"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary ">
                    <div class="inner"><h3>{{ visits.total }}</h3><p>Active Patients</p></div>
                    <div class="icon"><i class="fa fa-user-tag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Active Visits</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                        <EMRFrontOfficeDetailVisitList :visits="visits.data" view="dashboard" />
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Booked Appointments</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                        <EMRFrontOfficeDetailAppointmentList :appointments="appointments.data" @refreshAppointmentList="getAllInitials()" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRFrontOfficeDetailAppointmentList from '@/emr/front_office/details/AppointmentList.vue';
import EMRFrontOfficeDetailVisitList from '@/emr/front_office/details/VisitList.vue';
export default {
    components:{
        EMRFrontOfficeDetailAppointmentList, EMRFrontOfficeDetailVisitList
    },
    data() {
        return {
            appointments: {data:[], },
            editMode: false,
            loading: true,
            patients: [],
            temporary_patients: [],
            visits: {data: [], }
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        enterAuthCode(){
            $('#authCodeModal').modal('show');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/dashboard').then(response =>{
                this.refresh(response);
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Dashboard was not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        inputAuthCode(){

        },
        inputAuthCodes(){
            if (this.selected_transactions.length == 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No items selected!',
                    footer: 'Please select at least one item!'
                });
            }
            else{
                this.transaction_list = this.selected_transactions;
                $('#authCodeModal').modal('show');
            }
        },
        refresh(response){
            this.appointments = response.data.appointments;
            this.patients = response.data.patients;
            this.temporary_patients = response.data.temporary_patients;
            this.visits = response.data.visits;
        },

    },
    props:{
        transactions: Array,
        view: String,
    },
}
</script>