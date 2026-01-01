<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>0</h3>
                        <p>My Queue</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Department Queue</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Doctors' Queue</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <EMRConsultantDetailQueue source="consultant" type="mine" view="dashboard" />
            </div>
            <div class="col-md-6">
                <EMRConsultantDetailQueue source="consultant" type="specialty" view="dashboard" />
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            drugs: [],
            editMode: true,
            form: new Form(),
            issuing_stores: [],
            loading: false,
            patient: {},
            prescriptionConfirmationData: new Form({
                patient_id: '',
                visit_id: '',
                drugs: [],
            }),
            queue_doctor: {},
            queue_mine: {},
            queue_specialty: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page = 1) {
            this.loading = true;
            axios.get('/api/emr/consultations/dashboard')
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        /*
        generateInvoice() {
            this.prescriptionConfirmationData.post()
                .then(() => { })
        },
        getPrice(i, k = 0) {
            let array = this.price_lists[k]['price_list_items'];
            let chosen_item = this.prescriptionConfirmationData.drugs[i].specific_drug_id;
            var available_price = array.find(item => item.item_id === chosen_item)
            this.prescriptionConfirmationData.drugs[i].unit_cost = available_price.price;
            this.prescriptionConfirmationData.drugs[i].coverage = ((available_price.covered == 'yes') && (available_price.coverage != null)) ? available_price.coverage : 0;
            this.prescriptionConfirmationData.drugs[i].payment_mode = k;
        },*/
        refreshDashboard(response) {
            this.queue_doctor = response.data.queue_doctor;
            this.queue_mine = response.data.queue_mine;
            this.queue_specialty = response.data.queue_specialty;
        },
    },
    props: {}
}
</script>