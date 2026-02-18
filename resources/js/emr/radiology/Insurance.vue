<template>
    <section class="container-fluid overlay-wrapper p-0">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">HMO Desk</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height:600px;">
                        <!--InsuranceDetailTransactionList :transactions="requests.data"  /-->
                    </div>
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="transactions.per_page != null ? transactions.per_page : 52" :records="transactions.total != null ? transactions.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRInsuranceDetailTransactionList from '@/emr/insurance/details/TransactionList.vue'
export default {
    components:{EMRInsuranceDetailTransactionList},

    data() {
        return {
            current_page: 1,
            editMode: true,
            loading: false,
            request: {},
            transactions: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
            //this.applicant = {};
            //Fire.$emit('ApplicantDataFill', {});
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
            axios.get('/api/emr/radiology/insurance?page='+page)
            .then(response => {
                this.refreshRequests(response)
                //Fire.$emit('refreshAppointment', response);
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshRequests(response) {
            this.requests = response.data.requests;
        }
    },
    props: {}
}
</script>